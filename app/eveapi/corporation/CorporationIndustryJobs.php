<?php

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class IndustryJobs extends BaseApi {

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
				$job_data->assemblyLineID = $job->assemblyLineID;
				$job_data->containerID = $job->containerID;
				$job_data->installedItemID = $job->installedItemID;
				$job_data->installedItemLocationID = $job->installedItemLocationID;
				$job_data->installedItemQuantity = $job->installedItemQuantity;
				$job_data->installedItemProductivityLevel = $job->installedItemProductivityLevel;
				$job_data->installedItemMaterialLevel = $job->installedItemMaterialLevel;
				$job_data->installedItemLicensedProductionRunsRemaining = $job->installedItemLicensedProductionRunsRemaining;
				$job_data->outputLocationID = $job->outputLocationID;
				$job_data->installerID = $job->installerID;
				$job_data->runs = $job->runs;
				$job_data->licensedProductionRuns = $job->licensedProductionRuns;
				$job_data->installedInSolarSystemID = $job->installedInSolarSystemID;
				$job_data->containerLocationID = $job->containerLocationID;
				$job_data->materialMultiplier = $job->materialMultiplier;
				$job_data->charMaterialMultiplier = $job->charMaterialMultiplier;
				$job_data->timeMultiplier = $job->timeMultiplier;
				$job_data->charTimeMultiplier = $job->charTimeMultiplier;
				$job_data->installedItemTypeID = $job->installedItemTypeID;
				$job_data->outputTypeID = $job->outputTypeID;
				$job_data->containerTypeID = $job->containerTypeID;
				$job_data->installedItemCopy = $job->installedItemCopy;
				$job_data->completed = $job->completed;
				$job_data->completedSuccessfully = $job->completedSuccessfully;
				$job_data->installedItemFlag = $job->installedItemFlag;
				$job_data->outputFlag = $job->outputFlag;
				$job_data->activityID = $job->activityID;
				$job_data->completedStatus = $job->completedStatus;
				$job_data->installTime = $job->installTime;
				$job_data->beginProductionTime = $job->beginProductionTime;
				$job_data->endProductionTime = $job->endProductionTime;
				$job_data->pauseProductionTime = $job->pauseProductionTime;
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
