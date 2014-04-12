<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Research extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
		$api = 'Research';

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
				
				$research = $pheal
					->charScope
					->Research(array('characterID' => $characterID));

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
			if (!BaseApi::checkDbCache($scope, $api, $research->cached_until, $characterID)) {

				// Populate the jobs for this character
				foreach ($research->research as $r) {

					$research_data = \EveCharacterResearch::where('characterID', '=', $characterID)
						->where('agentID', '=', $r->agentID)
						->first();

					if (!$research_data)
						$new_r = new \EveCharacterResearch;
					else
						$new_r = $research_data;

					$new_r->characterID = $characterID;
					$new_r->agentID = $r->agentID;
					$new_r->skillTypeID = $r->skillTypeID;
					$new_r->researchStartDate = $r->researchStartDate;
					$new_r->pointsPerDay = $r->pointsPerDay;
					$new_r->remainderPoints = $r->remainderPoints;
					$new_r->save();
				}

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $research->cached_until, $characterID);
			}
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $research;
	}
}
