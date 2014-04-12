<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatAPIDeleteKey extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:delete-key';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete a API key.';

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
		$keyID = $this->argument('keyID');
		$this->info('Searching for key: ' . $keyID);

		$key = \SeatKey::where('keyID', '=', $keyID)->first();

		if (count($key) == 1) {

			if ($this->confirm('Are you sure you want to delete this key? [yes|no] ', true)) {

				$key = \SeatKey::where('keyID', '=', $keyID)->delete();
				$this->comment('Key deleted!');
				return;
			} else {

				$this->info('Not doing antything');
			}
		} else {

			$this->error('Key not found.');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('keyID', InputArgument::REQUIRED, 'The keyID to delete.'),
		);
	}
}
