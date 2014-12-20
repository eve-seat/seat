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

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class CustomsOffices extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'CustomsOffices';

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

            $customsoffices_list = $pheal
                ->corpScope
                ->CustomsOffices(array('characterID' => $characters[0]));

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
        if (!BaseApi::checkDbCache($scope, $api, $customsoffices_list->cached_until, $corporationID)) {

            // TODO: Look as how we update this. As per https://neweden-dev.com/Character/customsoffices_list, the itemID
            // may change. So, we cant really just update existing ids. For now, we just trash all and recreate the
            // assets :<
            // Maybe do this in one big transaction? lel

            \EveCorporationCustomsOffices::where('corporationID', '=', $corporationID)->delete();

            // Note about /corp/Locations
            //
            // We will build a list of itemID's to update the location information about
            // while we loop over the assets. This will be stored in location_retreive and
            // processed here [1]
            $location_retreive = array();

            // Populate the assets for this corporation as well as the contents.
            foreach ($customsoffices_list->pocos as $poco) {

                // Add the asset to the location retreival array
                $location_retreive[] = $poco->itemID;

                // Process the rest of the database population
                $poco_data = new \EveCorporationCustomsOffices;

                $poco_data->corporationID = $corporationID;
                $poco_data->itemID = $poco->itemID;
                $poco_data->solarSystemID = $poco->solarSystemID;
                $poco_data->solarSystemName = $poco->solarSystemName;
                $poco_data->reinforceHour = $poco->reinforceHour;
                $poco_data->allowAlliance = ($poco->allowAlliance == "True" ? true : false);
                $poco_data->allowStandings = ($poco->allowStandings == "True" ? true : false);
                $poco_data->standingLevel = $poco->standingLevel;
                $poco_data->taxRateAlliance = $poco->taxRateAlliance;
                $poco_data->taxRateCorp = $poco->taxRateCorp;
                $poco_data->taxRateStandingHigh = $poco->taxRateStandingHigh;
                $poco_data->taxRateStandingGood = $poco->taxRateStandingGood;
                $poco_data->taxRateStandingNeutral = $poco->taxRateStandingNeutral;
                $poco_data->taxRateStandingBad = $poco->taxRateStandingBad;
                $poco_data->taxRateStandingHorrible = $poco->taxRateStandingHorrible;
                $poco_data->save();

            }

            // Now empty and process the locations as per [1]
            \EveCorporationCustomsOfficesLocations::where('corporationID', '=', $corporationID)->delete();
            $location_retreive = array_chunk($location_retreive, 1);
            
            // Iterate over the chunks.
            foreach ($location_retreive as $chunk) {
            
                try {
            
                    $locations = $pheal
                        ->corpScope
                        ->Locations(array('characterID' => $characters[0], 'ids' => implode(',', $chunk)));
            
                } catch (\Pheal\Exceptions\PhealException $e) {
            
                    // Temp hack to check the asset list thingie
                    // TBH, I am not 100% sure yet why the freaking call would fail for a id we **just**
                    // got from the previous API call...
                    if ($e->getCode() == 135 || $e->getCode() == 221)   // 135 "not the owner" | 221 "illegal page request"
                        continue;
                    else
                        throw $e;
                }
            
                // Loop over the locations, check their closest celestial
                // and add the data to the database
                foreach ($locations->locations as $location) {
            
                    $closest_planet = BaseApi::findClosestPlanet($location->itemID, $location->x, $location->y, $location->z);
            
                    $location_data = new \EveCorporationCustomsOfficesLocations;
            
                    $location_data->corporationID = $corporationID;
                    $location_data->itemID = $location->itemID;
                    $location_data->itemName = $location->itemName;
                    $location_data->x = $location->x;
                    $location_data->y = $location->y;
                    $location_data->z = $location->z;
                    $location_data->mapID = $closest_planet['id'];
                    $location_data->mapName = $closest_planet['name'];
                    $location_data->save();
                }
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $customsoffices_list->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $customsoffices_list;
    }
}
