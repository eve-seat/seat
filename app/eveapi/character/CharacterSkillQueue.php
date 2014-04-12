<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class SkillQueue extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
		$api = 'SkillQueue';

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
				
				$skill_queue = $pheal
					->charScope
					->SkillQueue(array('characterID' => $characterID));

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
			if (!BaseApi::checkDbCache($scope, $api, $skill_queue->cached_until, $characterID)) {

				// Remove the current Queue
				\EveCharacterSkillQueue::where('characterID', '=', $characterID)->delete();

				// Add the current queue information
				foreach ($skill_queue->skillqueue as $queue) {

					// Start a new queue entry for this character
					$character_data = new \EveCharacterSkillQueue;

					// And populate the fields for him.
					$character_data->characterID = $characterID;
					$character_data->queuePosition = $queue->queuePosition;
					$character_data->typeID = $queue->typeID;
					$character_data->level = $queue->level;
					$character_data->startSP = $queue->startSP;
					$character_data->endSP = $queue->endSP;
					$character_data->startTime = (strlen($queue->startTime) > 0 ? $queue->startTime : null);
					$character_data->endTime = (strlen($queue->endTime) > 0 ? $queue->endTime : null);
					$character_data->save();
				}

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $skill_queue->cached_until, $characterID);
			}
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $skill_queue;
	}
}
