<?php

namespace App\Services\Queue;
 
class QueueHelper {

	/*
	|--------------------------------------------------------------------------
	| addToQueue()
	|--------------------------------------------------------------------------
	|
	| Checks the existance of a job and adds if not already present.
	|
	*/

	public static function addToQueue($queue, $ownerID, $vCode, $api, $scope) {

		// Set the root namesace for queued commands
		$command_namespace = 'Seat\EveQueues\\';

		// Prepare the auth array
		if($vCode != NULL)
			$auth = array('keyID' => $ownerID, 'vCode' => $vCode);
		else
			$auth = array();

		// Check the databse if there are jobs outstanding ie. they have the status
		// Queued or Working. If not, we will queue a new job, else just capture the
		// jobID and return that
		$jobID = \SeatQueueInformation::where('ownerID', '=', $ownerID)
			->where('api', '=', $api)
			->whereIn('status', array('Queued', 'Working'))
			->first();

		// Check if the $jobID was found, else, queue a new job
		if(!$jobID) {

			$jobID = \Queue::push($command_namespace . implode('\\', $queue),  $auth);
			\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $ownerID, 'api' => $api, 'scope' => $scope, 'status' => 'Queued'));
		} else {

			// To aid in potential capacity debugging, lets write a warning log entry so that a user
			// is able to see that a new job was not submitted
			\Log::warning('A new job was not submitted due a similar one still being outstanding. Details: ' . $jobID, array('src' => __CLASS__));

			// Set the jobID to the ID from the database
			$jobID = $jobID->jobID;
		}

		return $jobID;
	}

}
