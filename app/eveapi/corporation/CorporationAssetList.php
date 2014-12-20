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

class AssetList extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'AssetList';

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

            $asset_list = $pheal
                ->corpScope
                ->AssetList(array('characterID' => $characters[0]));

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
        if (!BaseApi::checkDbCache($scope, $api, $asset_list->cached_until, $corporationID)) {

            // TODO: Look as how we update this. As per https://neweden-dev.com/Character/Asset_List, the itemID
            // may change. So, we cant really just update existing ids. For now, we just trash all and recreate the
            // assets :<
            // Maybe do this in one big transaction? lel

            \EveCorporationAssetList::where('corporationID', '=', $corporationID)->delete();
            \EveCorporationAssetListContents::where('corporationID', '=', $corporationID)->delete();

            // Note about /corp/Locations
            //
            // We will build a list of itemID's to update the location information about
            // while we loop over the assets. This will be stored in location_retreive and
            // processed here [1]
            $location_retreive = array();

            // Populate the assets for this corporation as well as the contents.
            foreach ($asset_list->assets as $asset) {

                // Add the asset to the location retreival array
                $location_retreive[] = $asset->itemID;

                // Process the rest of the database population
                $asset_data = new \EveCorporationAssetList;

                $asset_data->corporationID = $corporationID;
                $asset_data->itemID = $asset->itemID;
                $asset_data->locationID = $asset->locationID;
                $asset_data->typeID = $asset->typeID;
                $asset_data->quantity = $asset->quantity;
                $asset_data->flag = $asset->flag;
                $asset_data->singleton = $asset->singleton;
                $asset_data->save();

                // Process the contents if there are any
                if (isset($asset->contents)) {

                    foreach ($asset->contents as $content) {

                        $content_data = new \EveCorporationAssetListContents;

                        $content_data->corporationID = $corporationID;
                        $content_data->itemID = $asset_data->itemID;
                        $content_data->typeID = $content->typeID;
                        $content_data->quantity = $content->quantity;
                        $content_data->flag = $content->flag;
                        $content_data->singleton = $content->singleton;
                        $content_data->rawQuantity = (isset($content->rawQuantity) ? $content->rawQuantity : 0);

                        $asset_data->contents()->save($content_data);
                    }
                }
            }

            // Now empty and process the locations as per [1]
            \EveCorporationAssetListLocations::where('corporationID', '=', $corporationID)->delete();
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

                    $closest_moon = BaseApi::findClosestMoon($location->itemID, $location->x, $location->y, $location->z);

                    $location_data = new \EveCorporationAssetListLocations;

                    $location_data->corporationID = $corporationID;
                    $location_data->itemID = $location->itemID;
                    $location_data->itemName = $location->itemName;
                    $location_data->x = $location->x;
                    $location_data->y = $location->y;
                    $location_data->z = $location->z;
                    $location_data->mapID = $closest_moon['id'];
                    $location_data->mapName = $closest_moon['name'];
                    $location_data->save();
                }
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $asset_list->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $asset_list;
    }
}
