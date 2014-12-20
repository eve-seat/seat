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

namespace Seat\Commands\Scheduled;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Seat\EveApi;
use Seat\EveApi\Account;
use App\Services\Settings\SettingHelper as Settings;

class EveCorporationAssetsUpdater extends ScheduledCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seatscheduled:api-update-corporation-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Corporation Assets.';

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
     * When a command should run
     *
     * @param Scheduler $scheduler
     * @return \Indatus\Dispatcher\Schedulable
     */
    public function schedule(Schedulable $scheduler)
    {
        return $scheduler->setSchedule('0', '*/8', '*', '*', '*');
    }

    /**
     * Is this command enbaled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Settings::getSetting('seatscheduled_corporation_assets') == 'true' ? true : false ;
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

                //TODO: Log this key's problems and disable it
                continue;
            }

            // Only process Corporation keys
            if ($access['type'] == 'Corporation')

                // Call the addToQueue helper to add a new update job. We
                // call the partial call here because some calls for
                // the corporation apis have bery long cache timers
                // and there is no use in doing all that work
                // for cached data. This one calls the
                // Assets updater
                \App\Services\Queue\QueueHelper::addToQueue('\Seat\EveQueues\Partial\CorporationAssets', $key->keyID, $key->vCode, 'CorporationAssets', 'Eve');
        }
    }
}
