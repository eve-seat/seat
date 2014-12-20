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

class AllianceList extends BaseApi
{

    public static function Update()
    {
        BaseApi::bootstrap();

        $scope = 'Eve';
        $api = 'AllianceList';

        // Prepare the Pheal instance
        $pheal = new Pheal();

        // Do the actual API call. pheal-ng actually handles some internal
        // caching too.
        try {

            $alliance_list = $pheal
                ->eveScope
                ->AllianceList();

        } catch (Exception $e) {

            throw $e;
        }

        // Check if the data in the database is still considered up to date.
        // checkDbCache will return true if this is the case
        if (!BaseApi::checkDbCache($scope, $api, $alliance_list->cached_until)) {

            // Really crappy method to do this I guess. But oh well.
            \EveEveAllianceListMemberCorporations::truncate();

            foreach ($alliance_list->alliances as $alliance) {

                $alliance_check = \EveEveAllianceList::where('allianceID', '=', $alliance->allianceID)->first();

                if (!$alliance_check)
                    $alliance_data = new \EveEveAllianceList;
                else
                    $alliance_data = $alliance_check;

                $alliance_data->name = $alliance->name;
                $alliance_data->shortName = $alliance->shortName;
                $alliance_data->allianceID = $alliance->allianceID;
                $alliance_data->executorCorpID = $alliance->executorCorpID;
                $alliance_data->memberCount = $alliance->memberCount;
                $alliance_data->startDate = $alliance->startDate;
                $alliance_data->save();

                $alliance_data->save();

                // And repopulate the current members
                foreach ($alliance->memberCorporations as $corporation) {

                    $member_corp = new \EveEveAllianceListMemberCorporations;
                    $member_corp->corporationID = $corporation->corporationID;
                    $member_corp->startDate = $corporation->startDate;
                    $alliance_data->members()->save($member_corp);
                }
            }

            // Set the cached entry time
            BaseApi::setDbCache($scope, $api, $alliance_list->cached_until);
        }

        return $alliance_list;
    }
}
