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

namespace Seat\Notifications\Starbase;

use Seat\Notifications\BaseNotify;

/*
|--------------------------------------------------------------------------
| Starbase Fuel Notification
|--------------------------------------------------------------------------
|
| This notification looks up the starbases that SeAT is aware of. Each
| starbase is then checked for it's fuel levels based on on its
| type. Once a starbase is determined as low on fuel, people
| with access to that tower will be sent a notification
| about it.
|
*/

class Fuel extends BaseNotify
{

    public static function Update()
    {

        // Before we even bother getting to the point of
        // determining which starbases would need a
        // notification to be sent, check if there
        // are any users configured that have
        // ther required roles
        $pos_role_users = \Auth::findAllUsersWithAccess('pos_manager');

        // If there are none with the role, just stop
        if(count($pos_role_users) <= 0)
            return;

        // Next, grab the list of starbases that we are aware of
        $starbases = \DB::table('corporation_starbaselist')
            ->select(
                'corporation_starbaselist.corporationID',
                'corporation_starbaselist.itemID',
                'corporation_starbaselist.moonID',
                'corporation_starbaselist.state',
                'corporation_starbaselist.stateTimeStamp',
                'corporation_starbaselist.onlineTimeStamp',
                'corporation_starbaselist.onlineTimeStamp',
                'corporation_starbasedetail.useStandingsFrom',
                'corporation_starbasedetail.onAggression',
                'corporation_starbasedetail.onCorporationWar',
                'corporation_starbasedetail.allowCorporationMembers',
                'corporation_starbasedetail.allowAllianceMembers',
                'corporation_starbasedetail.fuelBlocks',
                'corporation_starbasedetail.strontium',
                'corporation_starbasedetail.starbaseCharter',
                'invTypes.typeID',
                'invTypes.typeName',
                'mapDenormalize.itemName',
                'mapDenormalize.security',
                'invNames.itemName',
                'map_sovereignty.solarSystemName',
                'corporation_starbasedetail.updated_at'
            )
            ->join('corporation_starbasedetail', 'corporation_starbaselist.itemID', '=', 'corporation_starbasedetail.itemID')
            ->join('mapDenormalize', 'corporation_starbaselist.locationID', '=', 'mapDenormalize.itemID')
            ->join('invNames', 'corporation_starbaselist.moonID', '=', 'invNames.itemID')
            ->join('invTypes', 'corporation_starbaselist.typeID', '=', 'invTypes.typeID')
            ->leftJoin('map_sovereignty', 'corporation_starbaselist.locationID', '=', 'map_sovereignty.solarSystemID')
            ->orderBy('invNames.itemName', 'asc')
            ->get();

        // To determine the fuel needed, we need to get some
        // values out there that tell us *how* much fuel
        // certain towers need. So, we will set a base
        // amount for each of them. Should this
        // change in the future, then we can
        // simply edit the value here

        // The final usage once we have determined the tower
        // type
        $usage = 0;
        $stront_usage =0;

        // The base values for the tower sizes
        // ... fuel for non sov towers ...
        $large_usage = 40;
        $medium_usage = 20;
        $small_usage = 10;

        // ... and sov towers
        $sov_large_usage = 30;
        $sov_medium_usage = 15;
        $sov_small_usage = 8;

        // Stront usage
        $stront_large = 400;
        $stront_medium = 200;
        $stront_small = 100;

        // Now, we will loop over the starbases that we know if and determine
        // the size of the tower, along with the location of the tower.
        // Towers anchored in sov space get to use less fuel.
        //
        // The result of this loop should have $usage and $stront_usage
        // populated with the correct value for the tower in question
        foreach($starbases as $starbase) {

            // Lets check if the tower is anchored in space where the
            // owner of it holds sov.
            $sov_tower = false;

            // Get the allianceID that the corporation is a member of
            $alliance_id = \DB::table('corporation_corporationsheet')
                ->where('corporationID', $starbase->corporationID)
                ->pluck('allianceID');

            // Check if the alliance_id was actually determined. If so,
            // do the sov_towers loop, else we just set the
            if ($alliance_id) {

                // Lets see if this tower is in a sov system
                $sov_location = \DB::table('corporation_starbaselist')
                    ->whereIn('locationID', function($location) use ($alliance_id) {

                        $location->select('solarSystemID')
                            ->from('map_sovereignty')
                            ->where('factionID', 0)
                            ->where('allianceID', $alliance_id);
                    })->where('corporationID', $starbase->corporationID)
                    ->where('itemID', $starbase->itemID)
                    ->first();

                if ($sov_location)
                    $sov_tower = true;

            }

            // With the soverenity status known, move on to
            // determine the size of the tower.
            if (strpos($starbase->typeName, 'Small') !== false) {

                $stront_usage = $stront_small;
                $usage = $sov_tower ? $sov_small_usage : $small_usage;

            } elseif (strpos($starbase->typeName, 'Medium') !== false) {

                $stront_usage = $stront_medium;
                $usage = $sov_tower ? $sov_medium_usage : $medium_usage;

            } else {

                $stront_usage = $stront_large;
                $usage = $sov_tower ? $sov_large_usage : $large_usage;

            }

            // Now we finally have $usage and $stront_usage known!
            // Lets get to the part where we finally check if
            // there is enough reserves. If not, do what
            // we came for and notify someone

            // If now plus (the fuel amount devided by the usage) muliplied
            // by hours is less that 3 days, send a notification
            if(\Carbon\Carbon::now()->addHours($starbase->fuelBlocks / $usage)->lte(\Carbon\Carbon::now()->addDays(3))) {

                // OK! We need to tell someone that their towers fuel is
                // going to be running out soon!
                foreach ($pos_role_users as $pos_user) {

                    // A last check is done now to make sure we
                    // don't let everyone for every corp know
                    // about a specific corp. No, we first
                    // check that the user we want to
                    // send to has the role for that
                    // specific corp too!
                    if(BaseNotify::canAccessCorp($pos_user->id, $starbase->corporationID)) {

                        // Ok! Time to finally get to pushing the
                        // notification out! :D

                        // Get the corporation name for the notification
                        $corporation_name = \DB::table('account_apikeyinfo_characters')
                            ->where('corporationID', $starbase->corporationID)
                            ->pluck('corporationName');

                        // Prepare the total notification
                        $notification_type = 'Starbase';
                        $notification_title = 'Low Fuel Reserve';
                        $notification_text = 'The ' . $starbase->typeName . ' at ' . $starbase->itemName .
                            ' owned by ' . $corporation_name . ' has ' . $starbase->fuelBlocks .
                            ' fuel blocks left. The estimated offline time is ' .
                            \Carbon\Carbon::now()->addHours($starbase->fuelBlocks / $usage)->diffForHumans() . '.';

                        // Send the notification
                        BaseNotify::sendNotification($pos_user->id, $notification_type, $notification_title, $notification_text);

                    }
                }
            }   // End fuel left over if()

            // Check how many starbase charters are left. We
            // will have to check the security of the
            // anchored system to ensure we dont
            // spazz out and notifify about
            // 0sec towers.
            //
            // If the security status of the system the tower
            // is anchored in is > 5, check the charters
            if ($starbase->security >= 5) {

                if(Carbon\Carbon::now()->addHours($starbase->starbaseCharter / 1)->lte(Carbon\Carbon::now()->addDays(3))) {

                    // OK! We need to tell someone that their towers charters is
                    // going to be running out soon!
                    foreach ($pos_role_users as $pos_user) {

                        // A last check is done now to make sure we
                        // don't let everyone for every corp know
                        // about a specific corp. No, we first
                        // check that the user we want to
                        // send to has the role for that
                        // specific corp too!
                        if(BaseNotify::canAccessCorp($pos_user->id, $starbase->corporationID)) {

                            // Ok! Time to finally get to pushing the
                            // notification out! :D

                            // Get the corporation name for the notification
                            $corporation_name = \DB::table('account_apikeyinfo_characters')
                                ->where('corporationID', $starbase->corporationID)
                                ->pluck('corporationName');

                            // Prepare the total notification
                            $notification_type = 'Starbase';
                            $notification_title = 'Low Charter Reserve';
                            $notification_text = 'The ' . $starbase->typeName . ' at ' . $starbase->itemName .
                                ' owned by ' . $corporation_name . ' has ' . $starbase->fuelBlocks .
                                ' starbase charters left. The estimated offline time is ' .
                                \Carbon\Carbon::now()->addHours($starbase->starbaseCharter / 1)->diffForHumans() . '.';

                            // Send the notification
                            BaseNotify::sendNotification($pos_user->id, $notification_type, $notification_title, $notification_text);

                        }
                    }
                }
            }   // End charters left over if()
        }
    }
}
