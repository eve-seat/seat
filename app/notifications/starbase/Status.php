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

        $stateNeeded = array();

        foreach($stabases as $starbase) {

            if($starbase->state == 1)
                $stateNeeded = array_add($stateNeeded, $starbase->id, array("Anchored / Offline", $starbase->corporationID));

            elseif($starbase->state == 3)
                $stateNeeded = array_add($stateNeeded, $starbase->id, array("Reinforced", $starbase->corporationID));

        }

        $posUsers = \Sentry::findAllUsersWithAccess('pos_manager');

        if(!empty($stateNeeded)) {
            foreach($stateNeeded as $posNeedsChecked => $posNeedsCheckedData) {
                foreach($posUsers as $posUser) {
                    if(BaseNotify::canAccessCorp($posUser->id, $posNeedsCheckedData[1])) {
                        $notification = new \SeatNotification;
                        $notification->user_id = $posUser->id;
                        $notification->type = "POS";
                        $notification->title = "POS Status!";
                        $notification->text = "One of your starbases has the following status: ".$posNeedsCheckedData[0];
                        $notification->save();
                    }
                }
            }
        }
    }
}
