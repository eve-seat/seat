<?php

namespace Seat\Commands\Scheduled;

use Indatus\Dispatcher\ScheduledCommand;
use Indatus\Dispatcher\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Seat\EveApi;
use Seat\EveApi\Account;

class EveMapUpdater extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seatscheduled:api-update-map';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update Eve Map Related Information.';

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
		return $scheduler->daily();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		
		\Log::info('Started command ' . $this->name, array('src' => __CLASS__));

		$jobID = \Queue::push('Seat\EveQueues\Full\Map', array());
		\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => 0, 'api' => 'Map', 'scope' => 'Eve', 'status' => 'Queued'));
	}
}
