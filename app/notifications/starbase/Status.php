<?php

namespace Seat\Notifications\Starbase;

use Seat\Notifications\BaseNotify;

class StarbaseStatus extends BaseNotify {

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