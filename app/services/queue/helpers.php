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

namespace App\Services\Queue;

class QueueHelper
{

    /*
    |--------------------------------------------------------------------------
    | addToQueue()
    |--------------------------------------------------------------------------
    |
    | Checks the existance of a job and adds if not already present.
    |
    */

    public static function addToQueue($queue, $ownerID, $vCode, $api, $scope) {

        // Prepare the auth array
        if($vCode != null)
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

            $jobID = \Queue::push($queue, $auth);
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
