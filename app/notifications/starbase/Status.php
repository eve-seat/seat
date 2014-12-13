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
        $stabases = \EveCorporationStarbaseDetail::all();

        $state_needed = array();

        // Looping over the starbases, check what the status of it.
        // Currently we only send a notification if it went
        // offline or has been re-inforced
        foreach($stabases as $starbase) {

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

                        // We will make sure that we don't spam the user
                        // with notifications, so lets hash the event
                        // and ensure that its not present in the
                        // database yet

                        // Prepare the total notification
                        $notification_type = 'Starbase';
                        $notification_title = 'Dangerous Status';
                        $notification_text = 'One of your starbases is: ' . $starbase_status;

                        // Send the notification
                        BaseNotify::sendNotification($pos_user->id, $notification_type, $notification_title, $notification_text);

                    }
                }
            }
        }
    }
}
