<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Carthalyst\Sentry as Sentry;

class SeatReset extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:reset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset the SeAT Web Administrator Password.';

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
		$this->error('WARNING!!! This will RESET the current Administrator password!');
		$this->line('');

		$password = $this->secret('What is the new password to use for the admin user? : ');
		$password2 = $this->secret('Retype that password please: ');

		if ($password <> $password2) {

			$this->error('The passwords do not match. Not resetting');
			return;
		}

		$this->info('The passwords match. Resetting to the new ' . strlen($password) . ' char one.');

		// Attempt to find the admin user usnig Sentry helper functions.
		// If the user does not exist, we create it.
		try {

			$admin = \Sentry::findUserByLogin('admin');

		} catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {

			\Sentry::register(array(
				'email'	 	=> 'admin',
				'password'	=> $password,
			), true);	// Set the account to be active

			$admin = \Sentry::findUserByLogin('admin');
		}

		// Next, we check for the existance of the admin group and create it if it
		// does not exist
		try {

			$adminGroup = \Sentry::findGroupByName('Administrators');

		} catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {

			\Sentry::createGroup(array(
			    'name'        => 'Administrators',
			    'permissions' => array(
			        'superuser' => 1,
			    ),
			));

			$adminGroup = \Sentry::findGroupByName('Administrators');
		}

		// Set the password and group membership for the admin user.
		$admin->password = $password;
		$admin->save();
		$admin->addGroup($adminGroup);

		$this->info('Password has been changed successfully.');
	}
}
