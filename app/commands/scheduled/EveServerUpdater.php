<?php

namespace Seat\Commands\Scheduled;

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Seat\EveApi;
use Seat\EveApi\Account;

class EveServerUpdater extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seatscheduled:api-update-server';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates Eve Server Related Information.';

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
		return $scheduler->setSchedule('*/5', '*', '*', '*', '*');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		\Log::info('Started command ' . $this->name, array('src' => __CLASS__));
		
		// Server APIs
		\App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Server'), '0', NULL, 'ServerStatus', 'Server');
	}
}
