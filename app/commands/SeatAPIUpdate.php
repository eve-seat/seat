<?php

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Seat\EveApi;
use Seat\EveApi\Account;

class SeatAPIUpdate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'seat:api-update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Runs the API Update Job scheduler.';

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

		// Server APIs
		$jobID = \Queue::push('Seat\EveQueues\Full\Server', array());
		\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => 0, 'api' => 'ServerStatus', 'scope' => 'Server', 'status' => 'Queued'));

		// Map APIs
		$jobID = \Queue::push('Seat\EveQueues\Full\Map', array());
		\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => 0, 'api' => 'Map', 'scope' => 'Eve', 'status' => 'Queued'));

		// Eve APIs
		$jobID = \Queue::push('Seat\EveQueues\Full\Eve', array());
		\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => 0, 'api' => 'Eve', 'scope' => 'Eve', 'status' => 'Queued'));

		// Get the keys, and process them
		foreach (\SeatKey::where('isOk', '=', 1)->get() as $key) {

			$access = EveApi\BaseApi::determineAccess($key->keyID);
			if (!isset($access['type'])) {
				print 'Unable to determine type for key ' . $key->keyID . PHP_EOL;
				continue;
			}

			$type = $access['type'];

			switch ($access['type']) {
				case 'Character':
					$jobID = \Queue::push('Seat\EveQueues\Full\Character', array('keyID' => $key->keyID, 'vCode' => $key->vCode));
					\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $key->keyID, 'api' => 'Character', 'scope' => 'Eve', 'status' => 'Queued'));					
					break;

				case 'Corporation':
					$jobID = \Queue::push('Seat\EveQueues\Full\Corporation', array('keyID' => $key->keyID, 'vCode' => $key->vCode));
					\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $key->keyID, 'api' => 'Corporation', 'scope' => 'Eve', 'status' => 'Queued'));					
					break;
				
				default:
					# code...
					break;
			}
		}
	}
}
