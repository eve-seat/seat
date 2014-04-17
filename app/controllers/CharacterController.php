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
					WHERE a.`characterID` = ? ORDER BY location',
			array($characterID)
		);
	

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
			->with('assets', $assets);
	}

	/*
	|--------------------------------------------------------------------------
	| getSearchSkills()
	|--------------------------------------------------------------------------
	|
	| Calculate the daily wallet balance delta for the last 30 days and return
	| the results as a json response
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
	| Calculate the daily wallet balance delta for the last 30 days and return
	| the results as a json response
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
			->get();

		return View::make('character.skillsearch.ajax.result')
			->with('filter', $filter);
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
			->whereRaw('date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) and NOW()')
			->where('characterID', $characterID)
			->groupBy('day')
			->get();

		return Response::json($wallet_daily_delta);
	}
}
