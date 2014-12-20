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

class Contracts extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Char';
        $api = 'Contracts';

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

                $contracts = $pheal
                    ->charScope
                    ->Contracts(array('characterID' => $characterID));

            } catch (\Pheal\Exceptions\APIException $e) {

                // If we cant get account status information, prevent us from calling
                // this API again
                BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
                return;

            } catch (\Pheal\Exceptions\PhealException $e) {

                throw $e;
            }

            // Before we start looping over the contracts, we need to add some more
            // logic to this Updater. The ContractItems call seems to be flaky
            // in the sense that when we call the char/Contracts API, we get
            // a list of contractID's. These ID's are checked for existence
            // in the database and updated accordingly. If its a new
            // contract, we call ContractItems to get the details.
            // This is where shit falls apart and :ccp: thiks its
            // clever to error out for ID's we *JUST* got back.
            //
            // So, to reduce the chances of getting the calling IP banned due to
            // ~reasons~, we will have a local counter to limit the amount of
            // errors caused by this this call. If we hit this limit, we
            // return the function, and wait for the next run to update
            // again. We will also run banCall() so that the global
            // error counter is also aware of this.
            $error_limit = 25;

            // Check if the data in the database is still considered up to date.
            // checkDbCache will return true if this is the case
            if (!BaseApi::checkDbCache($scope, $api, $contracts->cached_until, $characterID)) {

                // Loop over the contracts and update
                foreach ($contracts->contractList as $contract) {

                    $contract_data = \EveCharacterContracts::where('characterID', '=', $characterID)
                        ->where('contractID', '=', $contract->contractID)
                        ->first();

                    // If we an existing contract that we are just going to update, then dont bother
                    // running /char/ContractItems. I *think* this will be the same all the time
                    // and can only change by creating a new contract
                    if (!$contract_data) {

                        $new_data = new \EveCharacterContracts;
                        $get_items = true; // [1]
                    } else {

                        $new_data = $contract_data;
                        $get_items = false;
                    }

                    $new_data->characterID = $characterID;
                    $new_data->contractID = $contract->contractID;
                    $new_data->issuerID = $contract->issuerID;
                    $new_data->issuerCorpID = $contract->issuerCorpID;
                    $new_data->assigneeID = $contract->assigneeID;
                    $new_data->acceptorID = $contract->acceptorID;
                    $new_data->startStationID = $contract->startStationID;
                    $new_data->endStationID = $contract->endStationID;
                    $new_data->type = $contract->type;
                    $new_data->status = $contract->status;
                    $new_data->title = (strlen($contract->title) > 0 ? $contract->title : null);
                    $new_data->forCorp = $contract->forCorp;
                    $new_data->availability = $contract->availability;
                    $new_data->dateIssued = $contract->dateIssued;
                    $new_data->dateExpired = (strlen($contract->dateExpired) > 0 ? $contract->dateExpired : null);
                    $new_data->dateAccepted = (strlen($contract->dateAccepted) > 0 ? $contract->dateAccepted : null);
                    $new_data->numDays = $contract->numDays;
                    $new_data->dateCompleted = (strlen($contract->dateCompleted) > 0 ? $contract->dateCompleted : null);
                    $new_data->price = $contract->price;
                    $new_data->reward = $contract->reward;
                    $new_data->collateral = $contract->collateral;
                    $new_data->buyout = $contract->buyout;
                    $new_data->volume = $contract->volume;
                    $new_data->save();

                    // [1] New contracts will have their 'items' updated too. Do it
                    if ($get_items) {

                        try {

                            $contracts_items = $pheal
                                ->charScope
                                ->ContractItems(array('characterID' => $characterID, 'contractID' => $contract->contractID));

                        // :ccp: Seems to give you a list of ID's for a call, and then
                        // complain seconds later that the itemID is incorrect. This
                        // after we *just* got it from them! ffs. Anyways, we will
                        // process banning here so that the global error counter
                        // in the \Cache::has('eve_api_error_count') can inc
                        // and we dont cause too many exceptions.
                        //
                        // We will also dec the $error_limit and break if we hit 0.
                        } catch (\Pheal\Exceptions\APIException $e) {

                            // Dec $error_limit
                            $error_limit--;

                            // Process the banning for the update of the global eve_api_error_count
                            BaseApi::banCall('ContractItems', $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());

                            // Check the state of the $error_limit and decide what to do
                            if($error_limit <= 0)
                                return;
                            else
                                continue;

                        } catch (\Pheal\Exceptions\PhealException $e) {

                            throw $e;
                        }

                        // Loop over the items in contracts and save it
                        foreach ($contracts_items->itemList as $item) {
                            $items = new \EveCharacterContractsItems;

                            $items->characterID = $characterID;
                            $items->contractID = $contract->contractID;
                            $items->recordID = $item->recordID;
                            $items->typeID = $item->typeID;
                            $items->quantity = $item->quantity;
                            $items->rawQuantity = (isset($item->rawQuantity) ? $item->rawQuantity : null);
                            $items->singleton = $item->singleton;
                            $items->included = $item->included;
                            $new_data->items()->save($items);
                        }
                    }
                }

                // Update the cached_until time in the database for this api call
                BaseApi::setDbCache($scope, $api, $contracts->cached_until, $characterID);
            }
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $contracts;
    }
}
