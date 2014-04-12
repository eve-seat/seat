<?php

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Shareholders extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Corp';
		$api = 'Shareholders';

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
			
			$shareholders = $pheal
				->corpScope
				->Shareholders(array('characterID' => $characters[0]));

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
		if (!BaseApi::checkDbCache($scope, $api, $shareholders->cached_until, $corporationID)) {

			// Characters & Corporations can be share holders. Record both
			foreach ($shareholders->characters as $holder) {

				$holder_data = \EveCorporationShareholderCharacters::where('shareholderID', '=', $holder->shareholderID)
					->where('corporationID', '=', $corporationID)
					->first();

				if (!$holder_data)
					$holder_data = new \EveCorporationShareholderCharacters;

				$holder_data->corporationID = $corporationID;
				$holder_data->shareholderID = $holder->shareholderID;
				$holder_data->shareholderName = $holder->shareholderName;
				$holder_data->shareholderCorporationID = $holder->shareholderCorporationID;
				$holder_data->shareholderCorporationName = $holder->shareholderCorporationName;
				$holder_data->shares = $holder->shares;
				$holder_data->save();
			}

			foreach ($shareholders->corporations as $holder) {

				$holder_data = \EveCorporationShareholderCorporations::where('shareholderID', '=', $holder->shareholderID)
					->where('corporationID', '=', $corporationID)
					->first();

				if (!$holder_data)
					$holder_data = new \EveCorporationShareholderCorporations;

				$holder_data->corporationID = $corporationID;
				$holder_data->shareholderID = $holder->shareholderID;
				$holder_data->shareholderName = $holder->shareholderName;
				$holder_data->shares = $holder->shares;
				$holder_data->save();
			}

			// Update the cached_until time in the database for this api call
			BaseApi::setDbCache($scope, $api, $shareholders->cached_until, $corporationID);
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $shareholders;
	}
}
