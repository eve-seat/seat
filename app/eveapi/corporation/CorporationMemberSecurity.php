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

class MemberSecurity extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'MemberSecurity';

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

            $member_security = $pheal
                ->corpScope
                ->MemberSecurity(array('characterID' => $characters[0]));

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
        if (!BaseApi::checkDbCache($scope, $api, $member_security->cached_until, $corporationID)) {

            foreach ($member_security->members as $security) {

                // Update order:
                // a) roles
                // b) grantableRoles
                // c) rolesAtHQ
                // d) grantableRolesAtHQ
                // e) rolesAtBase
                // f) grantableRolesAtBase
                // g) rolesAtOther
                // h) grantableRolesAtOther
                // i) titles
                //
                // The roles get deleted, and re-inserted based on the API response. Pretty shit way of
                // doing it I guess :<

                // a) Roles Update
                \EveCorporationMemberSecurityRoles::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->roles as $role) {

                    $roles_data = new \EveCorporationMemberSecurityRoles;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }


                // b) grantableRoles Update
                \EveCorporationMemberSecurityGrantableRoles::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->grantableRoles as $role) {

                    $roles_data = new \EveCorporationMemberSecurityGrantableRoles;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // c) rolesAtHQ Update
                \EveCorporationMemberSecurityRolesAtHQ::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->rolesAtHQ as $role) {

                    $roles_data = new \EveCorporationMemberSecurityRolesAtHQ;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // d) grantableRolesAtHQ Update
                \EveCorporationMemberSecurityGrantableRolesAtHQ::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->grantableRolesAtHQ as $role) {

                    $roles_data = new \EveCorporationMemberSecurityGrantableRolesAtHQ;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // e) rolesAtBase Update
                \EveCorporationMemberSecurityRolesAtBase::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->rolesAtBase as $role) {

                    $roles_data = new \EveCorporationMemberSecurityRolesAtBase;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // f) grantableRolesAtBase Update
                \EveCorporationMemberSecurityGrantableRolesAtBase::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->grantableRolesAtBase as $role) {

                    $roles_data = new \EveCorporationMemberSecurityGrantableRolesAtBase;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // g) rolesAtOther Update
                \EveCorporationMemberSecurityRolesAtOther::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->rolesAtOther as $role) {

                    $roles_data = new \EveCorporationMemberSecurityRolesAtOther;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // h) grantableRolesAtOther Update
                \EveCorporationMemberSecurityGrantableRolesAtOther::where('characterID', '=', $security->characterID)
                    ->delete();

                foreach ($security->grantableRolesAtOther as $role) {

                    $roles_data = new \EveCorporationMemberSecurityGrantableRolesAtOther;

                    $roles_data->characterID = $security->characterID;
                    $roles_data->corporationID = $corporationID;
                    $roles_data->name = $security->name;
                    $roles_data->roleID = $role->roleID;
                    $roles_data->roleName = $role->roleName;

                    $roles_data->save();
                }

                // i) titles Update

                // DUST characters seem to not have the titles attributes in their XML's coming
                // from the eveapi. So lets first check if the element exists before we attempt
                // to update it with the new data
                if (isset($security->titles)) {

                    \EveCorporationMemberSecurityTitles::where('characterID', '=', $security->characterID)
                        ->delete();

                    foreach ($security->titles as $role) {

                        $roles_data = new \EveCorporationMemberSecurityTitles;

                        $roles_data->characterID = $security->characterID;
                        $roles_data->corporationID = $corporationID;
                        $roles_data->name = $security->name;
                        $roles_data->titleID = $role->titleID;
                        $roles_data->titleName = $role->titleName;

                        $roles_data->save();
                    }
                }
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $member_security->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $member_security;
    }
}
