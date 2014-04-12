<?php

namespace Seat\EveApi\Map;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Kills extends BaseApi {

	public static function Update()
	{
		BaseApi::bootstrap();

		$scope = 'Map';
		$api = 'Kills';

		// Prepare the Pheal instance
		$pheal = new Pheal();

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$kills = $pheal
				->mapScope
				->Kills();

		} catch (Exception $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $kills->cached_until)) {

			// Update the Database while we loop over the solarSystem results
			foreach ($kills->solarSystems as $solarSystem) {

				// Find the system to check if we need to update || insert
				$system = \EveMapKills::where('solarSystemID', '=', $solarSystem->solarSystemID)->first();
			
				if (!$system)
					$system_data = new \EveMapKills;
				else
					$system_data = $system;

				// Update the system
				$system_data->solarSystemID	= $solarSystem->solarSystemID;
				$system_data->shipKills 	= $solarSystem->shipKills;
				$system_data->factionKills 	= $solarSystem->factionKills;
				$system_data->podKills 		= $solarSystem->podKills;
				$system_data->save();

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $kills->cached_until);
			}
		}
		return $kills;
	}
}
