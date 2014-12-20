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

class ContactList extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Char';
        $api = 'ContactList';

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

                $contact_list = $pheal
                    ->charScope
                    ->ContactList(array('characterID' => $characterID));

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
            if (!BaseApi::checkDbCache($scope, $api, $contact_list->cached_until, $characterID)) {

                // The ContactList API will potentially return the characters:
                // a) personal contacts,
                // b) corporation contacts,
                // c) alliance contracts.

                // TODO: Think about maybe doing this in a transaction?

                // First, the personal contacts
                // Remove the current contacts
                \EveCharacterContactList::where('characterID', '=', $characterID)->delete();

                // Loop over the list we got from the api and update the db
                foreach ($contact_list->contactList as $contact) {

                    $new_contact = new \EveCharacterContactList;
                    $new_contact->characterID = $characterID;
                    $new_contact->contactID = $contact->contactID;
                    $new_contact->contactName = $contact->contactName;
                    $new_contact->inWatchlist = (($contact->inWatchlist == True) ? 1 : 0);
                    $new_contact->standing = $contact->standing;
                    $new_contact->contactTypeID = $contact->contactTypeID;
                    $new_contact->save();
                }

                // Second, the corporate contacts
                // Remove the current contacts
                \EveCharacterContactListCorporate::where('characterID', '=', $characterID)->delete();

                // Loop over the list we got from the api and update the db
                foreach ($contact_list->corporateContactList as $contact) {

                    $new_contact = new \EveCharacterContactListCorporate;
                    $new_contact->characterID = $characterID;
                    $new_contact->contactID = $contact->contactID;
                    $new_contact->contactName = $contact->contactName;
                    $new_contact->standing = $contact->standing;
                    $new_contact->contactTypeID = $contact->contactTypeID;
                    $new_contact->save();
                }

                // Third, the alliance contacts
                // Remove the current contacts
                \EveCharacterContactListAlliance::where('characterID', '=', $characterID)->delete();

                // Loop over the list we got from the api and update the db
                foreach ($contact_list->allianceContactList as $contact) {

                    $new_contact = new \EveCharacterContactListAlliance;
                    $new_contact->characterID = $characterID;
                    $new_contact->contactID = $contact->contactID;
                    $new_contact->contactName = $contact->contactName;
                    $new_contact->standing = $contact->standing;
                    $new_contact->contactTypeID = $contact->contactTypeID;
                    $new_contact->save();
                }

                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $contact_list->cached_until, $characterID);
            }
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $contact_list;
    }
}
