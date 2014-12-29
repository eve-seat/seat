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

class SeatUpdate extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates SeAT.';

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
     * Check Github for release information
     *
     * @return array
     */
    public function checkVersion()
    {

        // Prepare a return array
        $results = array(
            'release_data' => null,
            'versions_behind' => 0
        );

        // Get the current version
        $current_version = \Config::get('seat.version');

        try {

            // Check the releases from Github for eve-seat/seat
            $headers = array('Accept' => 'application/json');
            $request = \Requests::get('https://api.github.com/repos/eve-seat/seat/releases', $headers);

            if ($request->status_code == 200) {

                $results['release_data'] = json_decode($request->body);

                // Try and determine if we are up to date
                if ($results['release_data'][0]->tag_name == 'v'.$current_version) {

                } else {

                    foreach ($results['release_data'] as $release) {

                        if ($release->tag_name == 'v'.$current_version)
                            break;
                        else
                            $results['versions_behind']++;
                    }
                }
            }

        } catch (Exception $e) {

            $this->error('[!] Error: Failed to retrieve version information.');
            $this->error('[!] ' . $e->getMessage());

        }

        return $results;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        // Prepare some welcome information
        $this->info('[+] Welcome to the SeAT v' . \Config::get('seat.version') . ' updater!');
        $this->line('');
        $this->comment('[+] Warning: Please ensure that you read any potential upgrade specifics prior to upgrading your installation.');
        $this->comment('[+] Upgrade guides can be found here: https://github.com/eve-seat/seat/tree/master/docs/upgrade_specifics');
        $this->line('');
        $this->comment('[+] Note: Any local changes to SeAT will be overriden with this upgrade. Ensure you have the required backups first.');
        $this->line('');
        $this->line('[+] Checking if we have everything required to attempt an update...');

        // Lets check if we have everything that we need to update,
        //and that we are infact out of date

        // Start with the git command
        $git_command = exec('which git');
        if (strlen($git_command) <= 0 || !\File::exists($git_command)) {

            $this->error('[!] Error: `git` command not found. Please ensure that you have Git installed and in your $PATH');
            return;
        }
        $this->info('[+] `git` found at `' . $git_command . '`');

        // Move on to composer. If we did not get the path
        // to where composer is installed, attempt to
        // find it. We will first check $PATH, then
        // the ./ and finally error out
        $composer_command = $this->option('composer');
        if (!is_null($composer_command)) {

            if(!\File::exists($composer_command)) {

                $this->error('[!] Error: Composer was not found at `' . $composer_command . '`. Please specify a valid path to `composer.phar`.');
                return;
            }

        } else {

            // So we did not get the path to composer.phar, lets
            // try and find it.
            $composer_command = exec('which composer');
            if(strlen($composer_command) <= 0 || !\File::exists($composer_command)) {

                $composer_command = exec('which composer.phar');
                if(strlen($composer_command) <= 0 || !\File::exists($composer_command)) {

                    $composer_command = base_path() . 'composer';
                    if(strlen($composer_command) <= 0 || !\File::exists($composer_command)) {

                        $composer_command = base_path() . 'composer.phar';
                        if(strlen($composer_command) <= 0 || !\File::exists($composer_command)) {

                            $this->error('[!] Error: Unable to find `composer.phar`. Please specify a valid path to `composer.phar` as a command argument.');
                            return;
                        }
                    }
                }
            }
        }

        $this->info('[+] `composer.phar` found at `' . $composer_command . '`');

        // Next ensure that the path where SeAT is installed is
        // writable to us now
        if(!\File::isWritable(base_path())) {

            $this->error('[!] Error: ' . base_path() . ' is not writable to the current user that is running this command.');
            return;
        }
        $this->info('[+] ' . base_path() . ' is writable for the upgrade.');
        $this->line('[+] Checking version information against the Github repository...');

        // Check the version information
        $version_information = $this->checkVersion();

        // If we are on the latest, simply exit
        if ($version_information['versions_behind'] == 0) {

            $this->comment('[+] You are running SeAT v' . \Config::get('seat.version') . ' which is the latest.');
            return;
        }

        // Ask the user if they want to upgrade.
        if(!$this->confirm('[?] Are you sure you want to upgrade from SeAT v' . \Config::get('seat.version') . ' to ' . $version_information['release_data'][0]->tag_name . '?', true)) {

            $this->error('[+] Error: User cancelled upgrade.');
            return;
        }

        // We have everything we need to continue the
        // upgrade. Bring the application down so
        // that we can continue.
        $this->call('down');
        if (!\App::isDownForMaintenance()) {

            $this->error('[!] Error: Dropping SeAT into maintenance mode failed.');
            return;
        }

        // Move on to calling git to pull down the latest
        // code from eve-seat/seat.

        // git fetch -f
        $this->line('[+] Running: `' . $git_command . ' fetch -f`');
        exec($git_command . ' fetch -f', $output, $exit_code);

        // If the command failed, we should have a $exit_code of
        // not 0. If thats the case, read $output and print
        // that as debugging information
        if ($exit_code !== 0) {

            $this->error('[!] Error: git fetch failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));
            return;
        }

        // git pull -f
        $this->line('[+] Running: `' . $git_command . ' pull -f`');
        exec($git_command . ' pull -f', $output, $exit_code);

        // If the command failed, we should have a $exit_code of
        // not 0. If thats the case, read $output and print
        // that as debugging information
        if ($exit_code !== 0) {

            $this->error('[!] Error: git pull failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));
            return;
        }

        // git checkout -f master if we did not get the --dev option
        if(!$this->option('dev')) {

            $this->line('[+] Running: `' . $git_command . ' checkout -f master`');
            exec($git_command . ' checkout -f master', $output, $exit_code);

            // If the command failed, we should have a $exit_code of
            // not 0. If thats the case, read $output and print
            // that as debugging information
            if ($exit_code !== 0) {

                $this->error('[!] Error: git checkout -f master failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));
                return;
            }
        }

        // composer self-update
        $this->line('[+] Running: `' . $composer_command . ' self-update`');
        exec($composer_command . ' self-update', $output, $exit_code);

        // If the command failed, we should have a $exit_code of
        // not 0. If thats the case, read $output and print
        // that as debugging information
        if ($exit_code !== 0) {

            $this->error('[!] Error: composer self-update failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));
            return;
        }

        // composer update
        $this->line('[+] Running: `' . $composer_command . ' update`');
        exec($composer_command . ' update', $output, $exit_code);

        // If the command failed, we should have a $exit_code of
        // not 0. If thats the case, read $output and print
        // that as debugging information
        if ($exit_code !== 0) {

            $this->error('[!] Error: composer update failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));
            return;
        }

        // composer dump-autoload
        $this->line('[+] Running: `' . $composer_command . ' dump-autoload`');
        exec($composer_command . ' dump-autoload', $output, $exit_code);

        // If the command failed, we should have a $exit_code of
        // not 0. If thats the case, read $output and print
        // that as debugging information
        if ($exit_code !== 0) {

            $this->error('[!] Error: composer dump-autoload failed with exit code ' . $exit_code . ' and command outut: ' . implode('\n', $output));
            return;
        }

        // Database migrations
        $this->line('[+] Running database migrations.');
        $this->call('migrate');

        // SDE Updates
        if(!$this->option('no-sde')) {

            $this->line('[+] Running EVE SDE Updates');
            $this->call('seat:update-sde', array('--confirm' => null));
        }

        // With everything done, bring the application back up
        $this->call('up');

        $this->line('');
        $this->info('[+] Upgrade done! It is recommended that you have a look at the `laravel.log` file for any potential errors.');
        $this->info('[+] You can view the log file with `php artisan tail`, or responding with [y] to the next question.');

        // Ask the user if they want to upgrade.
        if($this->confirm('[?] Do you want to tail the log file now?', true)) {

            $this->call('tail');
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
            array('composer', null, InputOption::VALUE_OPTIONAL, 'Specify the path to composer.phar.', null),
            array('no-sde', null, InputOption::VALUE_NONE, 'Skip updating the EVE SDE.', null),
            array('dev', null, InputOption::VALUE_NONE, 'Skip moving to the master branch.', null),
        );
    }

}
