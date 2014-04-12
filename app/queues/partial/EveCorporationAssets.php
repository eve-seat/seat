<?php

namespace Seat\EveQueues\Partial;

use Carbon\Carbon;
use Seat\EveApi;

class CorporationAssets {

    public function fire($job, $data) {

        $keyID = $data['keyID'];
        $vCode = $data['vCode'];
        
        $job_record = \SeatQueueInformation::where('jobID', '=', $job->getJobId())->first();

        // Check that we have a valid jobid
        if (!$job_record) {

            // Sometimes the jobs get picked up faster than the submitter could write a
            // database entry about it. So, just wait 5 seconds before we come back and
            // try again
            $job->release(5);
            return;
        }

        // We place the actual API work in our own try catch so that we can report
        // on any critical errors that may have occurred.

        // By default Laravel will requeue a failed job based on --tries, but we
        // dont really want failed api jobs to continually poll the API Server
        try {

            $job_record->status = 'Working';
            $job_record->save();

            $job_record->output = 'Started AssetList Update';
            $job_record->save();
            EveApi\Corporation\AssetList::Update($keyID, $vCode);

            $job_record->status = 'Done';
            $job_record->output = null;        
            $job_record->save();

            $job->delete();

        } catch (\Exception $e) {

            $job_record->status = 'Error';
            $job_record->output = 'Last status: ' . $job_record->output . ' Error: ' . $e->getCode() . ': ' . $e->getMessage() . ' in file ' . $e->getFile() . ' in line ' . $e->getLine();
            $job_record->save();

            $job->delete();
        }
    }
}