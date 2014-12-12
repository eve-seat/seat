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

namespace Seat\Notifications;

/*
|--------------------------------------------------------------------------
| BaseNotify
|--------------------------------------------------------------------------
|
| The base notifications class, with some helpful builders included
| All notification classes should extend this class!
|
*/

class BaseNotify
{

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

        foreach($corporation_affiliation as $affiliation)
            if($affiliation == $corpID)
                return true;

        return false;
    }

}
