<?php

namespace Seat\EveApi\Eve;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class ConquerableStationList extends BaseApi {

	public static function Update()
	{
		BaseApi::bootstrap();

		$scope = 'Eve';
		$api = 'ConquerableStationList';

		// Prepare the Pheal instance
		$pheal = new Pheal();

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$station_list = $pheal
				->eveScope
				->ConquerableStationList();

		} catch (Exception $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $station_list->cached_until)) {

			// Update the Database while we loop over the results
			foreach ($station_list->outposts as $outpost) {

				// Check if we need to update || insert
				$outpost_check = \EveEveConquerableStationList::where('stationID', '=', $outpost->stationID)->first();
			
				if (!$outpost_check)
					$outpost_data = new \EveEveConquerableStationList;
				else
					$outpost_data = $outpost_check;

				$outpost_data->stationID	= $outpost->stationID;
				$outpost_data->stationName = $outpost->stationName;
				$outpost_data->stationTypeID = $outpost->stationTypeID;
				$outpost_data->solarSystemID = $outpost->solarSystemID;
				$outpost_data->corporationID = $outpost->corporationID;
				$outpost_data->corporationName = $outpost->corporationName;
				$outpost_data->save();

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $station_list->cached_until);
			}
		}
		return $station_list;
	}
}
