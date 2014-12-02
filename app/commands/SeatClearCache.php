<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatClearCache extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:clear-cache';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clears all the api caches. This includes Redis, the DB caching and the Pheal disk-cache';

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
		$success = \File::deleteDirectory(storage_path(). '/cache/phealcache', true);

		if(!$success)
		{
			$this->error('Warning: Failed to delete pheal-ng disk cache!');
		}
		else
		{
			$this->info('Pheal-ng disk cache cleared!');
		}

		$redis = new \Predis\Client(array('host' => \Config::get('database.redis.default.host'), 'port' => \Config::get('database.redis.default.port')));
		$redis->flushall();

		$this->info('Redis cache cleared!');

		\DB::table('cached_until')->truncate();

		$this->info('DB cache cleared!');
	
	}
}
