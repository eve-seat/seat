<?php

namespace Seat\EveApi\Account;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class AccountStatus extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Account';
		$api = 'AccountStatus';

		if (BaseApi::isBannedCall($api, $scope, $keyID))
			return;

		// Prepare the Pheal instance
		$pheal = new Pheal($keyID, $vCode);

		// Need to check that this is not a Corporation Key...

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$status_info = $pheal
				->accountScope
				->AccountStatus();

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
		if (!BaseApi::checkDbCache($scope, $api, $status_info->cached_until, $keyID)) {

			$check_status = \EveAccountAccountStatus::where('keyID', '=', $keyID)->first();

			if (!$check_status)
				$status_data = new \EveAccountAccountStatus;
			else
				$status_data = $check_status;

			$status_data->keyID = $keyID;
			$status_data->paidUntil = $status_info->paidUntil;
			$status_data->createDate = $status_info->createDate;
			$status_data->logonCount = $status_info->logonCount;
			$status_data->logonMinutes = $status_info->logonMinutes;
			$status_data->save();

			// Update the cached_until time in the database for this api call
			BaseApi::setDbCache($scope, $api, $status_info->cached_until, $keyID);
		}
		return $status_info;
	}
}
