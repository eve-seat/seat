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

use Seat\EveApi;
use Seat\EveApi\Account;

class SeatAPIUpdate extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:api-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the API Update Job scheduler.';

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

        // Log what we are going to do in the laravel.log file
        \Log::info('Started command ' . $this->name, array('src' => __CLASS__));

        // Call the addToQueue helper to queue jobs for
        // the EVE Server, Map and General EVE info
        \App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Server'), 0, NULL, 'ServerStatus', 'Server');
        \App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Map'), 0, NULL, 'Map', 'Eve');
        \App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Eve'), 0, NULL, 'Eve', 'Eve');

        // Log the start of the key processing
        \Log::info('Starting job submissions for all keys', array('src' => __CLASS__));

        // Get the keys that are not disabled and process them.
        foreach (\SeatKey::where('isOk', '=', 1)->get() as $key) {

            // It is important to know the type of key we are working
            // with so that we may know which API calls will
            // apply to it. For that reason, we run the
            // Seat\EveApi\BaseApi\determineAccess()
            // function to get this.
            $access = EveApi\BaseApi::determineAccess($key->keyID);

            // If we failed to determine the access type of the
            // key, continue to the next key.
            if (!isset($access['type'])) {

                \Log::error('Unable to determine type for key ' . $key->keyID, array('src' => __CLASS__));
                continue;
            }

            // Based on the type of key that we have determined it
            // to be, we will call the appropriate addToQueue
            // helper to schedule update jobs for
            switch ($access['type']) {
                case 'Character':

                    // Do a fresh AccountStatus lookup
                    Account\AccountStatus::update($key->keyID, $key->vCode);
                    \App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Character'), $key->keyID, $key->vCode, 'Character', 'Eve');
                    break;

                case 'Corporation':
                    \App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Corporation'), $key->keyID, $key->vCode, 'Corporation', 'Eve');
                    break;

                default:
                    break;
            }
        }
    }
}
