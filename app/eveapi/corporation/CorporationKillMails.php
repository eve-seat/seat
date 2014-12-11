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

namespace Seat\EveApi\Corporation;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class KillMails extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // To start processing this API Updater, lets take a moment and
        // examine a sample XML for a kill (taken 2014-12-04 15:24:40):

        // <rowset name="kills" key="killID" columns="killID,solarSystemID,killTime,moonID">
        //   <row killID="4" solarSystemID="3" killTime="2013-11-30 14:36:00" moonID="0">
        //     <victim characterID="9" characterName="J" corporationID="9" corporationName="S" allianceID="9" allianceName="I" factionID="0" factionName="" damageTaken="48243" shipTypeID="33475" />
        //     <rowset name="attackers" columns="characterID,characterName,corporationID,corporationName,allianceID,allianceName,factionID,factionName,securityStatus,damageDone,finalBlow,weaponTypeID,shipTypeID">
        //       <row characterID="1" characterName="K" corporationID="8" corporationName="T" allianceID="1" allianceName="T" factionID="0" factionName="" securityStatus="0.88117301707494" damageDone="11610" finalBlow="1" weaponTypeID="2905" shipTypeID="11198" />
        //     </rowset>
        //     <rowset name="items" columns="typeID,flag,qtyDropped,qtyDestroyed,singleton">
        //       <row typeID="15331" flag="5" qtyDropped="5" qtyDestroyed="18" singleton="0" />
        //       <row typeID="2510" flag="5" qtyDropped="100" qtyDestroyed="0" singleton="0" />
        //     </rowset>
        //   </row>
        // </rowset>

        // Based on the above, we can see we have 3 sections that need to be kept up to date.
        // We also need to think about scenarios where we have 2 API's, where the one will
        // reflect a kill as a loss, and the other as a kill. For that reason we keep
        // a seperate table to map characterID<->killID, and then record the kill
        // and all the details seperately. That way, we only store the specific
        // killID and its details once, and it can be called by iether the
        // killer or the loser.

        // It is also possible to do 'Journal Walking' on the killmails, meaning that we can
        // pull all the history as far back as 2560 [1] entries. With that in mind, we
        // will set a MAX_INT value as a starting point, and then grab the fromID in
        // the responses to start the walking backwards while updating the db.
        $row_count = 1000;

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Corp';
        $api = 'KillMails';

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

        // So I think a corporation key will only ever have one character
        // attached to it. So we will just use that characterID for auth
        // things, but the corporationID for locking etc.
        $corporationID = BaseApi::findCharacterCorporation($characters[0]);

        // Prepare the Pheal instance
        $pheal = new Pheal($keyID, $vCode);

        // Here we will start a infinite loop for the Journal Walking to take
        // place. Once we receive less entrues than the expected $row_count
        // we know we reached the end of the walk and can safely break
        // out of the loop. Some database cached_until checks are
        // actually not done here. We are relying entirely on
        // pheal-ng to handle it.
        $first_request = true;
        $from_id = 9223372036854775807;

        while(true) {

            // Check if this is the first request. If so, don't use a fromID.
            try {

                if ($first_request) {

                    $kill_mails = $pheal
                        ->corpScope
                        ->KillMails(array('characterID' => $characters[0], 'rowCount' => $row_count));

                    // flip the first_request as those that get processed from here need to be from the `fromID`
                    $first_request = false;

                } else {

                    $kill_mails = $pheal
                        ->corpScope
                        ->KillMails(array('characterID' => $characters[0], 'rowCount' => $row_count, 'fromID' => $from_id));
                }

            } catch (\Pheal\Exceptions\APIException $e) {

                // If we cant get account status information, prevent us from calling
                // this API again
                BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
                return;

            } catch (\Pheal\Exceptions\PhealException $e) {

                throw $e;
            }

            // With $kill_mails now populated, get started in updating the database
            // with all of the information that we now have. While we update the
            // database, we also need to check that we get the lowest possible
            // $from_id (killID) for the Journal Walking to occur
            foreach ($kill_mails->kills as $kill) {

                // Ensure $from_id is at its lowest
                $from_id = min($kill->killID, $from_id);

                // With all of that work done, we can finally get to actually
                // populating the database with data!

                // Determine if we already know about this killID for this characterID
                $corporation_kill_mail = \EveCorporationKillMails::where('corporationID', $corporationID)
                    ->where('killID', $kill->killID)
                    ->first();

                // If we know about it, assume we have all the details already recorded,
                // otherwise we will record it
                if(!$corporation_kill_mail)
                    $corporation_kill_mail = new \EveCorporationKillMails;
                else
                    continue;

                $corporation_kill_mail->corporationID = $corporationID;
                $corporation_kill_mail->killID = $kill->killID;
                $corporation_kill_mail->save();

                // With record of the killmail, check if we have the details recorded
                // for it. If we do, we assume the attackers and items will be the
                // same and not need any update
                $killmail_detail = \EveCorporationKillMailDetail::where('killID', $kill->killID)
                    ->first();

                // The same as the killmail record for a character applies here. If
                // we already know about it, asssume that it is up to date and
                // continue with the next killmail.
                if(!$killmail_detail)
                    $killmail_detail = new \EveCorporationKillMailDetail;
                else
                    continue;

                // Assuming we got all the way here, its safe to assume we want to
                // record the details for this killmail.
                $killmail_detail->killID = $kill->killID;
                $killmail_detail->solarSystemID = $kill->solarSystemID;
                $killmail_detail->killTime = $kill->killTime;
                $killmail_detail->moonID = $kill->moonID;
                $killmail_detail->characterID = $kill->victim->characterID;
                $killmail_detail->characterName = $kill->victim->characterName;
                $killmail_detail->corporationID = $kill->victim->corporationID;
                $killmail_detail->corporationName = $kill->victim->corporationName;
                $killmail_detail->allianceID = $kill->victim->allianceID;
                $killmail_detail->allianceName = $kill->victim->allianceName;
                $killmail_detail->factionID = $kill->victim->factionID;
                $killmail_detail->factionName = $kill->victim->factionName;
                $killmail_detail->damageTaken = $kill->victim->damageTaken;
                $killmail_detail->shipTypeID = $kill->victim->shipTypeID;
                $killmail_detail->save();

                // Update the attackers
                foreach ($kill->attackers as $attacker) {

                    $attacker_information = new \EveCorporationKillMailAttackers;

                    // $attacker_information->killID = $kill->killID;
                    $attacker_information->characterID = $attacker->characterID;
                    $attacker_information->characterName = $attacker->characterName;
                    $attacker_information->corporationID = $attacker->corporationID;
                    $attacker_information->corporationName = $attacker->corporationName;
                    $attacker_information->allianceID = $attacker->allianceID;
                    $attacker_information->allianceName = $attacker->allianceName;
                    $attacker_information->factionID = $attacker->factionID;
                    $attacker_information->factionName = $attacker->factionName;
                    $attacker_information->securityStatus = $attacker->securityStatus;
                    $attacker_information->damageDone = $attacker->damageDone;
                    $attacker_information->finalBlow = $attacker->finalBlow;
                    $attacker_information->weaponTypeID = $attacker->weaponTypeID;
                    $attacker_information->shipTypeID = $attacker->shipTypeID;

                    // Add the attacker information to the killmail
                    $killmail_detail->attackers()->save($attacker_information);
                }

                // Finally, update the dropped items
                foreach ($kill->items as $item) {

                    $item_information = new \EveCorporationKillMailItems;

                    // $item_information->killID = $kill->killID;
                    $item_information->flag = $item->flag;
                    $item_information->qtyDropped = $item->qtyDropped;
                    $item_information->qtyDestroyed = $item->qtyDestroyed;
                    $item_information->singleton = $item->singleton;

                    // Add the item information to the killmail
                    $killmail_detail->items()->save($item_information);
                }
            }

            // Check how many entries we got back. If it is less than $row_count, we know we have
            // walked back the entire journal
            if (count($kill_mails->kills) < $row_count)
                break; // Break the while loop
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $kill_mails;
    }
}
