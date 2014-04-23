<?php

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Contracts extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Corp';
		$api = 'Contracts';

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
			
			$contracts = $pheal
				->corpScope
				->Contracts(array('characterID' => $characters[0]));

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
		if (!BaseApi::checkDbCache($scope, $api, $contracts->cached_until, $corporationID)) {

			// Loop over the contracts and update
			foreach ($contracts->contractList as $contract) {

				$contract_data = \EveCorporationContracts::where('corporationID', '=', $corporationID)
					->where('contractID', '=', $contract->contractID)
					->first();

				// If we an existing contract that we are just going to update, then dont bother
				// running /char/ContractItems. I *think* this will be the same all the time
				// and can only change by creating a new contract
				if (!$contract_data) {

					$new_data = new \EveCorporationContracts;
					$get_items = true; // [1]
				} else {

					$new_data = $contract_data;
					$get_items = false;
				}
				
				$new_data->corporationID = $corporationID;
				$new_data->contractID = $contract->contractID;
				$new_data->issuerID = $contract->issuerID;
				$new_data->issuerCorpID = $contract->issuerCorpID;
				$new_data->assigneeID = $contract->assigneeID;
				$new_data->acceptorID = $contract->acceptorID;
				$new_data->startStationID = $contract->startStationID;
				$new_data->endStationID = $contract->endStationID;
				$new_data->type = $contract->type;
				$new_data->status = $contract->status;
				$new_data->title = $contract->title;
				$new_data->forCorp = $contract->forCorp;
				$new_data->availability = $contract->availability;
				$new_data->dateIssued = $contract->dateIssued;
				$new_data->dateExpired = (strlen($contract->dateExpired) > 0 ? $contract->dateExpired : null);
				$new_data->dateAccepted = (strlen($contract->dateAccepted) > 0 ? $contract->dateAccepted : null);
				$new_data->numDays = $contract->numDays;
				$new_data->dateCompleted = (strlen($contract->dateCompleted) > 0 ? $contract->dateCompleted : null);
				$new_data->price = $contract->price;
				$new_data->reward = $contract->reward;
				$new_data->collateral = $contract->collateral;
				$new_data->buyout = $contract->buyout;
				$new_data->volume = $contract->volume;
				$new_data->save();

				// [1] New contracts will have their 'items' updated too. Do it
				if ($get_items) {

					try {
						
						$contracts_items = $pheal
							->corpScope
							->ContractItems(array('characterID' => $characters[0], 'contractID' => $contract->contractID));

					// We wont use the ban logic here as the accessmask does not differ
					// from the call that got us as far as this.
					} catch (\Pheal\Exceptions\APIException $e) {
						
						// What to do with a invalid id?
						continue;

					} catch (\Pheal\Exceptions\PhealException $e) {

						throw $e;
					}

					// Loop over the items in contracts and save it
					foreach ($contracts_items->itemList as $item) {
						$items = new \EveCorporationContractsItems;
						
						$items->corporationID = $corporationID;
						$items->contractID = $contract->contractID;
						$items->recordID = $item->recordID;
						$items->typeID = $item->typeID;
						$items->quantity = $item->quantity;
						$items->rawQuantity = (isset($item->rawQuantity) ? $item->rawQuantity : null);
						$items->singleton = $item->singleton;
						$items->included = $item->included;
						$new_data->items()->save($items);
					}
				}
			}

			// Update the cached_until time in the database for this api call
			BaseApi::setDbCache($scope, $api, $contracts->cached_until, $corporationID);
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $contracts;
	}
}
