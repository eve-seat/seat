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

class WalletJournal extends BaseApi
{

    public static function Update($keyID, $vCode)
    {

        $row_count = 500;

        // Start and validate they key pair
        BaseApi::bootstrap();
        BaseApi::validateKeyPair($keyID, $vCode);

        // Set key scopes and check if the call is banned
        $scope = 'Char';
        $api = 'WalletJournal';

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

            // When we query the API for transactions, the plan is to walk
            // all the way back until there is no more data available.
            // This plan is all fine and dandy, untill people whom
            // have like 500k+ transactions in the last month
            // start adding keys to your SeAT install and
            // suddenly shit starts to come to a halt
            // due to ~reasons~.

            // In order for us to help alleviate some of the stress what we
            // will be adding by this, we will ask for the maximum known
            // transactionID for the characterID that we are working
            // with. You may immediately think "but what about
            // transaction renumbering in a new generation?".
            // This is something that has to be figured out
            // a different way, however, considering it is
            // currently a 64bit INT, there may be some
            // time for us left to solve this problem.
            $known_max_ref_id = \EveCharacterWalletJournal::where('characterID', '=', $characterID)
                ->max('refID');

            // We are also going to set a flag that will signal the
            // completion of a update. This should be set to true
            // once $known_max_transaction_id == $transactionID.
            // We dont break immediately though as the api
            // results are not sorted. Id does however
            // prevent us from crawling back say
            // 400k *already* known entries.
            $keep_walking = true;

            // Prepare the Pheal instance
            $pheal = new Pheal($keyID, $vCode);

            // Start a infinite loop for the Journal Walking. We will break out of this once
            // we have reached the end of the records that we can get

            // TODO: This needs a lot more brain thingies applied in order to figure out how
            // we are going to go about the database cached_untill timer. For now, we will just
            // ignore the DB level one and rely entirely on pheal-ng to cache the XML's

            $first_request = true;
            $from_id = PHP_INT_MAX; // Use the maximum size for this PHP arch
            while (true) {

                // Do the actual API call. pheal-ng actually handles some internal
                // caching too.
                try {

                    if ($first_request) {

                        $wallet_journal = $pheal
                            ->charScope
                            ->WalletJournal(array('characterID' => $characterID, 'rowCount' => $row_count));

                        // flip the first_request as those that get processed from here need to be from the `fromID`
                        $first_request = false;

                    } else {

                        $wallet_journal = $pheal
                            ->charScope
                            ->WalletJournal(array('characterID' => $characterID, 'rowCount' => $row_count, 'fromID' => $from_id));
                    }

                } catch (\Pheal\Exceptions\APIException $e) {

                    // If we cant get account status information, prevent us from calling
                    // this API again
                    BaseApi::banCall($api, $scope, $keyID, 0, $e->getCode() . ': ' . $e->getMessage());
                    return;

                } catch (\Pheal\Exceptions\PhealException $e) {

                    throw $e;
                }

                // Process the transactions
                foreach ($wallet_journal->transactions as $transaction) {

                    // Ensure that $from_id is at its lowest
                    $from_id = min($transaction->refID, $from_id);

                    // Check if the known_max_transaction_id matches this transactionID
                    if ($known_max_ref_id == $transaction->refID)

                        // Looks like we have a response with our last known entry
                        // in it. Lets flip the waling bit.
                        $keep_walking = false;

                    // Generate a transaction hash. It would seem that refID's could possibly be cycled.
                    $transaction_hash = md5(implode(',', array($characterID, $transaction->date, $transaction->ownerID1, $transaction->refID)));

                    // In order try try and relieve some strain on the database, we will
                    // cache the hashes that we find we have knowledge of. The main
                    // goal of this is to make the lookups faster, and leave
                    // MySQL to do stuff it should rather be doing

                    // First, find thee entry in the cache. The cache key is determined
                    // as: <transaction hash + characterID + apitype>
                    if (!\Cache::has($transaction_hash . $characterID . $api)) {

                        // If the $transaction_hash is not in the Cache, ask the database
                        // if it knows about it. Again, if it exists, we will continue,
                        // but we will also add a new Cache entry to make the next
                        // lookip faster
                        $transaction_data  = \EveCharacterWalletJournal::where('characterID', '=', $characterID)
                            ->where('hash', '=', $transaction_hash)
                            ->first();

                        // Check if the database found the entry.
                        if (!$transaction_data) {

                            $transaction_data = new \EveCharacterWalletJournal;

                        } else {

                            // This entry exists in the database. Put a new Cache entry
                            // so that the next lookup may be faster. This cache
                            // entry will live for 1 week.
                            \Cache::put($transaction_hash . $characterID . $api, true, 60 * 24 * 7);

                            // Continue to the next transaction
                            continue;
                        }

                        $transaction_data->characterID = $characterID;
                        $transaction_data->hash = $transaction_hash;
                        $transaction_data->refID = $transaction->refID;
                        $transaction_data->date = $transaction->date;
                        $transaction_data->refTypeID = $transaction->refTypeID;
                        $transaction_data->ownerName1 = $transaction->ownerName1;
                        $transaction_data->ownerID1 = $transaction->ownerID1;
                        $transaction_data->ownerName2 = $transaction->ownerName2;
                        $transaction_data->ownerID2 = $transaction->ownerID2;
                        $transaction_data->argName1 = $transaction->argName1;
                        $transaction_data->argID1 = $transaction->argID1;
                        $transaction_data->amount = $transaction->amount;
                        $transaction_data->balance = $transaction->balance;
                        $transaction_data->reason = $transaction->reason;
                        $transaction_data->taxReceiverID = (strlen($transaction->taxReceiverID) > 0 ? $transaction->taxReceiverID : 0);
                        $transaction_data->taxAmount = (strlen($transaction->taxAmount) > 0 ? $transaction->taxAmount : 0);
                        $transaction_data->owner1TypeID = $transaction->owner1TypeID;
                        $transaction_data->owner2TypeID = $transaction->owner2TypeID;
                        $transaction_data->save();

                    } else {

                        // This entry already exists as the hash is cached.
                        continue;
                    }
                }

                // Check if the walking but was flipped. See the above
                // comments for an explanation of what is going on.
                if (!$keep_walking)
                    break;

                // Check how many entries we got back. If it us less that $row_count, we know we have
                // walked back the entire journal
                if (count($wallet_journal->transactions) < $row_count)
                    break; // Break the while loop
            }
        }

        // Unlock the call
        BaseApi::unlockCall($lockhash);

        return $wallet_journal;
    }
}
