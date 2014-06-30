<?php

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class StarbaseDetail extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Corp';
		$api = 'StarbaseDetail';

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

		// We now need to loop over the starbases that we have for this corporation
		// and update the details as needed.
		foreach (\EveCorporationStarbaseList::where('corporationID', '=', $corporationID)->get() as $starbase) {

			// Do the actual API call. pheal-ng actually handles some internal
			// caching too.
			try {
				
				$starbase_detail = $pheal
					->corpScope
					->StarbaseDetail(array('characterID' => $characters[0], 'itemID' => $starbase->itemID));

			} catch (\Pheal\Exceptions\APIException $e) {

				// In the odd chance that we get a old/invalid ID, catch only that error
				// else go boom
				if ($e->getCode() == 114) {

					\Log::error('API Exception caught but continuing. Error: ' . $e->getCode() . ': ' . $e->getMessage(), array('src' => __CLASS__));
					continue;
				}

				// I suspect that there is a caching specific thing here that I don't understand. I know
				// this is a bad thing, but for now, just continue when this occurs, and write a entry
				// to the application log about it.
				//
				// Error: 221: Illegal page request! Please verify the access granted by the key you are using!
				// ^~~~ this after we just got the starbase list... :(
				if ($e->getCode() == 221) {

					\Log::error('API Exception caught but continuing. Error: ' . $e->getCode() . ': ' . $e->getMessage(), array('src' => __CLASS__));
					continue;
				}

				// Lastly, go boom as something out of the ordinary is wrong.
				throw $e;

			} catch (\Pheal\Exceptions\PhealException $e) {

				// Lets add some information to the original exception and raise it
				$new_error = $e->getMessage() . ' - Current starbaseID: ' . $starbase->itemID;
				throw new Exception($new_error, $e->getCode());
			}

			// Update the details
			$starbase_data = \EveCorporationStarbaseDetail::where('corporationID', '=', $corporationID)
				->where('itemID', '=', $starbase->itemID)
				->first();

			if (!$starbase_data)
				$starbase_data = new \EveCorporationStarbaseDetail;

			$starbase_data->corporationID = $corporationID;
			$starbase_data->itemID = $starbase->itemID; // Fromt he outer loop
			$starbase_data->state = $starbase_detail->state;
			$starbase_data->stateTimestamp = $starbase_detail->stateTimestamp;
			$starbase_data->onlineTimestamp = $starbase_detail->onlineTimestamp;
			$starbase_data->usageFlags = $starbase_detail->generalSettings->usageFlags;
			$starbase_data->deployFlags = $starbase_detail->generalSettings->deployFlags;
			$starbase_data->allowCorporationMembers = $starbase_detail->generalSettings->allowCorporationMembers;
			$starbase_data->allowAllianceMembers = $starbase_detail->generalSettings->allowAllianceMembers;
			$starbase_data->useStandingsFrom = $starbase_detail->combatSettings->useStandingsFrom->ownerID;
			$starbase_data->onStandingDrop = $starbase_detail->combatSettings->onStandingDrop->standing;
			$starbase_data->onStatusDropEnabled = $starbase_detail->combatSettings->onStatusDrop->enabled;
			$starbase_data->onStatusDropStanding = $starbase_detail->combatSettings->onStatusDrop->standing;
			$starbase_data->onAggression = $starbase_detail->combatSettings->onAggression->enabled;
			$starbase_data->onCorporationWar = $starbase_detail->combatSettings->onCorporationWar->enabled;

			// Add the fuel
			foreach ($starbase_detail->fuel as $fuel) {
				
				if($fuel->typeID == 16275)
					$starbase_data->strontium = $fuel->quantity;
				
				if(in_array($fuel->typeID, array('4051','4246','4247','4312')))
					$starbase_data->fuelBlocks = $fuel->quantity;
			}

			$starbase_data->save();
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return null;
	}
}
