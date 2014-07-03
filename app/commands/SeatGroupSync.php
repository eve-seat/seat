<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Sentry;

class SeatGroupSync extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:groupsync';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Ensures the application has all the SeAT access groups present.';

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
		// We will search for groups and create them if they are not present.
		// Lets create an array that we can easily append to in case we need
		// more groups
		$groups = array(
			// This group is for POS Managers to allow access to the POS
			// Management View
			array(
				'name' => 'POS Managers',
				'permissions' => array(
					'pos_manager' => 1
				)
			),

			// Wallet Manager group is to allow access to corporation wallets
			// and Ledgers
			array(
				'name' => 'Wallet Managers',
				'permissions' => array(
					'wallet_manager' => 1
				)
			),

			// Recruiters group allows access to all character sheets and keys
			array(
				'name' => 'Recruiters',
				'permissions' => array(
					'recruiter' => 1
				)
			),

			// Asset Manager group is to allow access to Posses and Assets
			array(
				'name' => 'Asset Managers',
				'permissions' => array(
					'pos_manager' => 1,
					'asset_manager' => 1
				)
			),

			// Contract Manager group is to allow access to Posses and Assets
			array(
				'name' => 'Contract Managers',
				'permissions' => array(
					'contract_manager' => 1
				)
			),

			// Market Manager group is to allow access to Market Orders and Assets
			array(
				'name' => 'Market Managers',
				'permissions' => array(
					'market_manager' => 1
				)
			),

			// Key Managers Are Allowed to delete API Keys
			array(
				'name' => 'Key Manager',
				'permissions' => array(
					'key_manager' => 1
				)
			)
		);

		// Loop over $groups and check || create as needed
		foreach ($groups as $group_entry) {
			
			try {

				$group = Sentry::findGroupByName($group_entry['name']);				
				$this->line('[ok] Group ' . $group_entry['name'] . ' exists.');

			} catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {

				$this->info('[info] Group ' . $group_entry['name'] . ' was not found. Creating it.');

				try {
					// Create the group
					$group = Sentry::createGroup(array(
					    'name' => $group_entry['name'],
					    'permissions' => $group_entry['permissions']
					));
					$this->line('[ok] Group ' . $group_entry['name'] . ' created.');

				} catch (\Exception $e) {

					$this->error('Group ' . $group_entry['name'] . ' was not created. Error: ' . $e->getMessage());
					
				}
			}
		}
	}
}
