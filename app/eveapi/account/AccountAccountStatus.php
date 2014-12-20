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

namespace Seat\EveApi\Account;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class AccountStatus extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Account';
        $api = 'AccountStatus';

        if (BaseApi::isBannedCall($api, $scope, $keyID))
            return;

        // Prepare the Pheal instance
        $pheal = new Pheal($keyID, $vCode);

        // Need to check that this is not a Corporation Key...

        // Do the actual API call. pheal-ng actually handles some internal
        // caching too.
        try {

            $status_info = $pheal
                ->accountScope
                ->AccountStatus();

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
        if (!BaseApi::checkDbCache($scope, $api, $status_info->cached_until, $keyID)) {

            $check_status = \EveAccountAccountStatus::where('keyID', '=', $keyID)->first();

            if (!$check_status)
                $status_data = new \EveAccountAccountStatus;
            else
                $status_data = $check_status;

            $status_data->keyID = $keyID;
            $status_data->paidUntil = $status_info->paidUntil;
            $status_data->createDate = $status_info->createDate;
            $status_data->logonCount = $status_info->logonCount;
            $status_data->logonMinutes = $status_info->logonMinutes;
            $status_data->save();

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $status_info->cached_until, $keyID);
        }
        return $status_info;
    }
}
