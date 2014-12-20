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

class SeatAPIDeleteKey extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:delete-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a API key.';

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

        // Get the API key in question from the command
        // line arg
        $keyID = $this->argument('keyID');
        $this->info('Searching for key: ' . $keyID);

        // Find the API key in the datbase
        $key = \SeatKey::where('keyID', $keyID)->first();

        // If we found the key, move on to asking the user
        // if they really want to delete the key.
        if (count($key) == 1) {

            if ($this->confirm('Are you sure you want to delete this key? [yes|no] ', true)) {

                // Delete the API key from thet SeAT database
                $key = \SeatKey::where('keyID', $keyID)->delete();

                // Notify the user that they key is gone.
                $this->comment('Key deleted!');

                // We are done and can end now
                return;

            } else {

                $this->info('Not doing anything');
            }

        } else {

            $this->error('API Key not found.');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {

        return array(

            array('keyID', InputArgument::REQUIRED, 'The keyID to delete.'),
        );
    }
}
