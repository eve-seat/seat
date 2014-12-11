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

namespace Seat\EveApi\Server;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class ServerStatus extends BaseApi
{

    public static function Update()
    {
        BaseApi::bootstrap();

        $scope = 'Server';
        $api = 'ServerStatus';

        // Lock the call so that we are the only instance of this running now()
        // If it is already locked, just return without doing anything
        if (!BaseApi::isLockedCall($api, $scope))
            $lockhash = BaseApi::lockCall($api, $scope);
        else
            return;

        // Do the call
        $pheal = new Pheal();
        try {

            $server_status = $pheal
                ->serverScope
                ->ServerStatus();

        } catch (Exception $e) {

            throw $e;
        }

        if (!BaseApi::checkDbCache($scope, $api, $server_status->cached_until)) {

        // Update the Database
        $existing_status = \EveServerServerStatus::find(1);

            if (isset($existing_status)) {

                // Update the ServerStatus
                $existing_status->currentTime = $server_status->request_time;
                $existing_status->serverOpen = $server_status->serverOpen;
                $existing_status->onlinePlayers = $server_status->onlinePlayers;
                $existing_status->save();

            } else {

                // Create a ServerStatus entry
                \EveServerServerStatus::create(array(
                    'currentTime' => $server_status->request_time,
                    'serverOpen' => $server_status->serverOpen,
                    'onlinePlayers' => $server_status->onlinePlayers
                ));
            }

            // Update the cached_until time in the database for this api call
            BaseApi::setDbCache($scope, $api, $server_status->cached_until);
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $server_status;
    }
}
