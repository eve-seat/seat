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

    /*
    |--------------------------------------------------------------------------
    | canAccessCorp()
    |--------------------------------------------------------------------------
    |
    | Takes a SeAT userID and a corporationID, and checks if there is an
    | affiliation between the 2. Generally this means that the user
    | has a API key they they own, which declares them to be
    | 'affiliated' with that corporation
    |
    */
    public static function canAccessCorp($userID, $corpID)
    {

        // Get the SeAT user based on the userID
        $user = \Auth::findUserById($userID);

        // Determine the keys that the user owns
        $valid_keys = \SeatKey::where('user_id', $user->id)
            ->lists('keyID');

        // Check if we have received any keys in the
        // previous query
        if (!empty($valid_keys)) {

            // Get a list of the corporationID's that the
            // user is affaliated with
            $corporation_affiliation = \EveAccountAPIKeyInfoCharacters::whereIn('keyID', $valid_keys)
                ->groupBy('corporationID')
                ->lists('corporationID');

            // With the affiliations known, loop over them
            // matching the affaliated corporationID with
            // the corporationID in question
            foreach($corporation_affiliation as $affiliation)

                // If we have a match, return true
                if($affiliation == $corpID)
                    return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | makeNotificationHash()
    |--------------------------------------------------------------------------
    |
    | Generate a MD5 hash based on the 3 received arguements for caching related
    | information in the database.
    |
    */

    public static function makeNotificationHash($userID, $type, $title, $text)
    {
        return md5(implode(',', array($userID, $type, $title, $text)));
    }

    /*
    |--------------------------------------------------------------------------
    | sendNotification()
    |--------------------------------------------------------------------------
    |
    | Check the existence of a notification. If it does not exist, send
    | a new one to $user_id, saving the hash for future lookups
    |
    */

    public static function sendNotification($user_id, $type, $title, $text)
    {

        // Generate a hash to lookup
        $hash = BaseNotify::makeNotificationHash($user_id, $type, $title, $text);

        // Check the existance of the hash in the notifications table
        if(!\SeatNotification::where('hash', '=', $hash)->exists()) {

            // Send the new noticiation
            $notification = new \SeatNotification;
            $notification->user_id = $user_id;
            $notification->type = $type;
            $notification->title = $title;
            $notification->text = $text;
            $notification->hash = $hash;
            $notification->save();

            return true;

        }

        return false;
    }

}
