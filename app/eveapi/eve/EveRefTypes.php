<?php

namespace Seat\EveApi\Eve;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class RefTypes extends BaseApi {

	public static function Update()
	{
		BaseApi::bootstrap();

		$scope = 'Eve';
		$api = 'RefTypes';

		// Prepare the Pheal instance
		$pheal = new Pheal();

		// Do the actual API call. pheal-ng actually handles some internal
		// caching too.
		try {
			
			$ref_types = $pheal
				->eveScope
				->RefTypes();

		} catch (Exception $e) {

			throw $e;
		}

		// Check if the data in the database is still considered up to date.
		// checkDbCache will return true if this is the case
		if (!BaseApi::checkDbCache($scope, $api, $ref_types->cached_until)) {

			// Update the Database while we loop over the results
			foreach ($ref_types->refTypes as $ref_type) {

				// Check if we need to update || insert
				$type_check = \EveEveRefTypes::where('refTypeID', '=', $ref_type->refTypeID)->first();
			
				if (!$type_check)
					$type_data = new \EveEveRefTypes;
				else
					$type_data = $type_check;

				// Update the system
				$type_data->refTypeID	= $ref_type->refTypeID;
				$type_data->refTypeName = $ref_type->refTypeName;
				$type_data->save();

				// Update the cached_until time in the database for this api call
				BaseApi::setDbCache($scope, $api, $ref_types->cached_until);
			}
		}
		return $ref_types;
	}
}
