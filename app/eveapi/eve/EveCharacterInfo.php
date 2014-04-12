<?php

namespace Seat\EveApi\Eve;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class CharacterInfo extends BaseApi {

	public static function Update($characterID, $keyID = null, $vCode = null)
	{
		BaseApi::bootstrap();

		// Check arguements
		if (!is_int($characterID))
			throw new \Exception('Character is required and must be a integer');

		if (isset($keyID))
			BaseApi::validateKeyPair($keyID, $vCode);

		$scope = 'Eve';
		$api = 'CharacterInfo';

		// Prepare the Pheal instance
		$pheal = new Pheal($keyID, $vCode);

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$character_info = $pheal
				->eveScope
				->CharacterInfo(array('characterID' => $characterID));

		} catch (Exception $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $character_info->cached_until, $characterID)) {

			// Update the \EveEveCharacterInfo
			$character_check = \EveEveCharacterInfo::where('characterID', '=', $character_info->characterID)->first();

			if (!$character_check)
				$character = new \EveEveCharacterInfo;
			else
				$character = $character_check;

			$character->characterID = $character_info->characterID;
			$character->characterName = $character_info->characterName;
			$character->race = $character_info->race;
			$character->bloodline = $character_info->bloodline;

			$character->accountBalance = $character_info->accountBalance;
			$character->skillPoints = $character_info->skillPoints;
			$character->nextTrainingEnds = $character_info->nextTrainingEnds;
			$character->shipName = $character_info->shipName;
			$character->shipTypeID = $character_info->shipTypeID;
			$character->shipTypeName = $character_info->shipTypeName;

			$character->corporationID = $character_info->corporationID;
			$character->corporation = $character_info->corporation;
			$character->corporationDate = $character_info->corporationDate;
			$character->allianceID = $character_info->allianceID;
			$character->alliance = $character_info->alliance;
			$character->allianceDate = $character_info->allianceDate;

			$character->lastKnownLocation = $character_info->lastKnownLocation;

			$character->securityStatus = $character_info->securityStatus;
			$character->save();

			// Update \EveEveCharacterInfoEmploymentHistory
			foreach ($character_info->employmentHistory as $employment) {

				$employment_record_check = $character->employment()->where('recordID', '=', $employment->recordID)->first();

				if (!$employment_record_check)
					$employment_entry = new \EveEveCharacterInfoEmploymentHistory;
				else
					$employment_entry = $employment_record_check;

				$employment_entry->recordID = $employment->recordID;
				$employment_entry->corporationID = $employment->corporationID;
				$employment_entry->startDate = $employment->startDate;

				// Add/update the emplyment entry for this character
				$character->employment()->save($employment_entry);
			}

			// Set the cached entry time
			BaseApi::setDbCache($scope, $api, $character_info->cached_until, $characterID);
		}

		return $character_info;
	}
}
