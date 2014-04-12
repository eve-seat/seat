<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatAPIFindKeyByName extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:find-key-by-name';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Search for a API key attached to a character name.';

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
		$name = $this->argument('name');
		$this->info('Searching for term: ' . $name);

		$characters = \EveAccountAPIKeyInfoCharacters::where('characterName', 'like', '%' . $name . '%')->get();

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
			array('name', InputArgument::REQUIRED, 'The name of the character to search the key for.'),
		);
	}
}
