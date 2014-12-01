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

		\Log::info('Started command ' . $this->name, array('src' => __CLASS__));

		// Server, Map & Eve APIs
		\App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Server'), 0, NULL, 'ServerStatus', 'Server');
		\App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Map'), 0, NULL, 'Map', 'Eve');
		\App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Eve'), 0, NULL, 'Eve', 'Eve');

		// Log the start of the key processing
		\Log::info('Starting job submissions for all keys', array('src' => __CLASS__));

		// Get the keys, and process them
		foreach (\SeatKey::where('isOk', '=', 1)->get() as $key) {

			$access = EveApi\BaseApi::determineAccess($key->keyID);
			if (!isset($access['type'])) {
				\Log::error('Unable to determine type for key ' . $key->keyID, array('src' => __CLASS__));
				continue;
			}

			$type = $access['type'];

			switch ($access['type']) {
				case 'Character':
                    // fix for Api Corporation type banned call <<
                    Account\AccountStatus::update($key->keyID, $key->vCode);
                    // >>

					\App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Character'), $key->keyID, $key->vCode, 'Character', 'Eve');
					break;

				case 'Corporation':
					\App\Services\Queue\QueueHelper::addToQueue(array('Full', 'Corporation'), $key->keyID, $key->vCode, 'Corporation', 'Eve');
					break;
				
				default:
					# code...
					break;
			}
		}
	}
}
