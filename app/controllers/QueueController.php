<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class QueueController extends BaseController
{

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

        $db_history = \SeatQueueInformation::orderBy('updated_at', 'desc')->take(25)->get();

        return View::make('queue.status')
            ->with('redis_count', $redis_count)
            ->with('redis_status', $redis_status)
            ->with('db_queue_count', $db_queue_count)
            ->with('db_queue', $db_queue)
            ->with('db_done_count', $db_done_count)
            ->with('db_error_count', $db_error_count)
            ->with('db_errors', $db_errors)
            ->with('db_working_count', $db_working_count)
            ->with('db_working', $db_working)
            ->with('db_history', $db_history);
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
    | getViewError()
    |--------------------------------------------------------------------------
    |
    | Removes a Job record that is in a error state
    |
    */

    public function getViewError($id)
    {

        if (!\Auth::isSuperUser() )
            App::abort(404);

        $detail = \SeatQueueInformation::where('id', $id)->first();

        return View::make('queue.view')
            ->with('detail', $detail);
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

        if (!\Auth::isSuperUser() )
            App::abort(404);

        \SeatQueueInformation::where('status','Error')
            ->where('id', $id)
            ->delete();

        return Response::json();
    }

    /*
    |--------------------------------------------------------------------------
    | getDeleteAllErrors()
    |--------------------------------------------------------------------------
    |
    | Removes all Job Errors
    |
    */

    public function getDeleteAllErrors()
    {

        if (!\Auth::isSuperUser() )
            App::abort(404);

        \SeatQueueInformation::where('status','Error')
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

        if (!\Auth::isSuperUser() )
            App::abort(404);

        // Get the Redis JobID from the databse for this jib
        $redis_job_id = \SeatQueueInformation::where('id', $id)->pluck('jobID');

        // Connect to redis and loop over the queued jobs, looking for the job
        $redis = new Predis\Client(array('host' => Config::get('database.redis.default.host'), 'port' => Config::get('database.redis.default.port')));

        // Loop over the jobs in redis, looking for the one we want to delete
        foreach ($redis->lrange('queues:default', 0, -1) as $redis_job) {

            // Delete it if we match the jobID
            if (strpos($redis_job, $redis_job_id))
                $redis->lrem('queues:default', 0, $redis_job);
        }

        // Delete if from the database too
        \SeatQueueInformation::where('status','Queued')
            ->where('id', $id)
            ->delete();

        return Response::json();
    }
}
