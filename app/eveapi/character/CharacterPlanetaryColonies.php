<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class PlanetaryColonies extends BaseApi
{

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

                // Process each of the colonies as reported by the API. We will keep a note of the
                // planetID's that we have, and delete the ones that are not in this list
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

                    // Check if the pin check is banned
                    if (!BaseApi::isLockedCall('PlanetaryPins', $scope, $characterID)) {

                        // First, the pins
                        try {

                            $planetary_pins = $pheal
                                ->charScope
                                ->PlanetaryPins(array('characterID' => $characterID, 'planetID' => $colony->planetID));

                        } catch (\Pheal\Exceptions\APIException $e) {

                            // Process a ban for this call. Both the call and id is changed
                            // from $api->'custom' && $keyID -> $characterID
                            BaseApi::banCall('PlanetaryPins', $scope, $characterID, 0, $e->getCode() . ': ' . $e->getMessage());
                            return;

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

                        // Remove the old pins
                        if (count($known_pins) > 0)
                            \EveCharacterPlanetaryPins::where('characterID', $characterID)
                                ->where('planetID', $colony->planetID)
                                ->whereNotIn('pinID', $known_pins)
                                ->delete();
                    }

                    // Next, the routes
                    if (!BaseApi::isLockedCall('PlanetaryRoutes', $scope, $characterID)) {

                        try {

                            $planetary_routes = $pheal
                                ->charScope
                                ->PlanetaryRoutes(array('characterID' => $characterID, 'planetID' => $colony->planetID));

                        } catch (\Pheal\Exceptions\APIException $e) {

                            // Process a ban for this call. Both the call and id is changed
                            // from $api->'custom' && $keyID -> $characterID
                            BaseApi::banCall('PlanetaryRoutes', $scope, $characterID, 0, $e->getCode() . ': ' . $e->getMessage());
                            return;

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

                        // Remove the old routes
                        if (count($known_routes) > 0)
                            \EveCharacterPlanetaryRoutes::where('characterID', $characterID)
                                ->where('planetID', $colony->planetID)
                                ->whereNotIn('routeID', $known_routes)
                                ->delete();
                    }


                    // Lastly the links information
                    if (!BaseApi::isLockedCall('PlanetaryRoutes', $scope, $characterID)) {

                        try {

                            $planetary_links = $pheal
                                ->charScope
                                ->PlanetaryLinks(array('characterID' => $characterID, 'planetID' => $colony->planetID));

                        } catch (\Pheal\Exceptions\APIException $e) {

                            // Process a ban for this call. Both the call and id is changed
                            // from $api->'custom' && $keyID -> $characterID
                            BaseApi::banCall('PlanetaryLinks', $scope, $characterID, 0, $e->getCode() . ': ' . $e->getMessage());
                            return;

                        } catch (\Pheal\Exceptions\PhealException $e) {

                            // Assuming the API has already been available, we will throw on any error here as
                            // technically we should never hit one if the PlanetaryColonies call worked
                            throw $e;
                        }

                        // TODO: The logic for keeping the links up to date needs some more thinking as
                        // it does not have a unique 'linkID' or something that can be referenced.
                        // For now I am just going to drop all the links for this planet and recreate
                        // them all.
                        \EveCharacterPlanetaryLinks::where('characterID', $characterID)
                            ->where('planetID', $colony->planetID)
                            ->delete();

                        // Process the links from the API for this colony
                        foreach ($planetary_links->links as $link) {

                            $link_data = new \EveCharacterPlanetaryLinks;

                            $link_data->characterID = $characterID;
                            $link_data->planetID = $colony->planetID;
                            $link_data->sourcePinID = $link->sourcePinID;
                            $link_data->destinationPinID = $link->destinationPinID;
                            $link_data->linkLevel = $link->linkLevel;
                            $link_data->save();

                        }
                    }
                }
            }

            // Remove the old colonies that were recorded and are no longer present
            if (count($known_planets) > 0)
                \EveCharacterPlanetaryColonies::where('characterID', $characterID)
                    ->whereNotIn('planetID', $known_planets)
                    ->delete();
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $planetary_colonies;
    }
}
