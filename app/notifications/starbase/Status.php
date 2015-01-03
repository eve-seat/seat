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
| Status Status Notification
|--------------------------------------------------------------------------
|
| This notification looks up the starbases that SeAT is aware of. Each
| starbase is then checked for it's status and notified if it if
| changed
|
*/

class Status extends BaseNotify
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

        // Next, grab the starbases that we know of
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

        $state_needed = array();

        // Looping over the starbases, check what the status of it.
        // Currently we only send a notification if it went
        // offline or has been re-inforced
        foreach($starbases as $starbase) {

            // Check the status of the starbase and
            // switch between them, updating the
            // state of the tower for a message
            $starbase_status = null;

            switch ($starbase->state) {

                case 1:
                    $starbase_status = 'Anchored / Offline';
                    break;

                case 3:
                    $starbase_status = 'Reinforced';

                default:
                    # code...
                    break;
            }

            // If the starbase was in any of the statusses
            // that we case about, attempt to find users
            // to notify
            if(!is_null($starbase_status)) {

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
                        $notification_title = 'Dangerous Status';
                        $notification_text = 'The ' . $starbase->typeName . ' at ' . $starbase->itemName .
                            ' owned by ' . $corporation_name . ' is now ' . $starbase_status;

                        // Send the notification
                        BaseNotify::sendNotification($pos_user->id, $notification_type, $notification_title, $notification_text);

                    }
                }
            }
        }
    }
}
