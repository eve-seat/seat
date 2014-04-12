<?php

class CorporationController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| getListJournals()
	|--------------------------------------------------------------------------
	|
	| Get a list of the corporations that we can display wallet Journals for
	|
	*/

	public function getListJournals()
	{

		$corporations = DB::table('account_apikeyinfo')
			->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
			->where('account_apikeyinfo.type', 'Corporation')
			->get();

		return View::make('corporation.walletjournal.listjournals')
			->with('corporations', $corporations);
	}

	/*
	|--------------------------------------------------------------------------
	| getJournal()
	|--------------------------------------------------------------------------
	|
	| Display a worporation Wallet Journal
	|
	*/

	public function getJournal($corporationID)
	{

		$corporation_name = DB::table('account_apikeyinfo_characters')
			->where('corporationID', $corporationID)
			->first();

		$wallet_journal = DB::table('corporation_walletjournal')
			->join('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
			->join('corporation_corporationsheet_walletdivisions', 'corporation_walletjournal.accountKey', '=', 'corporation_corporationsheet_walletdivisions.accountKey')
			->where('corporation_walletjournal.corporationID', $corporationID)
			->where('corporation_corporationsheet_walletdivisions.corporationID', $corporationID)
			->orderBy('date', 'desc')
			->paginate(50);

		return View::make('corporation.walletjournal.walletjournal')
			->with('wallet_journal', $wallet_journal)
			->with('corporation_name', $corporation_name);
	}

	/*
	|--------------------------------------------------------------------------
	| getListTransactions()
	|--------------------------------------------------------------------------
	|
	| Get a list of the corporations that we can display wallet Journals for
	|
	*/

	public function getListTransactions()
	{

		$corporations = DB::table('account_apikeyinfo')
			->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
			->where('account_apikeyinfo.type', 'Corporation')
			->get();

		return View::make('corporation.wallettransactions.listtransactions')
			->with('corporations', $corporations);
	}

	/*
	|--------------------------------------------------------------------------
	| getTransactions()
	|--------------------------------------------------------------------------
	|
	| Display a worporation Wallet Journal
	|
	*/

	public function getTransactions($corporationID)
	{

		$corporation_name = DB::table('account_apikeyinfo_characters')
			->where('corporationID', $corporationID)
			->first();

		$wallet_transactions = DB::table('corporation_wallettransactions')
			->join('corporation_corporationsheet_walletdivisions', 'corporation_wallettransactions.accountKey', '=', 'corporation_corporationsheet_walletdivisions.accountKey')
			->where('corporation_wallettransactions.corporationID', $corporationID)
			->where('corporation_corporationsheet_walletdivisions.corporationID', $corporationID)
			->orderBy('transactionDateTime', 'desc')
			->paginate(50);

		return View::make('corporation.wallettransactions.wallettransactions')
			->with('wallet_transactions', $wallet_transactions)
			->with('corporation_name', $corporation_name);
	}

	/*
	|--------------------------------------------------------------------------
	| getListMemberTracking()
	|--------------------------------------------------------------------------
	|
	| Get a list of the corporations that we can display Member Tracking for
	|
	*/

	public function getListMemberTracking()
	{

		$corporations = DB::table('account_apikeyinfo')
			->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
			->where('account_apikeyinfo.type', 'Corporation')
			->get();

		return View::make('corporation.membertracking.listmembertracking')
			->with('corporations', $corporations);
	}

	/*
	|--------------------------------------------------------------------------
	| getMemberTracking()
	|--------------------------------------------------------------------------
	|
	| Display a corporations Members and related API Key Information
	|
	*/

	public function getMemberTracking($corporationID)
	{

		$members = DB::table(DB::raw('corporation_member_tracking as cmt'))
			->select(DB::raw('cmt.characterID, cmt.name, cmt.startDateTime, cmt.title, cmt.logonDateTime, cmt.logoffDateTime, cmt.location, cmt.shipType, k.keyID, k.isOk'))
			->leftJoin(DB::raw('account_apikeyinfo_characters'), 'cmt.characterID', '=', 'account_apikeyinfo_characters.characterID')
			->leftJoin(DB::raw('seat_keys as k'), 'account_apikeyinfo_characters.keyID', '=', 'k.keyID')
			->leftJoin(DB::raw('account_apikeyinfo as ap'), 'k.keyID', '=', 'ap.keyID')
			->where('cmt.corporationID', $corporationID)
			->orderBy('cmt.name', 'asc')
			->get();

		return View::make('corporation.membertracking.membertracking')
			->with('members', $members);
	}

	/*
	|--------------------------------------------------------------------------
	| getListStarBase()
	|--------------------------------------------------------------------------
	|
	| Get a list of the corporations that we can display Member Tracking for
	|
	*/

	public function getListStarBase()
	{

		$corporations = DB::table('account_apikeyinfo')
			->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
			->where('account_apikeyinfo.type', 'Corporation')
			->get();

		return View::make('corporation.starbase.liststarbase')
			->with('corporations', $corporations);
	}

	/*
	|--------------------------------------------------------------------------
	| getStarBase()
	|--------------------------------------------------------------------------
	|
	| List Corporation Starbase details.
	|
	|	TODO: Lots of calculations and information is still needed to be moved
	|	so that we can determine the amount of time left to in silos etc.
	|
	*/

	public function getStarBase($corporationID)
	{

		// The very first thing we should be doing is getting all of the starbases for the corporationID
		$starbases = DB::table('corporation_starbaselist')
			->select(
				'corporation_starbaselist.itemID',
				'corporation_starbaselist.moonID',
				'corporation_starbaselist.state',
				'corporation_starbaselist.stateTimeStamp',
				'corporation_starbaselist.onlineTimeStamp',
				'corporation_starbaselist.onlineTimeStamp',
				'corporation_starbasedetail.allowCorporationMembers',
				'corporation_starbasedetail.allowAllianceMembers',
				'corporation_starbasedetail.fuelBlocks',
				'corporation_starbasedetail.strontium',
				'invTypes.typeID',
				'invTypes.typeName',
				'mapDenormalize.itemName',
				'invNames.itemName',
				'map_sovereignty.solarSystemName'
			)
			->join('corporation_starbasedetail', 'corporation_starbaselist.itemID', '=', 'corporation_starbasedetail.itemID')
			->join('mapDenormalize', 'corporation_starbaselist.locationID', '=', 'mapDenormalize.itemID')
			->join('invNames', 'corporation_starbaselist.moonID', '=', 'invNames.itemID')
			->join('invTypes', 'corporation_starbaselist.typeID', '=', 'invTypes.typeID')
			->leftJoin('map_sovereignty', 'corporation_starbaselist.locationID', '=', 'map_sovereignty.solarSystemID')
			->where('corporation_starbaselist.corporationID', $corporationID)
			->orderBy('invNames.itemName', 'asc')
			->get();

		// With the list of starbases with us, lets get some meta information about the bay sizes for towers
		// for some calculations later in the view
		$bay_sizes = DB::table('invTypes')
			->select('invTypes.typeID', 'invTypes.typeName', 'invTypes.capacity', 'dgmTypeAttributes.valueFloat')
			->join('dgmTypeAttributes', 'invTypes.typeID', '=', 'dgmTypeAttributes.typeID')
			->where('dgmTypeAttributes.attributeID', 1233)
			->where('invTypes.groupID', 365)
			->get();

		// We need an array with the typeID as the key to easily determine the bay size. Shuffle it
		$shuffled_bays = array();
		foreach ($bay_sizes as $bay)
			$shuffled_bays[$bay->typeID] = array('fuelBay' => $bay->capacity, 'strontBay' => $bay->valueFloat);

		// Next, lets see which of this corporations' towers appear to be anchored in sov holding systems.
		// TODO: Check that the sov holder is actually the alliance the corporation is in
		$sov_towers = array_flip(DB::table('corporation_starbaselist')
			->select('itemID')
			->whereIn('locationID', function($location) {
				$location->select('solarSystemID')
					->from('map_sovereignty')
					->where('factionID', 0);
			})->where('corporationID', $corporationID)
			->lists('itemID'));

		// Lets get all of the item locations for this corporation and sort it out into a workable
		// array that can just be referenced and looped in the view. We will use the mapID as the
		// key in the resulting array to be able to associate the item to a tower
		$item_locations = DB::table('corporation_assetlist_locations')
			->where('corporationID', $corporationID)
			->get();

		// Shuffle the results
		$shuffled_locations = array();
		foreach ($item_locations as $location)
			$shuffled_locations[$location->mapID][] = array('itemID' => $location->itemID, 'itemName' => $location->itemName, 'mapName' => $location->mapName);

		// We will do a similar shuffle for the assetlist contents. First get them, and shuffle.
		// The key for this array will be the itemID as there may be multiple 'things' in a 'thing'
		$item_contents = DB::table('corporation_assetlist_contents')
			->join('invTypes', 'corporation_assetlist_contents.typeID', '=', 'invTypes.typeID')
			->where('corporationID', $corporationID)
			->get();

		// Shuffle the results
		$shuffled_contents = array();
		foreach ($item_contents as $contents)
			$shuffled_contents[$contents->itemID][] = array('quantity' => $contents->quantity, 'name' => $contents->typeName);

		// Define the tower states. See http://3rdpartyeve.net/eveapi/APIv2_Corp_StarbaseList_XML
		$tower_states = array(
		    '0' => 'Unanchored',
		    '1' => 'Anchored / Offline',
		    '2' => 'Onlining',
		    '3' => 'Reinforced',
		    '4' => 'Online'
		);

		return View::make('corporation.starbase.starbase')
			->with('starbases', $starbases)
			->with('bay_sizes', $shuffled_bays)
			->with('sov_towers', $sov_towers)
			->with('item_locations', $shuffled_locations)
			->with('item_contents', $shuffled_contents)
			->with('tower_states', $tower_states);
	}
}
