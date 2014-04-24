<?php

class QueueController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| getStatus()
	|--------------------------------------------------------------------------
	|
	| Display the current backend Queue status
	|
	*/

	public function getStatus()
	{

		// Connect to the Redis backend
		try {

			$redis = new Predis\Client(array('host' => Config::get('database.redis.default.host'), 'port' => Config::get('database.redis.default.port')));			
			$redis_count = count($redis->lrange('queues:default', 0, -1)); // Run LRANGE queues:default 0 -1. Laravel deletes done jobs from the queue

			$redis_status = 'OK';

		} catch (Exception $e) {
		
			$redis_count = 0;
			$redis_status = $e->getmessage();	
		}

		// Get the Queue information from the database
		$db_done_count = \SeatQueueInformation::where('status', '=', 'Done')->count();

		$db_queue_count = \SeatQueueInformation::where('status', '=', 'Queued')->count();
		$db_queue = \SeatQueueInformation::where('status', '=', 'Queued')->get();

		$db_error_count = \SeatQueueInformation::where('status', '=', 'Error')->count();
		$db_errors = \SeatQueueInformation::where('status', '=', 'Error')->get();

		$db_working_count = \SeatQueueInformation::where('status', '=', 'Working')->count();
		$db_working = \SeatQueueInformation::where('status', '=', 'Working')->get();



		return View::make('queue.status')
			->with('redis_count', $redis_count)
			->with('redis_status', $redis_status)
			->with('db_queue_count', $db_queue_count)
			->with('db_queue', $db_queue)
			->with('db_done_count', $db_done_count)
			->with('db_error_count', $db_error_count)
			->with('db_errors', $db_errors)
			->with('db_working_count', $db_working_count)
			->with('db_working', $db_working);
	}

	/*
	|--------------------------------------------------------------------------
	| getShortStatus()
	|--------------------------------------------------------------------------
	|
	| Return the current error, working and queued job counts as json
	|
	*/

	public function getShortStatus()
	{

		// Get the Queue information from the database
		$db_queue_count = \SeatQueueInformation::where('status', '=', 'Queued')->count();
		$db_working_count = \SeatQueueInformation::where('status', '=', 'Working')->count();
		$db_error_count = \SeatQueueInformation::where('status', '=', 'Error')->count();

		$response = array(
			'queue_count' => $db_queue_count,
			'working_count' => $db_working_count,
			'error_count' => $db_error_count
		);

		return Response::json($response);
	}

	/*
	|--------------------------------------------------------------------------
	| getDeleteError()
	|--------------------------------------------------------------------------
	|
	| Removes a Job record that is in a error state
	|
	*/

	public function getDeleteError($id)
	{

		\SeatQueueInformation::where('status','Error')
			->where('id', $id)
			->delete();

		return Response::json();
	}
	
		/*
	|--------------------------------------------------------------------------
	| getDeleteQueuedJob()
	|--------------------------------------------------------------------------
	|
	| Removes a Job record that is in a queued state
	|
	*/

	public function getDeleteQueuedJob($id)
	{

		\SeatQueueInformation::where('status','Queued')
			->where('id', $id)
			->delete();

		return Response::json();
	}
}
