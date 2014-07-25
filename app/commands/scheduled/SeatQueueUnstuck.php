<?php

namespace Seat\Commands\Scheduled;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Seat\EveApi;
use Seat\EveApi\Account;

class SeatQueueUnstuck extends ScheduledCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seatscheduled:queue-unstuck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove stuck Jobs from the SeAT Queue.';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        \Log::info('Started command ' . $this->name, array('src' => __CLASS__));
        
        /*
         * Check for stuck queued and woking jobs
         */
        
        $db_queue_count = \SeatQueueInformation::where('status', '=', 'Queued')->count();
        $db_working_count = \SeatQueueInformation::where('status', '=', 'Working')->count();
        
        if($db_queue_count!=0)
        {
            $db_queue = \SeatQueueInformation::where('status', '=', 'Queued')->get();
            
            foreach ($db_queue as $job) {
                $timeDelta = \Carbon\Carbon::now()->diffInMinutes($job['updated_at']);
                if($timeDelta >= 60)
                {
                    \SeatQueueInformation::where('jobID', '=', $job['jobID'])->update(array('status' => 'Error'));
                }
            }
        }

        if($db_working_count!=0)
        {
            $db_working = \SeatQueueInformation::where('status', '=', 'Working')->get();
            
            foreach ($db_working as $job) {
                $timeDelta = \Carbon\Carbon::now()->diffInMinutes($job['updated_at']);
                if($timeDelta >= 60)
                {
                    \SeatQueueInformation::where('jobID', '=', $job['jobID'])->update(array('status' => 'Error'));
                }
            }
        }
    }
}
