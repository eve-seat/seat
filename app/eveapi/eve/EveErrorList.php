<?php

namespace Seat\EveApi\Eve;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class ErrorList extends BaseApi {

	public static function Update()
	{
		BaseApi::bootstrap();

		$scope = 'Eve';
		$api = 'ErrorList';

		// Prepare the Pheal instance
		$pheal = new Pheal();

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$error_list = $pheal
				->eveScope
				->ErrorList();

		} catch (Exception $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $error_list->cached_until)) {

			// Update the Database while we loop over the solarSystem results
			foreach ($error_list->errors as $error) {

				// Find the system to check if we need to update || insert
				$error_check = \EveEveErrorList::where('errorCode', '=', $error->errorCode)->first();
			
				if (!$error_check)
					$error_data = new \EveEveErrorList;
				else
					$error_data = $error_check;

				// Update the system
				$error_data->errorCode	= $error->errorCode;
				$error_data->errorText = $error->errorText;
				$error_data->save();
			}
				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $error_list->cached_until);
			
		}
		return $error_list;
	}
}
