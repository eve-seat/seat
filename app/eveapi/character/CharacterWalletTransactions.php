<?php

namespace Seat\EveApi\Character;

use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class WalletTransactions extends BaseApi {

	public static function Update($keyID, $vCode)
	{

		$row_count = 1000;

		// Start and validate they key pair
		BaseApi::bootstrap();
		BaseApi::validateKeyPair($keyID, $vCode);

		// Set key scopes and check if the call is banned
		$scope = 'Char';
		$api = 'WalletTransactions';

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

			// Start a infinite loop for the Transactions Walking. We will break out of this once
			// we have reached the end of the records that we can get

			// TODO: This needs a lot more brain thingies applied in order to figure out how
			// we are going to go about the database cached_untill timer. For now, we will just
			// ignore the DB level one and rely entirely on pheal-ng to cache the XML's

			$first_request = true;
			$from_id = 9223372036854775807; // Max integer for 64bit PHP
			while (true) {

				// Do the actual API call. pheal-ng actually handles some internal
				// caching too.
				try {

					if ($first_request) {

						$wallet_transactions = $pheal
							->charScope
							->WalletTransactions(array('characterID' => $characterID, 'rowCount' => $row_count));
					} else {
					
						$wallet_transactions = $pheal
							->charScope
							->WalletTransactions(array('characterID' => $characterID, 'rowCount' => $row_count, 'fromID' => $from_id));
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
				foreach ($wallet_transactions->transactions as $transaction) {

					// Ensure that $from_id is at its lowest
					$from_id = min($transaction->transactionID, $from_id);

					// Generate a transaction hash. It would seem that transactionID's could possibly be
					// cycled. 
					$transaction_hash = md5(implode(',', array($characterID, $transaction->transactionDateTime, $transaction->clientID, $transaction->transactionID)));

					$transaction_data  = \EveCharacterWalletTransactions::where('characterID', '=', $characterID)
						->where('hash', '=', $transaction_hash)
						->first();

					if (!$transaction_data)
						$transaction_data = new \EveCharacterWalletTransactions;
					else
						continue;

					$transaction_data->characterID = $characterID;
					$transaction_data->hash = $transaction_hash;
					$transaction_data->transactionID = $transaction->transactionID;
					$transaction_data->transactionDateTime = $transaction->transactionDateTime;
					$transaction_data->quantity = $transaction->quantity;
					$transaction_data->typeName = $transaction->typeName;
					$transaction_data->typeID = $transaction->typeID;
					$transaction_data->price = $transaction->price;
					$transaction_data->clientID = $transaction->clientID;
					$transaction_data->clientName = $transaction->clientName;
					$transaction_data->stationID = $transaction->stationID;
					$transaction_data->stationName = $transaction->stationName;
					$transaction_data->transactionType = $transaction->transactionType;
					$transaction_data->transactionFor = $transaction->transactionFor;
					$transaction_data->journalTransactionID = $transaction->journalTransactionID;
					$transaction_data->clientTypeID = $transaction->clientTypeID;
					$transaction_data->save();
				}

				// Check how many entries we got back. If it us less than $row_count, we know we have
				// walked back the entire journal
				if (count($wallet_transactions->transactions) < $row_count)
					break; // Break the while loop
			}
		}

		// Unlock the call
		BaseApi::unlockCall($lockhash);

		return $wallet_transactions;
	}
}
