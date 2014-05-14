<?php

namespace App\Services\Queue;
 
class QueueHelper {

/*
	|--------------------------------------------------------------------------
	| addToQueue()
	|--------------------------------------------------------------------------
	|
	| Checks the job doesn't already exist in the queue, if it doesn't then
	| 	it adds it to the job queue
	|
	*/

	public static function addToQueue($queue, $ownerID, $vCode, $api, $scope, $return = false) {

		if($vCode != NULL)
			$array = array('keyID' => $ownerID, 'vCode' => $vCode);
		else
			$array = array();

		if(!\SeatQueueInformation::where('ownerID', '=', $ownerID)->where('api', '=', $api)->where('status', '=', 'Queued')->orWhere('status', '=', 'Working')->first()){
			$jobID = \Queue::push('Seat\EveQueues'.$queue,  $array);
			\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $ownerID, 'api' => $api, 'scope' => $scope, 'status' => 'Queued'));
		}

		if($return == false){
			return;
		}else{
			return $jobID;
		}

	}

}