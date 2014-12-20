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

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class Notifications extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Char';
        $api = 'Notifications';

        if (BaseApi::isBannedCall($api, $scope, $keyID))
            return;

        // Get the characters for this key
        $characters = BaseApi::findKeyCharacters($keyID);

        // Check if this key has any characters associated with it
        if (!$characters)
            return;

        // Lock the call so that we are the only instance of this running now()
        // If it is already locked, just return without doing anything
        if (!BaseApi::isLockedCall($api, $scope, $keyID))
            $lockhash = BaseApi::lockCall($api, $scope, $keyID);
        else
            return;

        // Next, start our loop over the characters and upate the database
        foreach ($characters as $characterID) {

            // Prepare the Pheal instance
            $pheal = new Pheal($keyID, $vCode);

            // Do the actual API call. pheal-ng actually handles some internal
            // caching too.
            try {

                $notifications = $pheal
                    ->charScope
                    ->Notifications(array('characterID' => $characterID));

            } catch (\Pheal\Exceptions\APIException $e) {

                // If we cant get account status information, prevent us from calling
                // this API again
                BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
                return;

            } catch (\Pheal\Exceptions\PhealException $e) {

                throw $e;
            }

            // Check if the data in the database is still considered up to date.
            // checkDbCache will return true if this is the case
            if (!BaseApi::checkDbCache($scope, $api, $notifications->cached_until, $characterID)) {

                // Loop over the list we got from the api and update the db,
                // remebering the messageID's for downloading the bodies too
                $texts = array();
                foreach ($notifications->notifications as $notification) {

                    $notification_data = \EveCharacterNotifications::where('characterID', '=', $characterID)
                        ->where('notificationID', '=', $notification->notificationID)
                        ->first();

                    if (!$notification_data) {

                        $new_notification = new \EveCharacterNotifications;
                        $texts[] = $notification->notificationID; // Record the notificationID to download later
                    } else {

                        // Check if we have the body for this existing message, else
                        // we will add it to the list to download
                        if (!\EveCharacterNotificationTexts::where('notificationID', '=', $notification->notificationID))
                            $texts[] = $notification->notificationID;

                        continue;
                    }

                    $new_notification->characterID = $characterID;
                    $new_notification->notificationID = $notification->notificationID;
                    $new_notification->typeID = $notification->typeID;
                    $new_notification->senderID = $notification->senderID;
                    $new_notification->senderName = $notification->senderName;
                    $new_notification->sentDate = $notification->sentDate;
                    $new_notification->read = $notification->read;
                    $new_notification->save();
                }

                // Split the text we need to download into chunks of 10 each. Pheal-NG will
                // log the whole request as a file name for chaching...
                // which is tooooooo looooooooooooong
                $texts = array_chunk($texts, 10);

                // Iterate over the chunks.
                foreach ($texts as $chunk) {

                    try {

                        $notification_texts = $pheal
                            ->charScope
                            ->NotificationTexts(array('characterID' => $characterID, 'ids' => implode(',', $chunk)));

                    } catch (\Pheal\Exceptions\PhealException $e) {

                        throw $e;
                    }

                    // Loop over the received texts
                    foreach ($notification_texts->notifications as $text) {

                        // Actually, this check is pretty redundant, so maybe remove it
                        $text_data = \EveCharacterNotificationTexts::where('notificationID', '=', $text->notificationID)->first();

                        if (!$text_data)
                            $new_text = new \EveCharacterNotificationTexts;
                        else
                            continue;

                        $new_text->notificationID = $text->notificationID;
                        $new_text->text = $text->__toString();
                        $new_text->save();
                    }
                }

                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $notifications->cached_until, $characterID);
            }
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $notifications;
    }
}
