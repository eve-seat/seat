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

class MemberSecurityLog extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'MemberSecurityLog';

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

            $member_security_log = $pheal
                ->corpScope
                ->MemberSecurityLog(array('characterID' => $characters[0]));

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
        if (!BaseApi::checkDbCache($scope, $api, $member_security_log->cached_until, $corporationID)) {

            foreach ($member_security_log->roleHistory as $log) {

                // Generate a log hash to lookup
                $loghash = md5(implode(',', array($log->changeTime, $log->characterID, $log->roleLocationType)));

                $log_data = \EveCorporationMemberSecurityLog::where('hash', '=', $loghash)
                    ->first();

                if (!$log_data)
                    $log_data = new \EveCorporationMemberSecurityLog;
                else
                    // We already have this log entry recorded, so just move along
                    continue;

                // Record the log entry
                $log_data->corporationID = $corporationID;
                $log_data->characterID = $log->characterID;
                $log_data->characterName = $log->characterName;
                $log_data->changeTime = $log->changeTime;
                $log_data->issuerID = $log->issuerID;
                $log_data->issuerName = $log->issuerName;
                $log_data->roleLocationType = $log->roleLocationType;
                $log_data->hash = $loghash;
                $log_data->save();

                // Generate the oldRoles & newRoles entries. Well just make some
                // lazy ass json entries.
                $oldRoles = array();
                foreach ($log->oldRoles as $entry)
                    $oldRoles[$entry->roleID] = $entry->roleName;

                $newRoles = array();
                foreach ($log->newRoles as $entry)
                    $newRoles[$entry->roleID] = $entry->roleName;

                // Save the log details
                $entry_data = new \EveCorporationMemberSecurityLogDetails;
                $entry_data->hash = $loghash;
                $entry_data->oldRoles = json_encode($oldRoles);
                $entry_data->newRoles = json_encode($newRoles);
                $log_data->details()->save($entry_data);
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $member_security_log->cached_until, $corporationID);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $member_security_log;
    }
}
