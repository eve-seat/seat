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

namespace Seat\EveApi\Eve;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class ConquerableStationList extends BaseApi
{

    public static function Update()
    {
        BaseApi::bootstrap();

        $scope = 'Eve';
        $api = 'ConquerableStationList';

        // Prepare the Pheal instance
        $pheal = new Pheal();

        // Do the actual API call. pheal-ng actually handles some internal
        // caching too.
        try {

            $station_list = $pheal
                ->eveScope
                ->ConquerableStationList();

        } catch (Exception $e) {

            throw $e;
        }

        // Check if the data in the database is still considered up to date.
        // checkDbCache will return true if this is the case
        if (!BaseApi::checkDbCache($scope, $api, $station_list->cached_until)) {

            // Update the Database while we loop over the results
            foreach ($station_list->outposts as $outpost) {

                // Check if we need to update || insert
                $outpost_check = \EveEveConquerableStationList::where('stationID', '=', $outpost->stationID)->first();

                if (!$outpost_check)
                    $outpost_data = new \EveEveConquerableStationList;
                else
                    $outpost_data = $outpost_check;

                $outpost_data->stationID    = $outpost->stationID;
                $outpost_data->stationName = $outpost->stationName;
                $outpost_data->stationTypeID = $outpost->stationTypeID;
                $outpost_data->solarSystemID = $outpost->solarSystemID;
                $outpost_data->corporationID = $outpost->corporationID;
                $outpost_data->corporationName = $outpost->corporationName;
                $outpost_data->save();
            }
                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $station_list->cached_until);
        }
        return $station_list;
    }
}
