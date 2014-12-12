<?php

namespace Seat\Notifications;

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| BaseNotify
|--------------------------------------------------------------------------
|
| The base notifications class, with some helpful builders included
| All notification classes should extend this class!
|
*/

class BaseNotify {

    public static function canAccessCorp($userID, $corpID)
    {
        $user = \Sentry::findUserById($userID);

        $valid_keys = \SeatKey::where('user_id', $user->id)
            ->lists('keyID');

        // Affiliated corporationID's.
        if (!empty($valid_keys)) {
            // Get the list of corporationID's that the user is affiliated with
            $corporation_affiliation = \EveAccountAPIKeyInfoCharacters::whereIn('keyID', $valid_keys)
                ->groupBy('corporationID')
                ->lists('corporationID');
        }

        foreach($corporation_affiliation as $affiliation){
            if($affiliation == $corpID)
                return true;
        }
        return false;
    }

}