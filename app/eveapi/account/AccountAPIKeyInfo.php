<?php

namespace Seat\EveApi\Account;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class APIKeyInfo extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		$scope = 'Account';
		$api = 'APIKeyInfo';

		// Prepare the Pheal instance
		$pheal = new Pheal($keyID, $vCode);

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$key_info = $pheal
				->accountScope
				->APIKeyInfo();

		} catch (\Pheal\Exceptions\APIException $e) {

			// If we cant get key information, just disable the key as its probably
			// not valid...

			// ... unless its actually *not* invalid but the API server is sick.
			// Think we'll just check if the error we got is one in an array we have
			// of ones we can 'safely' ignore. Typically temp errors.
			$ignore_error_codes = array(
				520 => 'Unexpected failure accessing database.'
			);

			// Check if we should ignore or disable
			if (!in_array($e->getCode(), $ignore_error_codes)) {

				BaseApi::disableKey($keyID, $e->getCode() . ': ' . $e->getMessage());
				return;

			} else {

			    return;
			}
		    
		} catch (\Pheal\Exceptions\PhealException $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $key_info->cached_until, $keyID)) {

			$key_data = \EveAccountAPIKeyInfo::where('keyID', '=', $keyID)->first();

			if (!$key_data)
				$key_data = new \EveAccountAPIKeyInfo;

			$key_data->keyID = $keyID;
			$key_data->accessMask = $key_info->key->accessMask;
			$key_data->type = $key_info->key->type;
			$key_data->expires = (strlen($key_info->key->expires) > 0 ? $key_info->key->expires : null);	// hack much?
			$key_data->save();

			// Check if we have any knowledge of any characters for this key. We will remove values from this
			// array as we move along to determine which characters we should delete that are possibly no
			// longer on this key
			$known_characters = array();
			foreach (\EveAccountAPIKeyInfoCharacters::where('keyID', '=', $keyID)->get() as $character) {
				$known_characters[] = $character->characterID;
			}
			$known_characters = array_flip($known_characters);

			// Update the key characters
			foreach ($key_info->key->characters as $character) {

				// Check if we need to update || insert
				$character_data = \EveAccountAPIKeyInfoCharacters::where('keyID', '=', $keyID)
					->where('characterID', '=', $character->characterID)->first();
			
				if (!$character_data)
					$character_data = new \EveAccountAPIKeyInfoCharacters;

				// else, add/update
				$character_data->characterID = $character->characterID;
				$character_data->characterName = $character->characterName;
				$character_data->corporationID = $character->corporationID;
				$character_data->corporationName = $character->corporationName;
				$key_data->characters()->save($character_data);

				// Remove this characterID from the known_characters as its still on
				// the key
				if (array_key_exists($character->characterID, $known_characters))
					unset($known_characters[$character->characterID]);
			}

			// Delete the characters that are no longer part of this key
			foreach (array_flip($known_characters) as $oldcharacter)
				\EveAccountAPIKeyInfoCharacters::where('keyID', '=', $keyID)->where('characterID', '=', $oldcharacter)->delete();

			// Update the cached_until time in the database for this api call
			BaseApi::setDbCache($scope, $api, $key_info->cached_until, $keyID);
		}
		
		return $key_info;
	}
}
