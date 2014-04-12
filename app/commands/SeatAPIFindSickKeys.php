<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatAPIFindSickKeys extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:find-sick-keys';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Show keys which are disabled due to API Authentication errors.';

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

		$this->info('Finding keys that are not ok.');
		foreach (\SeatKey::where('isOk', '=', 0)->get() as $key) {

			$this->comment('Key ' . $key->keyID . ' is not ok.');
			$this->line('Characters on this key:');

			foreach (\EveAccountAPIKeyInfoCharacters::where('keyID', '=', $key->keyID)->get() as $character) {
				$this->line($characterName);
			}
		}
	}
}
