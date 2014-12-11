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

class CharacterSheet extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Char';
        $api = 'CharacterSheet';

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

                $character_sheet = $pheal
                    ->charScope
                    ->CharacterSheet(array('characterID' => $characterID));

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
            if (!BaseApi::checkDbCache($scope, $api, $character_sheet->cached_until, $characterID)) {

                $character_data = \EveCharacterCharacterSheet::where('characterID', '=', $characterID)->first();

                if (!$character_data)
                    $new_data = new \EveCharacterCharacterSheet;
                else
                    $new_data = $character_data;

                $new_data->characterID = $character_sheet->characterID;
                $new_data->name = $character_sheet->name;
                $new_data->DoB = $character_sheet->DoB;
                $new_data->race = $character_sheet->race;
                $new_data->bloodLine = $character_sheet->bloodLine;
                $new_data->ancestry = $character_sheet->ancestry;
                $new_data->gender = $character_sheet->gender;
                $new_data->corporationName = $character_sheet->corporationName;
                $new_data->corporationID = $character_sheet->corporationID;
                $new_data->balance = $character_sheet->balance;
                $new_data->intelligence = $character_sheet->attributes->intelligence;
                $new_data->memory = $character_sheet->attributes->memory;
                $new_data->charisma = $character_sheet->attributes->charisma;
                $new_data->perception = $character_sheet->attributes->perception;
                $new_data->willpower = $character_sheet->attributes->willpower;

                // New stuff from Phoebe
                $new_data->homeStationID = $character_sheet->homeStationID;
                $new_data->factionName = $character_sheet->factionName;
                $new_data->factionID = $character_sheet->factionID;
                $new_data->freeRespecs = $character_sheet->freeRespecs;
                $new_data->cloneJumpDate = $character_sheet->cloneJumpDate;
                $new_data->lastRespecDate = $character_sheet->lastRespecDate;
                $new_data->lastTimedRespec = $character_sheet->lastTimedRespec;
                $new_data->remoteStationDate = $character_sheet->remoteStationDate;
                $new_data->jumpActivation = $character_sheet->jumpActivation;
                $new_data->jumpFatigue = $character_sheet->jumpFatigue;
                $new_data->jumpLastUpdate = $character_sheet->jumpLastUpdate;

                // Save the information
                $new_data->save();

                // Update the characters skills
                foreach ($character_sheet->skills as $skill) {

                    $skill_data = \EveCharacterCharacterSheetSkills::where('characterID', '=', $characterID)
                        ->where('typeID', '=', $skill->typeID)
                        ->first();

                    if (!$skill_data)
                        $skill_info = new \EveCharacterCharacterSheetSkills;
                    else
                        $skill_info = $skill_data;

                    $skill_info->characterID = $characterID;
                    $skill_info->typeID = $skill->typeID;
                    $skill_info->skillpoints = $skill->skillpoints;
                    $skill_info->level = $skill->level;
                    $skill_info->published = $skill->published;
                    $new_data->skills()->save($skill_info);
                }

                // Update the Jump Clones.
                // First thing we need to do is clear out all of the  known clones for
                // this character. We cant really say that clones will remain, so to
                // be safe, clear all of the clones, and populate the new ones.

                // So, lets get to the deletion part. We need to keep in mind that a characterID
                // my have multiple jumpClones. Each clone in turn may have multiple implants
                // which in turn are linked back to a jumpClone and then to a chacterID
                \EveCharacterCharacterSheetJumpClones::where('characterID', $characterID)->delete();
                \EveCharacterCharacterSheetJumpCloneImplants::where('characterID', $characterID)->delete();

                // Next, loop over the clones we got in the API response
                foreach ($character_sheet->jumpClones as $jump_clone) {

                    $clone_data = new \EveCharacterCharacterSheetJumpClones;

                    $clone_data->jumpCloneID = $jump_clone->jumpCloneID;
                    $clone_data->characterID = $characterID;
                    $clone_data->typeID = $jump_clone->typeID;
                    $clone_data->locationID = $jump_clone->locationID;
                    $clone_data->cloneName = $jump_clone->cloneName;

                    $clone_data->save();
                }

                // With the jump clones populated, we move on to the implants per
                // jump clone.
                foreach ($character_sheet->jumpCloneImplants as $jump_clone_implants) {

                    $implant_data = new \EveCharacterCharacterSheetJumpCloneImplants;

                    $implant_data->jumpCloneID = $jump_clone_implants->jumpCloneID;
                    $implant_data->characterID = $characterID;
                    $implant_data->typeID = $jump_clone_implants->typeID;
                    $implant_data->typeName = $jump_clone_implants->typeName;

                    $implant_data->save();
                }

                // Finally, we update the character with the implants that they have.
                // Again, we can not assume that the implants they had in the
                // previous update is the same as the current, so, delete
                // everything and populate again.
                \EveCharacterCharacterSheetImplants::where('characterID', $characterID)->delete();

                // Now, loop over the implants from the API response and populate them.
                foreach ($character_sheet->implants as $implant) {

                    $implant_data = new \EveCharacterCharacterSheetImplants;

                    $implant_data->characterID = $characterID;
                    $implant_data->typeID = $implant->typeID;
                    $implant_data->typeName = $implant->typeName;

                    $implant_data->save();
                }

                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $character_sheet->cached_until, $characterID);
            }
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $character_sheet;
    }
}
