<?php

class CharacterController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| getAll()
	|--------------------------------------------------------------------------
	|
	| Get all of the characters on record
	|
	*/

	public function getAll()
	{

		$characters = DB::table('account_apikeyinfo_characters')
			->leftJoin('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
			->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
			->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
			->groupBy('account_apikeyinfo_characters.characterID')
			->get();

		return View::make('character.all')
			->with('characters', $characters);
	}

	/*
	|--------------------------------------------------------------------------
	| getView()
	|--------------------------------------------------------------------------
	|
	| Get all of the information that we possibly have for a specific
	| characterID. This includes a character sheet, skill sheet, skill queue,
	| wallet journal, mail, notifications & assets
	|
	| Outstanding information for this function is still:
	|	Wallet Transactions, Attribute Enhancers, Contact Lists, Industry Jobs
	|	Standings
	|
	*/

	public function getView($characterID)
	{

		$character = DB::table('account_apikeyinfo_characters')
			->leftJoin('account_apikeyinfo', 'account_apikeyinfo_characters.keyID', '=', 'account_apikeyinfo.keyID')
			->leftJoin('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
			->join('account_accountstatus', 'account_apikeyinfo_characters.keyID', '=', 'account_accountstatus.keyID')
			->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
			->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
			->leftJoin('invTypes', 'character_skillintraining.trainingTypeID', '=', 'invTypes.typeID')
			->where('character_charactersheet.characterID', $characterID)
			->first();

		if(count($character) <= 0)
			return Redirect::action('CharacterController@getAll')
				->withErrors('Invalid Character ID');

		$other_characters = DB::table('account_apikeyinfo_characters')
			->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
			->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
			->where('account_apikeyinfo_characters.keyID', $character->keyID)
			->where('account_apikeyinfo_characters.characterID', '<>', $character->characterID)
			->get();

		$skillpoints = DB::table('character_charactersheet_skills')
			->where('characterID', $characterID)
			->sum('skillpoints');

		$skill_queue = DB::table('character_skillqueue')
			->join('invTypes', 'character_skillqueue.typeID', '=', 'invTypes.typeID')
			->where('characterID', $characterID)
			->get();

		// Thanks to the db being pretty WTF, we will just do some things™ and get an
		// array ready for all the skills. Essentially, we will use this as the main
		// loop for our skill presentation in the view
		$skill_groups = DB::table('invGroups')
			->where('categoryID', 16)
			->where('groupID', '<>', 505)
			->orderBy('groupName')
			->get();

		// Now that we have all the groups, get the characters skills and info
		$character_skills_information = DB::table('character_charactersheet_skills')
			->join('invTypes', 'character_charactersheet_skills.typeID', '=', 'invTypes.typeID')
			->join('invGroups', 'invTypes.groupID', '=', 'invGroups.groupID')
			->where('character_charactersheet_skills.characterID', $characterID)
			->orderBy('invTypes.typeName')
			->get();

		// Lastly, create an array that is easy to loop over in the template to display
		// the data
		// TODO: Look at the possibility of lists() and specifying the groupID as key
		$character_skills = array();
		foreach ($character_skills_information as $key => $value) {
			
			$character_skills[$value->groupID][] =  array(
                'typeID' => $value->typeID,
                'groupName' => $value->groupName,
                'typeName' => $value->typeName,
                'description' => $value->description,
                'skillpoints' => $value->skillpoints,
                'level' => $value->level
            );
		}

		$wallet_journal = DB::table('character_walletjournal')
			->join('eve_reftypes', 'character_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
			->where('characterID', $characterID)
			->orderBy('date', 'desc')
			->take(25)
			->get();

		$wallet_transactions = DB::table('character_wallettransactions')
			->where('characterID', $characterID)
			->orderBy('transactionDateTime', 'desc')
			->take(25)
			->get();

		$mail = DB::table('character_mailmessages')
			->where('characterID', $characterID)
			->orderBy('sentDate', 'desc')
			->take(25)
			->get();

		$notifications = DB::table('character_notifications')
			->join('eve_notification_types', 'character_notifications.typeID', '=', 'eve_notification_types.typeID')
			->join('character_notification_texts', 'character_notifications.notificationID', '=', 'character_notification_texts.notificationID')
			->where('character_notifications.characterID', $characterID)
			->orderBy('sentDate', 'desc')
			->take(25)
			->get();

		// Now, this query for the assets relative to their locations.. dunno if I want to
		// try and move this shit to a query builder / eloquent version... :<
		$assets = DB::select(
			'SELECT *, CASE
				when a.locationID BETWEEN 66000000 AND 66014933 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.locationID-6000001)
				when a.locationID BETWEEN 66014934 AND 67999999 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.locationID-6000000)
				when a.locationID BETWEEN 60014861 AND 60014928 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.locationID)
				when a.locationID BETWEEN 60000000 AND 61000000 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.locationID)
				when a.locationID>=61000000 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.locationID)
				else (SELECT m.itemName FROM mapDenormalize AS m
					WHERE m.itemID=a.locationID) end
					AS location,a.locationId AS locID FROM `character_assetlist` AS a
					LEFT JOIN `invTypes` ON a.`typeID` = `invTypes`.`typeID`
					LEFT JOIN `invGroups` ON `invTypes`.`groupID` = `invGroups`.`groupID`
					WHERE a.`characterID` = ? ORDER BY location',
			array($characterID)
		);
		
		// Get assets contents and sum the quantity
		$assets_contents = DB::table(DB::raw('character_assetlist_contents as a'))
			->select(DB::raw('*'), DB::raw('SUM(a.quantity) as sumquantity'))
			->leftJoin('invTypes', 'a.typeID', '=', 'invTypes.typeID')
			->leftJoin('invGroups', 'invTypes.groupID', '=', 'invGroups.groupID')
			->where('a.characterID', $characterID)
			->groupBy(DB::raw('a.itemID, a.typeID'))
			->get();
		
		// Create an array that is easy to loop over in the template to display the data
		$assets_list = array();
		$assets_count = 0; //start counting items

		foreach ($assets as $key => $value) {

			$assets_list[$value->location][$value->itemID] =  array(
				'quantity' => $value->quantity,
				'typeID' => $value->typeID,
				'typeName' => $value->typeName,
				'groupName' => $value->groupName
			);
			$assets_count++;

			foreach( $assets_contents as $contents) {

				if ($value->itemID == $contents->itemID) { // check what parent content item has

					// create a sub array 'contents' and put content item info in
					$assets_list[$value->location][$contents->itemID]['contents'][] = array(
						'quantity' => $contents->sumquantity,
						'typeID' => $contents->typeID,
						'typeName' => $contents->typeName,
						'groupName' => $contents->groupName
					);
					$assets_count++;
				}
			}
		}
			
		// Character contact list
		$contact_list = DB::table('character_contactlist')
			->where('characterID', $characterID)
			->get();
		
		// Character contract list
		// Not a clean Query. TODO: Find another way
		$contract_list = DB::select(
			'SELECT *, CASE
				when a.startStationID BETWEEN 66000000 AND 66014933 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.startStationID-6000001)
				when a.startStationID BETWEEN 66014934 AND 67999999 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.startStationID-6000000)
				when a.startStationID BETWEEN 60014861 AND 60014928 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.startStationID)
				when a.startStationID BETWEEN 60000000 AND 61000000 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.startStationID)
				when a.startStationID>=61000000 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.startStationID)
				else (SELECT m.itemName FROM mapDenormalize AS m
					WHERE m.itemID=a.startStationID) end
				AS startlocation,
				CASE
				when a.endStationID BETWEEN 66000000 AND 66014933 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.endStationID-6000001)
				when a.endStationID BETWEEN 66014934 AND 67999999 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.endStationID-6000000)
				when a.endStationID BETWEEN 60014861 AND 60014928 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.endStationID)
				when a.endStationID BETWEEN 60000000 AND 61000000 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.endStationID)
				when a.endStationID>=61000000 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.endStationID)
				else (SELECT m.itemName FROM mapDenormalize AS m
					WHERE m.itemID=a.endStationID) end
				AS endlocation 
				FROM `character_contracts` AS a
					WHERE a.`characterID` = ?',
			array($characterID)
		);
		
		// Character contract item
		$contract_list_item = DB::table('character_contracts_items')
			->leftJoin('invTypes', 'character_contracts_items.typeID', '=', 'invTypes.typeID')
			->where('characterID', $characterID)
			->get();
		
		// Create 2 array for seperate Courier and Other Contracts
		$contracts_courier = array();
		$contracts_other = array();
		
		// Loops the contracts list and fill arrays
		foreach ($contract_list as $key => $value) {

			if($value->type == 'Courier') {

				$contracts_courier[$value->contractID] =  array(
					'contractID' => $value->contractID,
					'issuerID' => $value->issuerID,
					'assigneeID' => $value->assigneeID,
					'acceptorID' => $value->acceptorID,
					'type' => $value->type,
					'status' => $value->status,
					'title' => $value->title,
					'dateIssued' => $value->dateIssued,
					'dateExpired' => $value->dateExpired,
					'dateAccepted' => $value->dateAccepted,
					'dateCompleted' => $value->dateCompleted,
					'reward' => $value->reward,
					'volume' => $value->volume,
					'collateral' => $value->collateral,
					'startlocation' => $value->startlocation,
					'endlocation' => $value->endlocation
				);

			} else {

				$contracts_other[$value->contractID] =  array(
					'contractID' => $value->contractID,
					'issuerID' => $value->issuerID,
					'assigneeID' => $value->assigneeID,
					'acceptorID' => $value->acceptorID,
					'type' => $value->type,
					'status' => $value->status,
					'title' => $value->title,
					'dateIssued' => $value->dateIssued,
					'dateExpired' => $value->dateExpired,
					'dateCompleted' => $value->dateCompleted,
					'reward' => $value->reward, // for "Buyer will get" isk
					'price' => $value->price,
					'buyout' => $value->buyout,
					'startlocation' => $value->startlocation
				);
			}
			
			// Loop the Item in contracts and add it to his parent
			foreach( $contract_list_item as $contents) {

				if ($value->contractID == $contents->contractID) { // check what parent content item has

					// create a sub array 'contents' and put content item info in
					$contracts_other[$value->contractID]['contents'][] = array(
						'quantity' => $contents->quantity,
						'typeID' => $contents->typeID,
						'typeName' => $contents->typeName,
						'included' => $contents->included // for "buyer will pay" item
					);
				}
			}
		}
		
		// Finally, give all this to the view to handle
		return View::make('character.view')
			->with('character', $character)
			->with('other_characters', $other_characters)
			->with('skillpoints', $skillpoints)
			->with('skill_queue', $skill_queue)
			->with('skill_groups', $skill_groups)
			->with('character_skills', $character_skills)
			->with('wallet_transactions', $wallet_transactions)
			->with('wallet_journal', $wallet_journal)
			->with('mail', $mail)
			->with('notifications', $notifications)
			->with('contact_list', $contact_list)
			->with('assets_list', $assets_list)
			->with('assets_count', $assets_count)
			->with('contracts_courier', $contracts_courier)
			->with('contracts_other', $contracts_other)
			->with('assets', $assets); // leave this just in case
	}

	/*
	|--------------------------------------------------------------------------
	| getFullWalletJournal()
	|--------------------------------------------------------------------------
	|
	| Display the full recorded wallet journal for a character
	|
	*/

	public function getFullWalletJournal($characterID)
	{

		$character_name = DB::table('account_apikeyinfo_characters')
			->where('characterID', $characterID)
			->pluck('characterName');

		$wallet_journal = DB::table('character_walletjournal')
			->join('eve_reftypes', 'character_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
			->where('characterID', $characterID)
			->orderBy('date', 'desc')
			->paginate(50);

		return View::make('character.walletjournal.view')
			->with('character_name', $character_name)
			->with('characterID', $characterID)
			->with('wallet_journal', $wallet_journal);
	}

	/*
	|--------------------------------------------------------------------------
	| getFullWalletTransactions()
	|--------------------------------------------------------------------------
	|
	| Display the full recorded wallet transactions for a character
	|
	*/

	public function getFullWalletTransactions($characterID)
	{

		$character_name = DB::table('account_apikeyinfo_characters')
			->where('characterID', $characterID)
			->pluck('characterName');

		$wallet_transactions = DB::table('character_wallettransactions')
			->where('characterID', $characterID)
			->orderBy('transactionDateTime', 'desc')
			->paginate(50);

		return View::make('character.wallettransactions.view')
			->with('character_name', $character_name)
			->with('characterID', $characterID)
			->with('wallet_transactions', $wallet_transactions);
	}

	/*
	|--------------------------------------------------------------------------
	| getFullMail()
	|--------------------------------------------------------------------------
	|
	| Display the full list of mail for the character
	|
	*/

	public function getFullMail($characterID)
	{

		$character_name = DB::table('account_apikeyinfo_characters')
			->where('characterID', $characterID)
			->pluck('characterName');

		$mail = DB::table('character_mailmessages')
			->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
			->where('character_mailmessages.characterID', $characterID)
			->orderBy('character_mailmessages.sentDate', 'desc')
			->paginate(100);

		return View::make('character.mail.view')
			->with('character_name', $character_name)
			->with('characterID', $characterID)
			->with('mail', $mail);
	}

	/*
	|--------------------------------------------------------------------------
	| getSearchSkills()
	|--------------------------------------------------------------------------
	|
	| Return a view to search character skills
	|
	*/

	public function getSearchSkills()
	{

		return View::make('character.skillsearch.search');
	}

	/*
	|--------------------------------------------------------------------------
	| postSearchSkills()
	|--------------------------------------------------------------------------
	|
	| Search for characters that have certain skills injected & their leverls
	|
	*/

	public function postSearchSkills()
	{

		// Ensure we actually got an array...
		if (!is_array(Input::get('skills')))
			App::abort(404);

		$filter = DB::table('character_charactersheet_skills')
			->join('account_apikeyinfo_characters', 'character_charactersheet_skills.characterID', '=', 'account_apikeyinfo_characters.characterID')
			->join('invTypes', 'character_charactersheet_skills.typeID', '=', 'invTypes.typeID')
			->whereIn('character_charactersheet_skills.typeID', array_values(Input::get('skills')))
			->orderBy('invTypes.typeName')
			->groupBy('character_charactersheet_skills.characterID')
			->get();

		return View::make('character.skillsearch.ajax.result')
			->with('filter', $filter);
	}

	/*
	|--------------------------------------------------------------------------
	| getSearchAssets()
	|--------------------------------------------------------------------------
	|
	| Return a view to search character assets
	|
	*/

	public function getSearchAssets()
	{

		return View::make('character.assetsearch.search');
	}

	/*
	|--------------------------------------------------------------------------
	| postSearchAssets()
	|--------------------------------------------------------------------------
	|
	| Search for characters that have certain assets
	|
	*/

	public function postSearchAssets()
	{

		if (!is_array(Input::get('items')))
			App::abort(404);

		// Seriously need to fix up this shit SQL, but not sure how to get this
		// into Fluent correctly yet...

		// Create a parameter list for use in our query
		$plist = ':id_'.implode(',:id_', array_keys(Input::get('items')));
		// Prepare the arguement list for the parameters list
		$parms = array_combine(explode(",", $plist), Input::get('items'));

		$assets = DB::select(
			"SELECT *, CASE
				when a.locationID BETWEEN 66000000 AND 66014933 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.locationID-6000001)
				when a.locationID BETWEEN 66014934 AND 67999999 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.locationID-6000000)
				when a.locationID BETWEEN 60014861 AND 60014928 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.locationID)
				when a.locationID BETWEEN 60000000 AND 61000000 then
					(SELECT s.stationName FROM staStations AS s
					  WHERE s.stationID=a.locationID)
				when a.locationID>=61000000 then
					(SELECT c.stationName FROM `eve_conquerablestationlist` AS c
					  WHERE c.stationID=a.locationID)
				else (SELECT m.itemName FROM mapDenormalize AS m
					WHERE m.itemID=a.locationID) end
					AS location,a.locationId AS locID FROM `character_assetlist` AS a
					LEFT JOIN `invTypes` ON a.`typeID` = `invTypes`.`typeID`
					JOIN `account_apikeyinfo_characters` on `account_apikeyinfo_characters`.`characterID` = a.`characterID`
					WHERE `invTypes`.`typeID` IN ( $plist ) GROUP BY a.`characterID` ORDER BY location",
			$parms	
		);

		return View::make('character.assetsearch.ajax.result')
			->with('assets', $assets);
	}

	/*
	|--------------------------------------------------------------------------
	| getWalletDelta()
	|--------------------------------------------------------------------------
	|
	| Calculate the daily wallet balance delta for the last 30 days and return
	| the results as a json response
	|
	*/

	public function getWalletDelta($characterID)
	{

		$wallet_daily_delta = DB::table('character_walletjournal')
			->select(DB::raw('DATE(`date`) as day, IFNULL( SUM( amount ), 0 ) AS daily_delta'))
			// ->whereRaw('date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) and NOW()')
			->where('characterID', $characterID)
			->groupBy('day')
			->get();

		return Response::json($wallet_daily_delta);
	}
}
