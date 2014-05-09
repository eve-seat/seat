<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class PlanetaryColonies extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
		$api = 'PlanetaryColonies';

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
				
				$planetary_colonies = $pheal
					->charScope
					->PlanetaryColonies(array('characterID' => $characterID));

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
			if (!BaseApi::checkDbCache($scope, $api, $planetary_colonies->cached_until, $characterID)) {

				// We start by declaring some empty arrays so that we can use them to cleanup things
				// that is no longer in the API. Things like route changes, colony moves etc all
				// contribute to things that should be removed.
				$known_planets = array();
				$known_pins = array();
				$known_routes = array();
				$known_links = array();

				// Process each of the colonies as reported by the API. We will keep a note of the
				// planetID's that we have, and delete the ones that are not in this list
				$known_planets = array();
				foreach ($planetary_colonies->colonies as $colony) {

					// Check the database and prepare a new object if needed
					$colony_data = \EveCharacterPlanetaryColonies::where('characterID', $characterID)
						->where('planetID', $colony->planetID)
						->first();

					if (!$colony_data)
						$colony_data = new \EveCharacterPlanetaryColonies;

					$colony_data->characterID = $characterID;
					$colony_data->characterName = $colony->ownerName;
					$colony_data->solarSystemID = $colony->solarSystemID;
					$colony_data->solarSystemName = $colony->solarSystemName;
					$colony_data->planetID = $colony->planetID;
					$colony_data->planetName = $colony->planetName;
					$colony_data->planetTypeID = $colony->planetTypeID;
					$colony_data->planetTypeName = $colony->planetTypeName;
					$colony_data->lastUpdate = $colony->lastUpdate;
					$colony_data->upgradeLevel = $colony->upgradeLevel;
					$colony_data->numberOfPins = $colony->numberOfPins;
					$colony_data->save();

					// Add this as a known planetID. Colonies that are not in this array will be
					// deleted at the end of this function
					$known_planets[] = $colony->planetID;

					// Next we process the pins, links and routes for this planetID
					//

					// First, the pins
					try {
						
						$planetary_pins = $pheal
							->charScope
							->PlanetaryPins(array('characterID' => $characterID, 'planetID' => $colony->planetID));

					} catch (\Pheal\Exceptions\PhealException $e) {

						// Assuming the API has already been available, we will throw on any error here as
						// technically we should never hit one if the PlanetaryColonies call worked
						throw $e;
					}

					// Process the pins from the API for this planet
					foreach ($planetary_pins->pins as $pin) {

						// Find the pin in the database or create a new one
						$pin_data = \EveCharacterPlanetaryPins::where('characterID', $characterID)
							->where('planetID', $colony->planetID)
							->where('pinID', $pin->pinID)
							->first();

						if (!$pin_data)
							$pin_data = new \EveCharacterPlanetaryPins;

						$pin_data->characterID = $characterID;
						$pin_data->planetID = $colony->planetID;
						$pin_data->pinID = $pin->pinID;
						$pin_data->typeID = $pin->typeID;
						$pin_data->typeName = $pin->typeName;
						$pin_data->schematicID = $pin->schematicID;
						$pin_data->lastLaunchTime = $pin->lastLaunchTime;
						$pin_data->cycleTime = $pin->cycleTime;
						$pin_data->quantityPerCycle = $pin->quantityPerCycle;
						$pin_data->installTime = $pin->installTime;
						$pin_data->expiryTime = $pin->expiryTime;
						$pin_data->contentTypeID = $pin->contentTypeID;
						$pin_data->contentTypeName = $pin->contentTypeName;
						$pin_data->contentQuantity = $pin->contentQuantity;
						$pin_data->longitude = $pin->longitude;
						$pin_data->latitude = $pin->latitude;
						$pin_data->save();

						// Add this pin to the known pins
						$known_pins[] = $pin->pinID;
					}

					//TODO: Cleanup old pins for this planet

					// Next, the routes
					try {
						
						$planetary_routes = $pheal
							->charScope
							->PlanetaryRoutes(array('characterID' => $characterID, 'planetID' => $colony->planetID));

					} catch (\Pheal\Exceptions\PhealException $e) {

						// Assuming the API has already been available, we will throw on any error here as
						// technically we should never hit one if the PlanetaryColonies call worked
						throw $e;
					}

					// Process the pins from the API for this planet
					foreach ($planetary_routes->routes as $route) {

						// Find the pin in the database or create a new one
						$route_data = \EveCharacterPlanetaryRoutes::where('characterID', $characterID)
							->where('planetID', $colony->planetID)
							->where('routeID', $route->routeID)
							->first();

						if (!$route_data)
							$route_data = new \EveCharacterPlanetaryRoutes;

						$route_data->characterID = $characterID;
						$route_data->planetID = $colony->planetID;
						$route_data->routeID = $route->routeID;
						$route_data->sourcePinID = $route->sourcePinID;
						$route_data->destinationPinID = $route->destinationPinID;
						$route_data->contentTypeID = $route->contentTypeID;
						$route_data->contentTypeName = $route->contentTypeName;
						$route_data->quantity = $route->quantity;
						$route_data->waypoint1 = $route->waypoint1;
						$route_data->waypoint2 = $route->waypoint2;
						$route_data->waypoint3 = $route->waypoint3;
						$route_data->waypoint4 = $route->waypoint4;
						$route_data->waypoint5 = $route->waypoint5;
						$route_data->save();

						// Add this route to the known routes
						$known_routes[] = $route->routeID;
					}

					//TODO: Cleanup old routes

					// Lastly the links information
					try {
						
						$planetary_links = $pheal
							->charScope
							->PlanetaryLinks(array('characterID' => $characterID, 'planetID' => $colony->planetID));

					} catch (\Pheal\Exceptions\PhealException $e) {

						// Assuming the API has already been available, we will throw on any error here as
						// technically we should never hit one if the PlanetaryColonies call worked
						throw $e;
					}

					// Process the pins from the API for this planet
					foreach ($planetary_links->links as $link) {

						// Find the pin in the database or create a new one
						$link_data = \EveCharacterPlanetaryLinks::where('characterID', $characterID)
							->where('planetID', $colony->planetID)
							->where('sourcePinID', $link->sourcePinID)
							->where('destinationPinID', $link->destinationPinID)
							->first();

						if (!$link_data)
							$link_data = new \EveCharacterPlanetarylinks;

						$link_data->characterID = $characterID;
						$link_data->planetID = $colony->planetID;
						$link_data->sourcePinID = $link->sourcePinID;
						$link_data->destinationPinID = $link->destinationPinID;
						$link_data->linkLevel = $link->linkLevel;
						$link_data->save();

						// Add this link to the known links
						$known_links[] = array($link->sourcePinID, $link->destinationPinID);
					}

					// TODO: Cleanup Old Links

				}

			}

			// TODO: Cleanup colonies
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $planetary_colonies;
	}
}
