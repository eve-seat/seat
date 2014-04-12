<?php

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class MarketOrders extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Corp';
		$api = 'MarketOrders';

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
			
			$market_orders = $pheal
				->corpScope
				->MarketOrders(array('characterID' => $characters[0]));

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
		if (!BaseApi::checkDbCache($scope, $api, $market_orders->cached_until, $corporationID)) {

			foreach ($market_orders->orders as $order) {

				$order_data = \EveCorporationMarketOrders::where('corporationID', '=', $corporationID)
					->where('orderID', '=', $order->orderID)
					->first();

				if (!$order_data)
					$order_info = new \EveCorporationMarketOrders;
				else
					$order_info = $order_data;

				$order_info->corporationID = $corporationID;
				$order_info->orderID = $order->orderID;
				$order_info->charID = $order->charID;
				$order_info->stationID = $order->stationID;
				$order_info->volEntered = $order->volEntered;
				$order_info->volRemaining = $order->volRemaining;
				$order_info->minVolume = $order->minVolume;
				$order_info->orderState = $order->orderState;
				$order_info->typeID = $order->typeID;
				$order_info->range = $order->range;
				$order_info->accountKey = $order->accountKey;
				$order_info->duration = $order->duration;
				$order_info->escrow = $order->escrow;
				$order_info->price = $order->price;
				$order_info->bid = $order->bid;
				$order_info->issued = $order->issued;
				$order_info->save();
			}

			// Update the cached_until time in the database for this api call
			BaseApi::setDbCache($scope, $api, $market_orders->cached_until, $corporationID);
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $market_orders;
	}
}
