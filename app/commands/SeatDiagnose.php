<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class SeatDiagnose extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:diagnose';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Runs Diagnostic checks to aid in debugging.';

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
		$this->info('Running SeAT ' . \Config::get('seat.version') . ' Diagnostics');
		$this->line('');

		// SeAT & Laravel
		$this->info('SeAT configuration:');
		if (\Config::get('app.debug'))
			$this->comment('[warning] Debug Mode On: Yes. It is reccomended that you set this to false in app/config/app.php');
		else
			$this->line('[ok] Debug Mode On: No');
		$this->line('Url: ' . \Config::get('app.url'));
		$this->line('Failed API limit: ' . \Config::get('seat.ban_limit'));
		$this->line('Ban count time: ' . \Config::get('seat.ban_grace') . ' minutes');
		$this->line('');

		// Logs
		$this->info('Logging:');
		if (is_writable(storage_path() . '/logs/laravel.log'))
			$this->line('[ok] ' . storage_path() . '/logs/laravel.log is writable.');
		else
			$this->error('[error] ' . storage_path() . '/logs/laravel.log is not writable.');
		$this->line('');

		// Database
		$this->info('Database configuration:');
		$this->line('Database driver: ' . \Config::get('database.default'));
		$this->line('MySQL Host: ' . \Config::get('database.connections.mysql.host'));
		$this->line('MySQL Database: ' . \Config::get('database.connections.mysql.database'));
		$this->line('MySQL Username: ' . \Config::get('database.connections.mysql.username'));
		$this->line('MySQL Password: ' . str_repeat('*', strlen(\Config::get('database.connections.mysql.password'))));
		$this->line('');

		// Test Database conenctions
		$this->info('Database connection test...');
		try {

			$this->line('[ok] Successfully connected to database `' . \DB::connection()->getDatabaseName() . '` (did not test schema)');

		} catch (\Exception $e) {
		
			$this->error('[error] Unable to obtain a MySQL connection. The error was: ' . $e->getCode() . ': ' . $e->getMessage());	
		}
		$this->line('');

		// Redis
		$this->info('Redis configuration:');
		$this->line('Redis Host: ' . \Config::get('database.redis.default.host'));
		$this->line('Redis Port: ' . \Config::get('database.redis.default.port'));
		$this->line('');

		// Testing Redis
		$this->info('Redis connection test...');
		$key_test = str_random(40);
		try {

			$redis = new \Predis\Client(array('host' => \Config::get('database.redis.default.host'), 'port' => \Config::get('database.redis.default.port')));
			$redis->set($key_test, \Carbon\Carbon::now());
			$redis->expire($key_test, 10);

			$this->line('[ok] Successfully set the key: ' . $key_test . ' and set it to expire in 10 seconds');

			$value_test = $redis->get($key_test);
			$this->line('[ok] Successfully retreived key: ' . $key_test . ' which has value: ' . $value_test);
		} catch (\Exception $e) {

			$this->error('[error] Redis test failed. The last error was: ' . $e->getCode() . ': ' . $e->getMessage());
			
		}
		$this->line('');

		// Testing Pheal
		$this->info('EVE API call test with phealng...');

		// Prepare pheal
		BaseApi::bootstrap();
		$pheal = new Pheal();

		try {
			
			$server_status = $pheal->serverScope->ServerStatus();
			$this->line('[ok] Testing the ServerStatus API call returned a response reporting ' . $server_status->onlinePlayers . ' online players, with the result cache expiring ' . \Carbon\Carbon::parse($server_status->cached_until)->diffForHumans());

		} catch (\Exception $e) {

			$this->error('[error] API Call test failed. The last error was: ' . $e->getCode() . ': ' . $e->getMessage());
		}
		$this->line('');

	}
}
