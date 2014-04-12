<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatAPIFindNameByKey extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:find-name-by-key';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Return known characters on a key.';

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

		$characters = \EveAccountAPIKeyInfoCharacters::where('keyID', 'like', '%' . $keyID . '%')->get();

		if (count($characters) > 0) {

			foreach ($characters as $character) {
				$this->info('Found match: ' . $character->characterName . ' with keyID: ' . $character->keyID);
			}
		} else {

			$this->error('No matches found.');
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
			array('keyID', InputArgument::REQUIRED, 'The keyID to search for.'),
		);
	}

}
