<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class IndustryJobs extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
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

		// Next, start our loop over the characters and upate the database
		foreach ($characters as $characterID) {

			// Prepare the Pheal instance
			$pheal = new Pheal($keyID, $vCode);

			// Do the actual API call. pheal-ng actually handles some internal
			// caching too.
			try {
				
				$industry_jobs = $pheal
					->charScope
					->IndustryJobs(array('characterID' => $characterID));

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
			if (!BaseApi::checkDbCache($scope, $api, $industry_jobs->cached_until, $characterID)) {

				// Populate the jobs for this character
				foreach ($industry_jobs->jobs as $job) {

					$job_data = \EveCharacterIndustryJobs::where('characterID', '=', $characterID)
						->where('jobID', '=', $job->jobID)
						->first();

					if (!$job_data)
						$new_job = new \EveCharacterIndustryJobs;
					else
						$new_job = $job_data;

					$new_job->jobID = $job->jobID;
					$new_job->characterID = $characterID;
					$new_job->outputLocationID = $job->outputLocationID;
					$new_job->installerID = $job->installerID;
					$new_job->runs = $job->runs;
					$new_job->activityID = $job->activityID;
					$new_job->installerName = $job->installerName;
					$new_job->facilityID = $job->facilityID;
					$new_job->solarSystemID = $job->solarSystemID;
					$new_job->solarSystemName = $job->solarSystemName;
					$new_job->stationID = $job->stationID;
					$new_job->blueprintID = $job->blueprintID;
					$new_job->blueprintTypeID = $job->blueprintTypeID;
					$new_job->blueprintTypeName = $job->blueprintTypeName;
					$new_job->blueprintLocationID = $job->blueprintLocationID;
					$new_job->cost = $job->cost;
					$new_job->teamID = $job->teamID;
					$new_job->licensedRuns = $job->licensedRuns;
					$new_job->probability = $job->probability;
					$new_job->productTypeID = $job->productTypeID;
					$new_job->productTypeName = $job->productTypeName;
					$new_job->status = $job->status;
					$new_job->timeInSeconds = $job->timeInSeconds;
					$new_job->startDate = $job->startDate;
					$new_job->endDate = $job->endDate;
					$new_job->pauseDate = $job->pauseDate;
					$new_job->completedDate = $job->completedDate;
					$new_job->completedCharacterID = $job->completedCharacterID;
					$new_job->save();
				}

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $industry_jobs->cached_until, $characterID);
			}
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $industry_jobs;
	}
}
