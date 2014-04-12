<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Carbon\Carbon;

class SeatQueueStatus extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:queue-status';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Query and return information about the current queues.';

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

		// Connect to the Redis backend
		$redis = new \Predis\Client(array('host' => \Config::get('database.redis.default.host'), 'port' => \Config::get('database.redis.default.port')));
		$redis_count = count($redis->lrange('queues:default', 0, -1));
		$this->line('Redis reports ' . $redis_count . ' jobs in the queue (queues:default)');

		// Get the Queue information from the database
		$db_info = \SeatQueueInformation::where('status', '=', 'Queued')->count();
		$this->line('The database reports ' . $db_info . ' queued jobs');
		if ($db_info <> $redis_count)
			$this->info('[!] The redis & db queued counts are not the same. This is not always a bad thing');

		$db_info = \SeatQueueInformation::where('status', '=', 'Done')->count();
		$this->line('The database reports ' . $db_info . ' done jobs');

		$db_info = \SeatQueueInformation::where('status', '=', 'Error')->count();
		if ($db_info > 0) {

			$this->comment('Current error-state jobs (' . $db_info . '):');

			foreach (\SeatQueueInformation::where('status', '=', 'Error')->get() as $row) {
			
				$this->line(
					'OwnerID: ' . $row->ownerID . ' | Scope: ' . $row->scope .
				 	' | API: ' . $row->api . ' | Status: "' . $row->output .
				 	'" | Created: ' . Carbon::parse($row->created_at)->diffForHumans() .
				 	' | Last updated: ' . Carbon::parse($row->updated_at)->diffForHumans()
				);
			}			
		} else {
			$this->line('The database reports ' . $db_info . ' error jobs');
		}

		$db_info = \SeatQueueInformation::where('status', '=', 'Working')->count();
		if ($db_info > 0) {

			$this->comment('Current working-state jobs (' . $db_info . '):');

			foreach (\SeatQueueInformation::where('status', '=', 'Working')->get() as $row) {
			
				$this->line(
					'OwnerID: ' . $row->ownerID . ' | Scope: ' . $row->scope .
				 	' | API: ' . $row->api . ' | Status: "' . $row->output .
				 	'" | Created: ' . Carbon::parse($row->created_at)->diffForHumans() .
				 	' | Last updated: ' . Carbon::parse($row->updated_at)->diffForHumans()
				);
			}
		} else {

			$this->line('The database reports ' . $db_info . ' working jobs');
		}
	}
}
