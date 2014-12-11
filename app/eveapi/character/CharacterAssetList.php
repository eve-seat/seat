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

class AssetList extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Char';
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

        // Next, start our loop over the characters and upate the database
        foreach ($characters as $characterID) {

            // Prepare the Pheal instance
            $pheal = new Pheal($keyID, $vCode);

            // Do the actual API call. pheal-ng actually handles some internal
            // caching too.
            try {

                $asset_list = $pheal
                    ->charScope
                    ->AssetList(array('characterID' => $characterID));

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
            if (!BaseApi::checkDbCache($scope, $api, $asset_list->cached_until, $characterID)) {

                // TODO: Look as how we update this. As per https://neweden-dev.com/Character/Asset_List, the itemID
                // may change. So, we cant really just update existing ids. For now, we just trash all and recreate the
                // assets :<
                // Maybe do this in one big transaction? lel

                \EveCharacterAssetList::where('characterID', '=', $characterID)->delete();
                \EveCharacterAssetListContents::where('characterID', '=', $characterID)->delete();

                // Populate the assets for this character as well as the contents.
                foreach ($asset_list->assets as $asset) {

                    $asset_data = new \EveCharacterAssetList;

                    $asset_data->characterID = $characterID;
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

                            $content_data = new \EveCharacterAssetListContents;

                            $content_data->characterID = $characterID;
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

                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $asset_list->cached_until, $characterID);
            }
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $asset_list;
    }
}
