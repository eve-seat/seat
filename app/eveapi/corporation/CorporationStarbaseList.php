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

class StarbaseList extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'StarbaseList';

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

            $starbase_list = $pheal
                ->corpScope
                ->StarbaseList(array('characterID' => $characters[0]));

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
        if (!BaseApi::checkDbCache($scope, $api, $starbase_list->cached_until, $corporationID)) {


            // So for this call I dont think we should just trash all of the posses and their
            // details. Instead, cause I'm bad, well get all of the current posses for the corp
            // and delete the values that we know of. The resulting array will be the ones we delete
            // as they are probably removed/killed posses
            $old_starbases = array();
            foreach (\EveCorporationStarbaseList::where('corporationID', '=', $corporationID)->get() as $item)
                $old_starbases[] = $item->itemID;

            // Arrayflip hax to get the starbaseID's as keys
            $old_starbases = array_flip($old_starbases);    // <-- help a noob please :<

            // Next, loop over the starbases from the API and populate/update the db
            foreach ($starbase_list->starbases as $starbase) {

                $starbase_data = \EveCorporationStarbaseList::where('corporationID', '=', $corporationID)
                    ->where('itemID', '=', $starbase->itemID)
                    ->first();

                if (!$starbase_data)
                    $starbase_data = new \EveCorporationStarbaseList;

                $starbase_data->corporationID = $corporationID;
                $starbase_data->itemID = $starbase->itemID;
                $starbase_data->typeID = $starbase->typeID;
                $starbase_data->locationID = $starbase->locationID;
                $starbase_data->moonID = $starbase->moonID;
                $starbase_data->state = $starbase->state;
                $starbase_data->stateTimestamp = $starbase->stateTimestamp;
                $starbase_data->onlineTimestamp = $starbase->onlineTimestamp;
                $starbase_data->standingOwnerID = $starbase->standingOwnerID;
                $starbase_data->save();

                // Update the old_starbases list by removing the ones that still
                // exist
                if (array_key_exists($starbase->itemID, $old_starbases))
                    unset($old_starbases[$starbase->itemID]);
            }

            // Delete old starbases if there are any
            if (count($old_starbases) > 0) {

                // Delete the old starbase...
                foreach (array_flip($old_starbases) as $starbase_id)
                    \EveCorporationStarbaseList::where('itemID', '=', $starbase_id)->delete();

                // ... and its details
                foreach (array_flip($old_starbases) as $starbase_id)
                    \EveCorporationStarbaseDetail::where('itemID', '=', $starbase_id)->delete();
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $starbase_list->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $starbase_list;
    }
}
