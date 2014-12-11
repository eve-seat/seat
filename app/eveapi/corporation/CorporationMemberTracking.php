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

class MemberTracking extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'MemberTracking';

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

            $member_tracking = $pheal
                ->corpScope
                ->MemberTracking(array('characterID' => $characters[0], 'extended' => 1));

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
        if (!BaseApi::checkDbCache($scope, $api, $member_tracking->cached_until, $corporationID)) {


            // Get a list of the current corporation members. Once we are done with the member
            // updates, we will just go and delete the members that are left in this array, assuming
            // that they are  no longer in this corporation.
            $existing_members = array();
            foreach (\EveCorporationMemberTracking::where('corporationID', $corporationID)->get() as $member)
                $existing_members[] = $member->characterID;

            // Flip the array so that the keys are the characterID's
            $existing_members = array_flip($existing_members);

            // Process the results from the API call
            foreach ($member_tracking->members as $member) {

                $member_data = \EveCorporationMemberTracking::where('characterID', '=', $member->characterID)
                    ->where('corporationID', '=', $corporationID)
                    ->first();

                if (!$member_data)
                    $member_data = new \EveCorporationMemberTracking;

                $member_data->characterID = $member->characterID;
                $member_data->corporationID = $corporationID;
                $member_data->name = $member->name;
                $member_data->startDateTime = $member->startDateTime;
                $member_data->baseID = $member->baseID;
                $member_data->base = $member->base;
                $member_data->title = $member->title;
                $member_data->logonDateTime = $member->logonDateTime;
                $member_data->logoffDateTime = $member->logoffDateTime;
                $member_data->locationID = $member->locationID;
                $member_data->location = $member->location;
                $member_data->shipTypeID = $member->shipTypeID;
                $member_data->shipType = $member->shipType;
                $member_data->roles = $member->roles;
                $member_data->grantableRoles = $member->grantableRoles;

                $member_data->save();

                // Remove this member from the existing members
                unset($existing_members[$member->characterID]);
            }

            // Next, remove the members that were not in the API call.
            if (count($existing_members) > 0)
                \EveCorporationMemberTracking::whereIn('characterID', array_keys($existing_members))->delete();

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $member_tracking->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $member_tracking;
    }
}
