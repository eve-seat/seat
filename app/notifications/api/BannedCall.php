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
| Banned API Calls Notification
|--------------------------------------------------------------------------
|
| This notification looks up the banned calls tied to API keys. Once
| a key has received a banned call a notification will be sent.
|
*/

class BannedCall extends BaseNotify
{

    public static function Update()
    {

        // Learn about all of the banned calls that
        // we are aware of
        $banned_calls = \EveBannedCall::all();

        // For every banned call that we have, prepare
        // a notification to send
        foreach($banned_calls as $banned_call) {

            // Super users will be receiving this notification,
            // so loop over the current superusers that we
            // have
            foreach(\Auth::findAllUsersWithAccess('superuser') as $super_user) {

                // Compile the full notification
                $notification_type = 'API';
                $notification_title = 'Banned Call';
                $notification_text = 'The SeAT backend has banned the ' . $banned_call->scope . '\\'. $banned_call->api .
                    ' API call for key ' . $banned_call->ownerID . '. Reason: ' . $banned_call->reason . PHP_EOL .
                    'Key characters: ' . PHP_EOL;

                // Add some information about the characters on this key
                foreach (\EveAccountAPIKeyInfoCharacters::where('keyID', $banned_call->ownerID)->get() as $character)
                    $notification_text .= ' - ' . $character->characterName . PHP_EOL;

                // Send the notification
                BaseNotify::sendNotification($super_user->id, $notification_type, $notification_title, $notification_text);

            }
        }
    }
}
