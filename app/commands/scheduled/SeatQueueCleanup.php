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

use App\Services\Settings\SettingHelper as Settings;

class SeatQueueCleanup extends ScheduledCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seatscheduled:queue-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove jobs that have possibly \'run-away\' and or are past the 60minute grace run time';

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
        return $scheduler->setSchedule('*/30', '*', '*', '*', '*');
    }

    /**
     * Is this command enbaled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Settings::getSetting('seatscheduled_queue_cleanup') == 'true' ? true : false ;
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

        // Grab the 'Queued' or 'Working' jobs that we have record
        // of so that we can process a time related check on
        // them
        foreach(\SeatQueueInformation::where('status', 'Queued')->orWhere('status', 'Working')->get() as $job) {

            // We want to preserve the job status that we had for
            // some debugging reasons, so lets set the new
            // message added to it.
            $new_output = '60 Minute Job Runtime Limit Exceeded. The last status was: ' .
                $job->output;

            // If the Job has been in the queue for more than 1 hour,
            // move it over to a Error status.
            if(\Carbon\Carbon::now()->diffInMinutes($job['updated_at']) > 60)
                \SeatQueueInformation::where('jobID', '=', $job['jobID'])
                    ->update(
                        array(
                            'status' => 'Error',
                            'output' => $new_output
                            )
                        );
        }
    }

}
