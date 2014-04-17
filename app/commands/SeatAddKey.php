<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Pheal\Pheal;
use Seat\EveApi;
use Seat\EveApi\BaseApi;
use Seat\EveApi\Account;

class SeatAddKey extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:add-key';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Adds a API Key to SeAT.';

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
		$vCode = $this->argument('vCode');

		EveApi\BaseApi::validateKeyPair($keyID, $vCode);

		$this->info('The key pair appear to be valid.');
		$this->line('Checking if key exists in the database...');

		if (!\SeatKey::where('keyID', $keyID)->first()) {

			$this->info('keyID is not present in the database. Making the APIKeyInfo API call...');

			// Setup a pheal instance and get some API data :D
			BaseApi::bootstrap();
			$pheal = new Pheal($keyID, $vCode);

			// Get API Key Information
			try {
				$key_info = $pheal->accountScope->APIKeyInfo();

			} catch (\Pheal\Exceptions\PhealException $e) {

				$this->error('Unable to retreive key information. The erorr was: ' . $e->getCode() . ': ' . $e->getMessage());
				return;
			}

			// Print some information about the key
			$this->line('Key Type: ' . $key_info->key->type);
			$this->line('Key Access Mask: ' . $key_info->key->accessMask);
			$this->info('Characters on key: ');
			foreach ($key_info->key->characters as $character)
				$this->line('	- ' . $character->characterName . ' in corporation ' . $character->corporationName);

			// Confirm adding the key
			if ($this->confirm('Do you wish to add this key to the database? [yes|no]')) {
			    
			    $key_info = new \SeatKey;

			    $key_info->keyID = $keyID;
				$key_info->vCode = $vCode;
				$key_info->isOk = 1;
				$key_info->lastError = null;
				$key_info->deleted_at = null;
				$key_info->user_id = 1; // TODO: Fix this when the proper user management occurs
				$key_info->save();

				$this->info('Successfully saved the API key to the database.');
			}


		} else {

			$this->error('keyID ' . $keyID . ' already exists in the database.');
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
			array('keyID', InputArgument::REQUIRED, 'The keyID.'),
			array('vCode', InputArgument::REQUIRED, 'The vCode.'),
		);
	}

}
