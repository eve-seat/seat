<?php

namespace Seat\Commands\Scheduled;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatQueueCleanup extends ScheduledCommand {

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        \Log::info('Started command ' . $this->name, array('src' => __CLASS__));

        // TODO: Query only Jobs with 'update_at' > 1h to optimize
        foreach(\SeatQueueInformation::where('status', '=', 'Queued')
          ->orWhere('status','=', 'Working')
          ->get() as $job
        ) {
            if(\Carbon\Carbon::now()->diffInMinutes0($job['updated_at']) > 60)
            {
                \SeatQueueInformation::where('jobID', '=', $job['jobID'])
                    ->update(
                        array(
                            'status' => 'Error',
                            'output' => '60 Minute Job Runtime Limit Exceeded'
                            )
                        );
            }
        }
    }
}
