<?php

namespace Seat\EveApi\Map;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Jumps extends BaseApi {

	public static function Update()
	{
		BaseApi::bootstrap();

		$scope = 'Map';
		$api = 'Jumps';

		// Prepare the Pheal instance
		$pheal = new Pheal();

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$jumps = $pheal
				->mapScope
				->Jumps();

		} catch (Exception $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $jumps->cached_until)) {

			// Update the Database while we loop over the solarSystem results
			foreach ($jumps->solarSystems as $solarSystem) {

				// Find the system to check if we need to update || insert
				$system = \EveMapJumps::where('solarSystemID', '=', $solarSystem->solarSystemID)->first();
			
				if (!$system)
					$system_data = new \EveMapJumps;
				else
					$system_data = $system;

				// Update the system_data
				$system_data->solarSystemID	= $solarSystem->solarSystemID;
				$system_data->shipJumps 	= $solarSystem->shipJumps;
				$system_data->save();

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $jumps->cached_until);
			}
		}
		return $jumps;
	}
}
