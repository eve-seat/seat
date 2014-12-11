<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatUpdateSDE extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:update-sde';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the SeAT database to the latest tested EVE Static Data Export.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        // Start by warning the user about the command that will be run
        $this->comment('Warning! This Laravel command uses exec() to execute a mysql shell command to import a extracted dump.');
        $this->comment('Due to the way the command is constructed, should someone view the current running processes of your server, they will be able to see your SeAT database users password.');
        $this->line('');
        $this->line('Ensure that you understand this before continuing.');

        // Test that we have valid Database details. An exception
        // will be thrown if this fails.
        \DB::connection()->getDatabaseName();

        if ($this->option('confirm') || $this->confirm('Are you sure you want to update to the latest EVE SDE? [yes|no]', true)) {

            $this->info('Checking for SDE updates at https://raw.githubusercontent.com/eve-seat/seat/resources/sde_version.json ...');

            // Check the current SDE version from Github in the eve-seat/seat/resources
            // repository. First though, we setup some default headers and options
            // that will be used in Requests in this script
            $headers = array('Accept' => 'application/json');
            $options = array(
                'timeout'   => 120,
                'useragent' => 'SeAT-Updater/' . \Config::get('seat.version')
            );

            // Now, attempt the actual request.
            $request = \Requests::get(
                'https://raw.githubusercontent.com/eve-seat/seat/resources/sde_version.json',
                $headers,
                $options
            );

            // If the request failed, return as there is nothing we can do about that
            if (!$request->success) {

                $this->error('Warning: Failed to retreive the latest SDE information from the eve-seat/resources branch.');
                return;
            }

            // Read the response JSON
            $sde_data = json_decode($request->body);

            // If it was not possible to parse the JSON, return as there is nothing
            // we can go any further
            if(is_null($sde_data)) {

                $this->error('Warning: Failed to parse the response JSON.');
                return;
            }

            // Show some debugging information about what we got from the resources branch
            $this->line('The current SDE version is ' . $sde_data->version);
            $this->line(count($sde_data->tables) . ' dumps will be downloaded from ' . $sde_data->url . ' in ' .
                $sde_data->format . ' format and imported into mysql://' . \Config::get('database.connections.mysql.host') .
                '/' . \Config::get('database.connections.mysql.database'));

            // Prepare a final confirmation before work comences for the update
            if ($this->option('confirm') || $this->confirm('Does the above look OK? [yes|no]', true)) {

                // Prepare the filesystem for the dumps
                $storage = storage_path() . '/sde/' . $sde_data->version . '/';
                $this->line('Preparing ' . $storage . ' to store the dumps.');

                // Check that we are able to write to the storage_path()
                if (\File::isWritable(storage_path())) {

                    // Check that the path exists
                    if(!\File::exists($storage))
                        \File::makeDirectory($storage, 0755, true);

                    // With the disk storage ok, we continue to download the
                    // archives. To try and save some badnwidth, we will
                    // check that the archive does not already exist.
                    $this->line('Downloading SDE data...');

                    // Loop over the tables as defined in the JSON
                    foreach ($sde_data->tables as $table) {

                        $full_path = $storage . $table . $sde_data->format;
                        $full_url = $sde_data->url . $table . $sde_data->format;

                        // Check if we don't already have this file. If it does
                        // exist, check that it is larger than 0 bytes.
                        if(\File::exists($full_path))
                            if (\File::size($full_path) > 0)
                                continue;

                        // Attempt to download the file
                        $file = \Requests::get($full_url, $options, $options);
                        if ($file->success) {

                            // Save the dump to disk
                            \File::put($full_path, $file->body);

                            $this->info('[OK] ' . $full_url);

                        } else {

                            $this->error('Failed to download ' . $full_url . '. The HTTP error was ' . $file->status_code);
                        }

                    }

                    // Next, we will read the bz2 archives, and attempt to run the
                    // SQL queries in them.

                    // Unfortunately, at least until I learn a better way, I seems like
                    // running the imports with a exec() is the only way I can get
                    // this to work, for now. =(
                    $this->line('Importing the SQL...');
                    foreach ($sde_data->tables as $table) {

                        // Prepare the full path and check its validity
                        $full_path = $storage . $table . $sde_data->format;
                        if(!\File::exists($full_path) || !\File::size($full_path) > 0) {

                            $this->error('Warning: ' . $full_path . ' does not appear to be valid. Maybe the download failed? Skipping import for this file.');
                            continue;
                        }

                        // Now that we know that the source .bz2 exists, check if
                        // the archive is possibly already extracted. Of so,
                        // skip the extraction process completely.
                        $full_extracted_path = $storage . $table . '.sql';

                        if(!\File::exists($full_extracted_path) || !\File::size($full_extracted_path) > 0) {

                            // Get 2 handles ready for both the in and out files
                            $input_file = bzopen($full_path, 'r');
                            $output_file = fopen($full_extracted_path, 'w');

                            // Write the $output_file in chunks
                            while ($chunk = bzread ($input_file, 4096))
                                fwrite ($output_file, $chunk, 4096);

                            // Close the input file
                            bzclose($input_file);
                            fclose($output_file);
                        }

                        // With the output file ready, prepare the scary exec() command
                        // that should be run. A sample $import_command is:
                        // mysql -u root -h 127.0.0.1 seat < /tmp/sample.sql
                        $import_command = 'mysql -u ' . \Config::get('database.connections.mysql.username') .
                            // Check if the password is longer than 0. If not, dont specify the -p flag
                            (strlen(\Config::get('database.connections.mysql.password')) ? ' -p' : '' )
                                // Append this regardless
                                . \Config::get('database.connections.mysql.password') .
                            ' -h ' . \Config::get('database.connections.mysql.host') .
                            ' ' .\Config::get('database.connections.mysql.database') .
                            ' < ' . $full_extracted_path;

                        // Run the command... (*scared_face*)
                        exec($import_command, $output, $exit_code);

                        // If the command failed, we should have a $exit_code of
                        // not 0. If thats the case, read $output and print
                        // that as debugging information
                        if ($exit_code !== 0)
                            $this->error('Warning: Import failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));

                        // Write the OK
                        $this->info('[OK] ' . $table . $sde_data->format .
                            ' [' . number_format(\File::size($full_path)/ 1048576, 2) . 'MB]');
                    }

                    $this->line('SDE update to ' . $sde_data->version . ' completed successfully.');

                } else {

                    $this->error('Warning: Unable to write to the storage path ' . storage_path() . ' to store the dumps.');
                }

            } else {

                $this->comment('Warning: SDE Update aborted');
            }
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {

        return array(

            // Allow for the command to be confirmed inline
            array('confirm', null, InputOption::VALUE_NONE, 'Confirm the update should be run.', null),
        );
    }
}
