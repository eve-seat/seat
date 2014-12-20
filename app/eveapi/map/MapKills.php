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

namespace Seat\EveApi\Map;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Kills extends BaseApi
{

    public static function Update()
    {
        BaseApi::bootstrap();

        $scope = 'Map';
        $api = 'Kills';

        // Prepare the Pheal instance
        $pheal = new Pheal();

        // Do the actual API call. pheal-ng actually handles some internal
        // caching too.
        try {

            $kills = $pheal
                ->mapScope
                ->Kills();

        } catch (Exception $e) {

            throw $e;
        }

        // Check if the data in the database is still considered up to date.
        // checkDbCache will return true if this is the case
        if (!BaseApi::checkDbCache($scope, $api, $kills->cached_until)) {

            // Update the Database while we loop over the solarSystem results
            foreach ($kills->solarSystems as $solarSystem) {

                // Find the system to check if we need to update || insert
                $system = \EveMapKills::where('solarSystemID', '=', $solarSystem->solarSystemID)->first();

                if (!$system)
                    $system_data = new \EveMapKills;
                else
                    $system_data = $system;

                // Update the system
                $system_data->solarSystemID = $solarSystem->solarSystemID;
                $system_data->shipKills     = $solarSystem->shipKills;
                $system_data->factionKills  = $solarSystem->factionKills;
                $system_data->podKills      = $solarSystem->podKills;
                $system_data->save();
            }
                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $kills->cached_until);

        }
        return $kills;
    }
}
