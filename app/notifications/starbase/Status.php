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

class StarbaseStatus extends BaseNotify
{

    public static function update()
    {
        $stabases = \EveCorporationStarbaseDetail::all();

        $state_needed = array();

        foreach($stabases as $starbase) {

            if($starbase->state == 1)
                $state_needed = array_add($state_needed, $starbase->id, array("Anchored / Offline", $starbase->corporationID));

            elseif($starbase->state == 3)
                $state_needed = array_add($state_needed, $starbase->id, array("Reinforced", $starbase->corporationID));

        }

        $pos_users = \Sentry::findAllUsersWithAccess('pos_manager');

        if(!empty($state_needed)) {
            foreach($state_needed as $pos_needs_checked => $pos_needs_checked_data) {
                foreach($pos_users as $pos_user) {
                    if(BaseNotify::canAccessCorp($pos_user->id, $pos_needs_checked_data[1])) {
                        $notification_type = "POS";
                        $notification_title = "Low Fuel!";
                        $notification_text = "One of your starbases has the following status: ".$pos_needs_checked_data[0];
                        $hash = BaseNotify::makeNotificationHash($super_user->id, $notification_type, $notification_title, $notification_text);

                        $check = \SeatNotification::where('hash', '=', $hash)->exists();

                        if(!$check) {

                            $notification = new \SeatNotification;
                            $notification->user_id = $pos_user->id;
                            $notification->type = $notification_type;
                            $notification->title = $notification_title;
                            $notification->text = $notification_text;
                            $notification->hash = $hash;
                            $notification->save();
                        }
                    }
                }
            }
        }
    }
}
