<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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

		$admin = \User::where('username', '=', 'admin')->first();

		if (!isset($admin)) {

			$this->error('The admin user could not be found... Have you run db:seed ?');
			return;
		}

		$admin->password = \Hash::make($password);
		$admin->save();

		$this->info('Password has been changed successfully.');
	}
}
