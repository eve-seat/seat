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

					$new_job->characterID = $characterID;
					$new_job->jobID = $job->jobID;
					$new_job->assemblyLineID = $job->assemblyLineID;
					$new_job->containerID = $job->containerID;
					$new_job->installedItemID = $job->installedItemID;
					$new_job->installedItemLocationID = $job->installedItemLocationID;
					$new_job->installedItemQuantity = $job->installedItemQuantity;
					$new_job->installedItemProductivityLevel = $job->installedItemProductivityLevel;
					$new_job->installedItemMaterialLevel = $job->installedItemMaterialLevel;
					$new_job->installedItemLicensedProductionRunsRemaining = $job->installedItemLicensedProductionRunsRemaining;
					$new_job->outputLocationID = $job->outputLocationID;
					$new_job->installerID = $job->installerID;
					$new_job->runs = $job->runs;
					$new_job->licensedProductionRuns = $job->licensedProductionRuns;
					$new_job->installedInSolarSystemID = $job->installedInSolarSystemID;
					$new_job->containerLocationID = $job->containerLocationID;
					$new_job->materialMultiplier = $job->materialMultiplier;
					$new_job->charMaterialMultiplier = $job->charMaterialMultiplier;
					$new_job->timeMultiplier = $job->timeMultiplier;
					$new_job->charTimeMultiplier = $job->charTimeMultiplier;
					$new_job->installedItemTypeID = $job->installedItemTypeID;
					$new_job->outputTypeID = $job->outputTypeID;
					$new_job->containerTypeID = $job->containerTypeID;
					$new_job->installedItemCopy = $job->installedItemCopy;
					$new_job->completed = $job->completed;
					$new_job->completedSuccessfully = $job->completedSuccessfully;
					$new_job->installedItemFlag = $job->installedItemFlag;
					$new_job->outputFlag = $job->outputFlag;
					$new_job->activityID = $job->activityID;
					$new_job->completedStatus = $job->completedStatus;
					$new_job->installTime = $job->installTime;
					$new_job->beginProductionTime = $job->beginProductionTime;
					$new_job->endProductionTime = $job->endProductionTime;
					$new_job->pauseProductionTime = $job->pauseProductionTime;
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
