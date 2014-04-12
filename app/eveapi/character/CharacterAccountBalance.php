<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class AccountBalance extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
		$api = 'AccountBalance';

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
				
				$account_balance = $pheal
					->charScope
					->AccountBalance(array('characterID' => $characterID));

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
			if (!BaseApi::checkDbCache($scope, $api, $account_balance->cached_until, $characterID)) {

				// While we _know_ that there should only be one account for characters
				// aka account 1000, lets loop anyways. You never know what CCP might do
				// next :)
				foreach ($account_balance->accounts as $account) {

					$check_account = \EveCharacterAccountBalance::where('characterID', '=', $characterID)
						->where('accountID', '=', $account->accountID)
						->first();

					if (!$check_account)
						$account_data = new \EveCharacterAccountBalance;
					else
						$account_data = $check_account;

					$account_data->characterID = $characterID;
					$account_data->accountID = $account->accountID;
					$account_data->accountKey = $account->accountKey;
					$account_data->balance = $account->balance;
					$account_data->save();
				}

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $account_balance->cached_until, $characterID);
			}
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $account_balance;
	}
}
