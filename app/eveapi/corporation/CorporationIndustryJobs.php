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

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class IndustryJobs extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'IndustryJobs';

        if (BaseApi::isBannedCall($api, $scope, $keyID))
            return;

        // Get the characters for this key
        $characters = BaseApi::findKeyCharacters($keyID);

        // Check if this key has any characters associated with it
        if (!$characters)
            return;

        // Lock the call so that we are the only instance of this running now()
        // If it is already locked, just return without doing anything
        if (!BaseApi::isLockedCall($api, $scope, $keyID))
            $lockhash = BaseApi::lockCall($api, $scope, $keyID);
        else
            return;

        // So I think a corporation key will only ever have one character
        // attached to it. So we will just use that characterID for auth
        // things, but the corporationID for locking etc.
        $corporationID = BaseApi::findCharacterCorporation($characters[0]);

        // Prepare the Pheal instance
        $pheal = new Pheal($keyID, $vCode);

        // Do the actual API call. pheal-ng actually handles some internal
        // caching too.
        try {

            $industry_jobs = $pheal
                ->corpScope
                ->IndustryJobs(array('characterID' => $characters[0]));

        } catch (\Pheal\Exceptions\APIException $e) {

            // If we cant get account status information, prevent us from calling
            // this API again
            BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
            return;

        } catch (\Pheal\Exceptions\PhealException $e) {

            throw $e;
        }

        // Check if the data in the database is still considered up to date.
        // checkDbCache will return true if this is the case
        if (!BaseApi::checkDbCache($scope, $api, $industry_jobs->cached_until, $corporationID)) {

            // Populate the jobs for this character
            foreach ($industry_jobs->jobs as $job) {

                $job_data = \EveCorporationIndustryJobs::where('corporationID', '=', $corporationID)
                    ->where('jobID', '=', $job->jobID)
                    ->first();

                if (!$job_data)
                    $job_data = new \EveCorporationIndustryJobs;

                $job_data->corporationID = $corporationID;
                $job_data->jobID = $job->jobID;
                $job_data->outputLocationID = $job->outputLocationID;
                $job_data->installerID = $job->installerID;
                $job_data->runs = $job->runs;
                $job_data->activityID = $job->activityID;
                $job_data->installerName = $job->installerName;
                $job_data->facilityID = $job->facilityID;
                $job_data->solarSystemID = $job->solarSystemID;
                $job_data->solarSystemName = $job->solarSystemName;
                $job_data->stationID = $job->stationID;
                $job_data->blueprintID = $job->blueprintID;
                $job_data->blueprintTypeID = $job->blueprintTypeID;
                $job_data->blueprintTypeName = $job->blueprintTypeName;
                $job_data->blueprintLocationID = $job->blueprintLocationID;
                $job_data->cost = $job->cost;
                $job_data->teamID = $job->teamID;
                $job_data->licensedRuns = $job->licensedRuns;
                $job_data->probability = $job->probability;
                $job_data->productTypeID = $job->productTypeID;
                $job_data->productTypeName = $job->productTypeName;
                $job_data->status = $job->status;
                $job_data->timeInSeconds = $job->timeInSeconds;
                $job_data->startDate = $job->startDate;
                $job_data->endDate = $job->endDate;
                $job_data->pauseDate = $job->pauseDate;
                $job_data->completedDate = $job->completedDate;
                $job_data->completedCharacterID = $job->completedCharacterID;
                $job_data->save();
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $industry_jobs->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $industry_jobs;
    }
}
