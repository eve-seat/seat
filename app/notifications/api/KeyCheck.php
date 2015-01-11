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

namespace Seat\Notifications\Api;

use Seat\Notifications\BaseNotify;

/*
|--------------------------------------------------------------------------
| Disabled API Keys Notification
|--------------------------------------------------------------------------
|
| This notification looks up disabled API keys. Once a key has been
| determined as disabled, or 'isOk == 0', a notification will be
| send to a super user
|
*/

class KeyCheck extends BaseNotify
{

    public static function Update()
    {

        // Grab all of the API keys that SeAT
        // is aware of
        $seat_keys = \SeatKey::all();

        // Looping over all of the keys, we will find
        // the superusers and send them the
        // notification about this
        foreach ($seat_keys as $seat_key) {

            if($seat_key->isOk == 0) {

                // We have a key that is not OK, find
                // some people to tell about this!
                foreach(\Auth::findAllUsersWithAccess('superuser') as $super_user) {

                    // Compile the full notification
                    $notification_type = 'API';
                    $notification_title = 'Disabled Key';
                    $notification_text = 'The SeAT backend has disabled API key '. $seat_key->keyID . '. ' .
                        'The last error was: ' . $seat_key->lastError;

                    // Send the Notification
                    BaseNotify::sendNotification($super_user->id, $notification_type, $notification_title, $notification_text);

                }
            }
        }
    }
}
