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

use App\Services\Helpers\Helpers;

class CorporationController extends BaseController
{

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

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getJournal', array($corporations[0]->corporationID));

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

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('wallet_manager'))
                App::abort(404);

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
            ->with('corporation_name', $corporation_name)
            ->with('corporationID', $corporationID);
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

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getTransactions', array($corporations[0]->corporationID));

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

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('wallet_manager'))
                App::abort(404);

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

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getMemberTracking', array($corporations[0]->corporationID));

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

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('recruiter'))
                App::abort(404);

        $members = DB::table(DB::raw('corporation_member_tracking as cmt'))
            ->select(DB::raw('cmt.characterID, cmt.name, cmt.startDateTime, cmt.title, cmt.logonDateTime, cmt.logoffDateTime, cmt.location, cmt.shipType, k.keyID, k.isOk'))
            ->leftJoin(DB::raw('account_apikeyinfo_characters'), 'cmt.characterID', '=', 'account_apikeyinfo_characters.characterID')
            ->leftJoin(DB::raw('seat_keys as k'), 'account_apikeyinfo_characters.keyID', '=', 'k.keyID')
            ->leftJoin(DB::raw('account_apikeyinfo as ap'), 'k.keyID', '=', 'ap.keyID')
            ->where('cmt.corporationID', $corporationID)
            ->orderBy('k.isOk', 'asc')
            ->orderBy('cmt.name', 'asc')
            ->groupBy('cmt.characterID')
            ->get();

        // Set an array with the character info that we have
        $member_info = null;
        foreach (DB::table('eve_characterinfo')->get() as $character)
            $member_info[$character->characterID] = $character;

        return View::make('corporation.membertracking.membertracking')
            ->with('members', $members)
            ->with('member_info', $member_info);
    }

    /*
    |--------------------------------------------------------------------------
    | getListAssets()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display Member Tracking for
    |
    */

    public function getListAssets()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getAssets', array($corporations[0]->corporationID));

        return View::make('corporation.assets.listasset')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getAssets()
    |--------------------------------------------------------------------------
    |
    | Display a corporations Members and related API Key Information
    |
    */

    public function getAssets($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('asset_manager'))
                App::abort(404);

        $corporation_name = DB::table('account_apikeyinfo_characters')
            ->where('corporationID', $corporationID)
            ->first();

        // try and move this shit to a query builder / eloquent version... :<
        $assets = DB::select(
            "SELECT *,
                CASE
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
                AS location,a.locationId AS locID FROM `corporation_assetlist` AS a
                LEFT JOIN `invTypes` ON a.`typeID` = `invTypes`.`typeID`
                LEFT JOIN `invGroups` ON `invTypes`.`groupID` = `invGroups`.`groupID`
                WHERE a.`corporationID` = ?
            ORDER BY location",
            array($corporationID)
        );

        // Query Asset content from corporation_assetlist_contents DB and sum the quantity for not getting a long list of item
        $assets_contents = DB::table(DB::raw('corporation_assetlist_contents as a'))
            ->select(DB::raw('*'), DB::raw('SUM(a.quantity) as sumquantity'))
            ->leftJoin('invTypes', 'a.typeID', '=', 'invTypes.typeID')
            ->leftJoin('invGroups', 'invTypes.groupID', '=', 'invGroups.groupID')
            ->where('a.corporationID', $corporationID)
            ->groupBy(DB::raw('a.itemID, a.typeID'))
            ->get();

        // Lastly, create an array that is easy to loop over in the template to display
        // the data, split into those assets in a station and those assets in space

        // Declare empty variables
        $station_assets_list = $space_assets_list = array();
        $station_assets_count = $space_assets_count = 0; //start counting item

        // Loop over and split by locationID 60000000-67999999 are stationIDs
        foreach ($assets as $key => $value) {

            if($value->locationID > 60000000 && $value->locationID < 67999999) {

                $station_assets_list[$value->location][$value->itemID] = array(
                    'quantity' => $value->quantity,
                    'typeID' => $value->typeID,
                    'typeName' => $value->typeName,
                    'groupName' => $value->groupName,
                    'volume' => $value->volume * $value->quantity,
                );

                $station_assets_count++;

                foreach( $assets_contents as $contents) {

                    // Check the parents content
                    if ($value->itemID == $contents->itemID) {

                        // create a sub array 'contents' and put content item info in
                        $station_assets_list[$value->location][$contents->itemID]['contents'][] = array(
                            'quantity' => $contents->sumquantity,
                            'typeID' => $contents->typeID,
                            'typeName' => $contents->typeName,
                            'groupName' => $contents->groupName,
                            'volume' => $contents->volume * $contents->sumquantity
                        );

                        $station_assets_count++;
                    }
                }

            } else {

                $space_assets_list[$value->location][$value->itemID] = array(
                    'quantity' => $value->quantity,
                    'typeID' => $value->typeID,
                    'typeName' => $value->typeName,
                    'groupName' => $value->groupName,
                    'volume' => $value->volume * $value->quantity,
                );

                $space_assets_count++;

                foreach( $assets_contents as $contents){

                    // Check the parents content
                    if ($value->itemID == $contents->itemID) {

                        // create a sub array 'contents' and put content item info in
                        $space_assets_list[$value->location][$contents->itemID]['contents'][] = array(
                            'quantity' => $contents->sumquantity,
                            'typeID' => $contents->typeID,
                            'typeName' => $contents->typeName,
                            'groupName' => $contents->groupName,
                            'volume' => $contents->volume * $contents->sumquantity
                        );

                        $space_assets_count++;
                    }
                }
            }
        }

        return View::make('corporation.assets.assets')
            ->with('corporation_name', $corporation_name)
            ->with('assets', $assets)
            ->with('assets_contents', $assets_contents)
            ->with('station_assets_list', $station_assets_list)
            ->with('station_assets_count', $station_assets_count)
            ->with('space_assets_list', $space_assets_list)
            ->with('space_assets_count', $space_assets_count);
    }

    /*
    |--------------------------------------------------------------------------
    | getListContracts()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display Member Tracking for
    |
    */

    public function getListContracts()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getContracts', array($corporations[0]->corporationID));

        return View::make('corporation.contracts.listcontract')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getContracts()
    |--------------------------------------------------------------------------
    |
    | Display a corporations Members and related API Key Information
    |
    */

    public function getContracts($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('contract_manager'))
                App::abort(404);

        $corporation_name = DB::table('account_apikeyinfo_characters')
            ->where('corporationID', $corporationID)
            ->first();

        // Contract list
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
                FROM `corporation_contracts` AS a
                    WHERE a.`corporationID` = ?',
            array($corporationID)
        );

        // Character contract item
        $contract_list_item = DB::table('corporation_contracts_items')
            ->leftJoin('invTypes', 'corporation_contracts_items.typeID', '=', 'invTypes.typeID')
            ->where('corporationID', $corporationID)
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

                // Check the parents content
                if ($value->contractID == $contents->contractID)

                    // create a sub array 'contents' and put content item info in
                    $contracts_other[$value->contractID]['contents'][] = array(
                        'quantity' => $contents->quantity,
                        'typeID' => $contents->typeID,
                        'typeName' => $contents->typeName,
                        'included' => $contents->included // for "buyer will pay" item
                    );
            }
        }


        return View::make('corporation.contracts.contracts')
            ->with('corporation_name', $corporation_name)
            ->with('contracts_courier', $contracts_courier)
            ->with('contracts_other', $contracts_other);
    }

    /*
    |--------------------------------------------------------------------------
    | getListStarbase()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display Member Tracking for
    |
    */

    public function getListStarbase()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getStarbase', array($corporations[0]->corporationID));

        return View::make('corporation.starbase.liststarbase')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getStarbase()
    |--------------------------------------------------------------------------
    |
    | List Corporation Starbase details.
    |
    |   TODO: Lots of calculations and information is still needed to be moved
    |   so that we can determine the amount of time left to in silos etc.
    |
    */

    public function getStarbase($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('pos_manager'))
                App::abort(404);

        // The very first thing we should be doing is getting all of the starbases for the corporationID
        $starbases = DB::table('corporation_starbaselist')
            ->select(
                'corporation_starbaselist.itemID',
                'corporation_starbaselist.moonID',
                'corporation_starbaselist.state',
                'corporation_starbaselist.stateTimeStamp',
                'corporation_starbaselist.onlineTimeStamp',
                'corporation_starbaselist.onlineTimeStamp',
                'corporation_starbasedetail.useStandingsFrom',
                'corporation_starbasedetail.onAggression',
                'corporation_starbasedetail.onCorporationWar',
                'corporation_starbasedetail.allowCorporationMembers',
                'corporation_starbasedetail.allowAllianceMembers',
                'corporation_starbasedetail.fuelBlocks',
                'corporation_starbasedetail.strontium',
                'corporation_starbasedetail.starbaseCharter',
                'invTypes.typeID',
                'invTypes.typeName',
                'mapDenormalize.itemName',
                'mapDenormalize.security',
                'invNames.itemName',
                'map_sovereignty.solarSystemName',
                'corporation_starbasedetail.updated_at'
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

        // When calculating *actual* silo capacity, we need to keep in mind that certain towers have bonusses
        // to silo cargo capacity, like amarr & gallente towers do now. Get the applicable bonusses
        $tower_bay_bonuses = DB::table('dgmTypeAttributes')
            ->select('typeID', 'valueFloat')
            ->where('attributeID', 757)     // From dgmAttributeTypes, 757 = controlTowerSiloCapacityBonus
            ->get();

        // Reshuffle the tower_bay_bonuses into a workable array for the view
        $tower_cargo_bonusses = array();
        foreach ($tower_bay_bonuses as $bonus)
            $tower_cargo_bonusses[$bonus->typeID] = $bonus->valueFloat;

        // Not all modules are bonnussed in size. As far as I can tell, it only seems to be coupling arrays and
        // silos that benefit from the silo bay size bonus. Set an array with these type ids
        $cargo_size_bonusable_modules = array(14343, 17982);    // Silo, Coupling Array

        // Figure out the allianceID of the corporation in question so that we can determine whether their
        // towers are in sov systems
        $alliance_id = DB::table('corporation_corporationsheet')
            ->where('corporationID', $corporationID)
            ->pluck('allianceID');

        // Check if the alliance_id was actually determined. If so, do the sov_towers loop, else
        // we just set the array empty
        if ($alliance_id) {

            // Lets see which of this corporations' towers appear to be anchored in sov holding systems.
            $sov_towers = array_flip(DB::table('corporation_starbaselist')
                ->select('itemID')
                ->whereIn('locationID', function($location) use ($alliance_id) {

                    $location->select('solarSystemID')
                        ->from('map_sovereignty')
                        ->where('factionID', 0)
                        ->where('allianceID', $alliance_id);
                })->where('corporationID', $corporationID)
                ->lists('itemID'));
        } else {

            // We will just have an empty array then
            $sov_towers = array();
        }

        // Lets get all of the item locations for this corporation and sort it out into a workable
        // array that can just be referenced and looped in the view. We will use the mapID as the
        // key in the resulting array to be able to associate the item to a tower.
        $item_locations = DB::table('corporation_assetlist_locations')
            ->leftJoin('corporation_assetlist', 'corporation_assetlist_locations.itemID', '=', 'corporation_assetlist.itemID')
            ->leftJoin('invTypes', 'corporation_assetlist.typeID', '=', 'invTypes.typeID')
            ->where('corporation_assetlist_locations.corporationID', $corporationID)
            ->get();

        // Shuffle the results
        $shuffled_locations = array();
        foreach ($item_locations as $location)
            $shuffled_locations[$location->mapID][] = array(
                'itemID' => $location->itemID,
                'typeID' => $location->typeID,
                'typeName' => $location->typeName,
                'itemName' => $location->itemName,
                'mapName' => $location->mapName,
                'capacity' => $location->capacity
            );

        // We will do a similar shuffle for the assetlist contents. First get them, and shuffle.
        // The key for this array will be the itemID as there may be multiple 'things' in a 'thing'
        $item_contents = DB::table('corporation_assetlist_contents')
            ->join('invTypes', 'corporation_assetlist_contents.typeID', '=', 'invTypes.typeID')
            ->where('corporationID', $corporationID)
            ->get();

        // Shuffle the results
        $shuffled_contents = array();
        foreach ($item_contents as $contents)
            $shuffled_contents[$contents->itemID][] = array(
                'typeID' => $contents->typeID,
                'quantity' => $contents->quantity,
                'name' => $contents->typeName,
                'volume' => $contents->volume
            );

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
            ->with('tower_cargo_bonusses', $tower_cargo_bonusses)
            ->with('cargo_size_bonusable_modules', $cargo_size_bonusable_modules)
            ->with('sov_towers', $sov_towers)
            ->with('item_locations', $shuffled_locations)
            ->with('item_contents', $shuffled_contents)
            ->with('tower_states', $tower_states);
    }

    /*
    |--------------------------------------------------------------------------
    | getListLedgers()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display ledgers for
    |
    */

    public function getListLedgers()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getLedgerSummary', array($corporations[0]->corporationID));

        return View::make('corporation.ledger.listledger')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getLedgerSummary()
    |--------------------------------------------------------------------------
    |
    | Display a corporation Wallet Journal
    |
    */

    public function getLedgerSummary($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('wallet_manager'))
                App::abort(404);

        // Get the month/year data
        $available_dates = DB::table('corporation_walletjournal')
            ->select(DB::raw('DISTINCT(MONTH(date)) AS month, YEAR(date) AS year'))
            ->where('corporationID', $corporationID)
            ->orderBy(DB::raw('year, month'), 'desc')
            ->get();

        // Parse the available dates and sort the array
        $ledger_dates = array();
        foreach ($available_dates as $date)
            $ledger_dates[] = Carbon\Carbon::createFromDate($date->year, $date->month)->toDateString();

        arsort($ledger_dates);

        // Get some data for the global ledger prepared

        // Current Corporation Wallet Balances
        $wallet_balances = DB::table('corporation_accountbalance')
            ->join('corporation_corporationsheet_walletdivisions', 'corporation_accountbalance.accountKey', '=', 'corporation_corporationsheet_walletdivisions.accountKey')
            ->where('corporation_corporationsheet_walletdivisions.corporationID', $corporationID)
            ->where('corporation_accountbalance.corporationID', $corporationID)
            ->get();

        // The overall corporation ledger. We will loop over the wallet divisions
        // and get the ledger calculated for each
        $ledgers = array();

        foreach (EveCorporationCorporationSheetWalletDivisions::where('corporationID', $corporationID)->get() as $division) {

            $ledgers[$division->accountKey] = array(
                'divisionName' => $division->description,
                'ledger' => DB::table('corporation_walletjournal')
                    ->select('refTypeName', DB::raw('sum(`amount`) `total`'))
                    ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
                    ->where('corporation_walletjournal.accountKey', $division->accountKey)
                    ->where('corporation_walletjournal.corporationID', $corporationID)
                    ->groupBy('corporation_walletjournal.refTypeID')
                    ->orderBy('refTypeName')
                    ->get(),
                'total' => DB::table('corporation_walletjournal')
                    ->select(DB::raw('sum(`amount`) `total`'))
                    ->where('corporation_walletjournal.accountKey', $division->accountKey)
                    ->where('corporation_walletjournal.corporationID', $corporationID)
                    ->pluck('total')
            );
        }

        // Tax contributions
        $bounty_tax = DB::table('corporation_walletjournal')
            ->select('ownerID2', 'ownerName2', DB::raw('SUM(corporation_walletjournal.amount) total'))
            ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->whereIn('corporation_walletjournal.refTypeID', array(17,85)) // Ref type ids: 17: Bounty Prize; 85: Bounty Prizes
            ->where('corporation_walletjournal.corporationID', $corporationID)
            ->groupBy('corporation_walletjournal.ownerName2')
            ->orderBy('total', 'desc')
            ->get();

        $mission_tax = DB::table('corporation_walletjournal')
            ->select('ownerID2', 'ownerName2', DB::raw('SUM(corporation_walletjournal.amount) total'))
            ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->whereIn('corporation_walletjournal.refTypeID', array(33,34)) // Ref type ids: 33: Mission Reward; 34: Mission Time Bonus
            ->where('corporation_walletjournal.corporationID', $corporationID)
            ->groupBy('corporation_walletjournal.ownerName2')
            ->orderBy('total', 'desc')
            ->get();

        $pi_tax = DB::table('corporation_walletjournal')
            ->select('ownerID1', 'ownerName1', DB::raw('SUM(corporation_walletjournal.amount) total'))
            ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->whereIn('corporation_walletjournal.refTypeID', array(96,97,98)) // Ref type ids: 96: Planetary Import Tax; 97: Planetary Export Tax; 98: Planetary Construction
            ->where('corporation_walletjournal.corporationID', $corporationID)
            ->groupBy('corporation_walletjournal.ownerName1')
            ->orderBy('total', 'desc')
            ->get();

        return View::make('corporation.ledger.ledger')
            ->with('corporationID', $corporationID)
            ->with('ledger_dates', $ledger_dates)
            ->with('wallet_balances', $wallet_balances)
            ->with('ledgers', $ledgers)
            ->with('bounty_tax', $bounty_tax)
            ->with('mission_tax', $mission_tax)
            ->with('pi_tax', $pi_tax);
    }

    /*
    |--------------------------------------------------------------------------
    | getLedgerMonth()
    |--------------------------------------------------------------------------
    |
    | Display a corporation Wallet Journal for a specific date
    |
    | Very large amount of code re-use here and in getLedgerSummary() so need
    | look at avoiding this.
    |
    */

    public function getLedgerMonth($corporationID, $date)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('wallet_manager'))
                App::abort(404);

        // TODO: Add check to ensure corporation exists

        // Parse the date
        $month = Carbon\Carbon::parse($date)->month;
        $year = Carbon\Carbon::parse($date)->year;

        // The overall corporation ledger. We will loop over the wallet divisions
        // and get the ledger calculated for each
        $ledgers = array();

        foreach (EveCorporationCorporationSheetWalletDivisions::where('corporationID', $corporationID)->get() as $division) {

            $ledgers[$division->accountKey] = array(
                'divisionName' => $division->description,
                'ledger' => DB::table('corporation_walletjournal')
                    ->select('refTypeName', DB::raw('sum(`amount`) `total`'))
                    ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
                    ->where('corporation_walletjournal.accountKey', $division->accountKey)
                    ->where(DB::raw('MONTH(date)'), $month)
                    ->where(DB::raw('YEAR(date)'), $year)
                    ->where('corporation_walletjournal.corporationID', $corporationID)
                    ->groupBy('corporation_walletjournal.refTypeID')
                    ->orderBy('refTypeName')
                    ->get(),
                'total' => DB::table('corporation_walletjournal')
                    ->select(DB::raw('sum(`amount`) `total`'))
                    ->where('corporation_walletjournal.accountKey', $division->accountKey)
                    ->where('corporation_walletjournal.corporationID', $corporationID)
                    ->where(DB::raw('MONTH(date)'), $month)
                    ->where(DB::raw('YEAR(date)'), $year)
                    ->pluck('total')
            );
        }

        // Tax contributions
        $bounty_tax = DB::table('corporation_walletjournal')
            ->select('ownerID2', 'ownerName2', DB::raw('SUM(corporation_walletjournal.amount) total'))
            ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->whereIn('corporation_walletjournal.refTypeID', array(17,85)) // Ref type ids: 17: Bounty Prize; 85: Bounty Prizes
            ->where(DB::raw('MONTH(date)'), $month)
            ->where(DB::raw('YEAR(date)'), $year)
            ->where('corporation_walletjournal.corporationID', $corporationID)
            ->groupBy('corporation_walletjournal.ownerName2')
            ->orderBy('total', 'desc')
            ->get();

        $mission_tax = DB::table('corporation_walletjournal')
            ->select('ownerID2', 'ownerName2', DB::raw('SUM(corporation_walletjournal.amount) total'))
            ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->whereIn('corporation_walletjournal.refTypeID', array(33,34)) // Ref type ids: 33: Mission Reward; 34: Mission Time Bonus
            ->where(DB::raw('MONTH(date)'), $month)
            ->where(DB::raw('YEAR(date)'), $year)
            ->where('corporation_walletjournal.corporationID', $corporationID)
            ->groupBy('corporation_walletjournal.ownerName2')
            ->orderBy('total', 'desc')
            ->get();

        $pi_tax = DB::table('corporation_walletjournal')
            ->select('ownerID1', 'ownerName1', DB::raw('SUM(corporation_walletjournal.amount) total'))
            ->leftJoin('eve_reftypes', 'corporation_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->whereIn('corporation_walletjournal.refTypeID', array(96,97,98)) // Ref type ids: 96: Planetary Import Tax; 97: Planetary Export Tax; 98: Planetary Construction
            ->where(DB::raw('MONTH(date)'), $month)
            ->where(DB::raw('YEAR(date)'), $year)
            ->where('corporation_walletjournal.corporationID', $corporationID)
            ->groupBy('corporation_walletjournal.ownerName1')
            ->orderBy('total', 'desc')
            ->get();

        return View::make('corporation.ledger.ajax.ledgermonth')
            ->with('corporationID', $corporationID)
            ->with('date', $date)
            ->with('ledgers', $ledgers)
            ->with('bounty_tax', $bounty_tax)
            ->with('mission_tax', $mission_tax)
            ->with('pi_tax', $pi_tax);
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

    public function getWalletDelta($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('wallet_manager'))
                App::abort(404);

        $wallet_daily_delta = DB::table('corporation_walletjournal')
            ->select(DB::raw('DATE(`date`) as day, IFNULL( SUM( amount ), 0 ) AS daily_delta'))
            ->where('corporationID', $corporationID)
            ->groupBy('day')
            ->get();

        return Response::json($wallet_daily_delta);
    }


    /*
    |--------------------------------------------------------------------------
    | getListSheets()
    |--------------------------------------------------------------------------
    |
    | Overview for getSheet()
    |
    */

    public function getListMemberSecurity()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getMemberSecurity', array($corporations[0]->corporationID));

        return View::make('corporation.membersecurity.listmembersecurity')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getSheet()
    |--------------------------------------------------------------------------
    |
    | Display a corporations Members and related Security Information
    |
    */

    public function getMemberSecurity($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('recruiter'))
                App::abort(404);

        $member_roles = DB::table('corporation_msec_roles as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_base = DB::table('corporation_msec_roles_at_base as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_hq = DB::table('corporation_msec_roles_at_hq as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_other = DB::table('corporation_msec_roles_at_other as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_grantable = DB::table('corporation_msec_grantable_roles as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_grantable_base = DB::table('corporation_msec_grantable_roles_at_base as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_grantable_hq = DB::table('corporation_msec_grantable_roles_at_hq as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();

        $member_roles_grantable_other = DB::table('corporation_msec_grantable_roles_at_other as cmr')
            ->select(DB::raw('cmr.characterID, cmr.name, GROUP_CONCAT(rolemap.roleName SEPARATOR \',\') AS roleName'))
            ->join(DB::raw('eve_corporation_rolemap as rolemap'),'cmr.roleID','=','rolemap.roleID')
            ->where('cmr.corporationID', $corporationID)
            ->groupBy('cmr.characterID')
            ->orderBy('cmr.name', 'asc')
            ->get();


        $member_roles_log = DB::table('corporation_msec_log as cml')
            ->select('cml.characterID', 'cmt.name as characterName', 'cml.issuerID', 'cml.issuerName', 'cml.changeTime', 'cmld.oldRoles', 'cmld.newRoles','cml.roleLocationType')
            ->join(DB::raw('corporation_msec_log_details as cmld'),'cml.hash','=','cmld.hash')
            ->join(DB::raw('corporation_member_tracking as cmt'),'cml.characterID','=','cmt.characterID')
            ->where('cml.corporationID', $corporationID)
            ->orderBy('cml.changeTime', 'desc')
            ->get();

        return View::make('corporation.membersecurity.membersecurity')
            ->with('member_roles',                  $member_roles)
            ->with('member_roles_hq',               $member_roles_hq)
            ->with('member_roles_base',             $member_roles_base)
            ->with('member_roles_other',            $member_roles_other)
            ->with('member_roles_grantable',        $member_roles_grantable)
            ->with('member_roles_grantable_hq',     $member_roles_grantable_hq)
            ->with('member_roles_grantable_base',   $member_roles_grantable_base)
            ->with('member_roles_grantable_other',  $member_roles_grantable_other)
            ->with('member_roles_log',              $member_roles_log)
        ;
    }

    /*
    |--------------------------------------------------------------------------
    | getListMarketOrders()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display market orders for
    |
    */

    public function getListMarketOrders()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getMarketOrders', array($corporations[0]->corporationID));

        return View::make('corporation.marketorders.listmarketorders')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getMarketOrders()
    |--------------------------------------------------------------------------
    |
    | Display the corporation Market orders
    |
    */

    public function getMarketOrders($corporationID)
    {

        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('market_manager'))
                App::abort(404);

        $corporation_name = DB::table('account_apikeyinfo_characters')
            ->where('corporationID', $corporationID)
            ->first();

        // Corporation Market Orders
        $market_orders = DB::select(
            'SELECT *, CASE
                when a.stationID BETWEEN 66000000 AND 66014933 then
                    (SELECT s.stationName FROM staStations AS s
                      WHERE s.stationID=a.stationID-6000001)
                when a.stationID BETWEEN 66014934 AND 67999999 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                      WHERE c.stationID=a.stationID-6000000)
                when a.stationID BETWEEN 60014861 AND 60014928 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                      WHERE c.stationID=a.stationID)
                when a.stationID BETWEEN 60000000 AND 61000000 then
                    (SELECT s.stationName FROM staStations AS s
                      WHERE s.stationID=a.stationID)
                when a.stationID>=61000000 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                      WHERE c.stationID=a.stationID)
                else (SELECT m.itemName FROM mapDenormalize AS m
                    WHERE m.itemID=a.stationID) end
                    AS location,a.stationID AS locID FROM `corporation_marketorders` AS a
                    LEFT JOIN `invTypes` ON a.`typeID` = `invTypes`.`typeID`
                    LEFT JOIN `invGroups` ON `invTypes`.`groupID` = `invGroups`.`groupID`
                    WHERE a.`corporationID` = ? ORDER BY a.orderState, a.issued desc',
            array($corporationID)
        );

        // Order states from: https://neweden-dev.com/Character/Market_Orders
        //orderState     byte
        // Valid states: 0 = open/active, 1 = closed, 2 = expired (or fulfilled), 3 = cancelled, 4 = pending, 5 = character deleted.
        $order_states = array(
            '0' => 'Active',
            '1' => 'Closed',
            '2' => 'Expired / Fulfilled',
            '3' => 'Cancelled',
            '4' => 'Pending',
            '5' => 'Deleted'
        );

        $wallet_divisions_data = DB::table('corporation_corporationsheet_walletdivisions')
            ->select('accountKey', 'description')
            ->where('corporationID', $corporationID)
            ->get();

        // Set the key for the wallet divisions array
        $wallet_divisions = array();
        foreach ($wallet_divisions_data as $division)
            $wallet_divisions[$division->accountKey] = $division->description;

        return View::make('corporation.marketorders.marketorders')
            ->with('market_orders', $market_orders)
            ->with('order_states', $order_states)
            ->with('wallet_divisions', $wallet_divisions)
            ->with('corporation_name', $corporation_name);
    }

    /*
    |--------------------------------------------------------------------------
    | getListMemberStandings()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display market orders for
    |
    */

    public function getListMemberStandings()
    {

        $corporations = DB::table('account_apikeyinfo')
            ->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
            ->where('account_apikeyinfo.type', 'Corporation')
            ->get();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getMemberStandings', array($corporations[0]->corporationID));

        return View::make('corporation.memberstandings.listmemberstandings')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getMemberStandings()
    |--------------------------------------------------------------------------
    |
    | Display the corporation Member Standings
    |
    */

    public function getMemberStandings($corporationID)
    {

        $corporation_name = DB::table('account_apikeyinfo_characters')
            ->where('corporationID', $corporationID)
            ->first();

        $agent_standings = DB::table('corporation_standings_agents')
            ->where('corporationID', $corporationID)
            ->get();

        $faction_standings = DB::table('corporation_standings_factions')
            ->where('corporationID', $corporationID)
            ->get();

        $npc_standings = DB::table('corporation_standings_npccorporations')
            ->where('corporationID', $corporationID)
            ->get();

        return View::make('corporation.memberstandings.memberstandings')
            ->with('agent_standings', $agent_standings)
            ->with('faction_standings', $faction_standings)
            ->with('npc_standings', $npc_standings)
            ->with('corporation_name', $corporation_name);
    }

    /*
    |--------------------------------------------------------------------------
    | getListKillMails()
    |--------------------------------------------------------------------------
    |
    | Get a list of the corporations that we can display killmails for
    |
    */

    public function getListKillMails()
    {

        $corporations = DB::table('account_apikeyinfo')
            ->join('account_apikeyinfo_characters', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
            ->where('account_apikeyinfo.type', 'Corporation')
            ->get();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getKillMails', array($corporations[0]->corporationID));

        return View::make('corporation.killmails.listkillmails')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getKillMails()
    |--------------------------------------------------------------------------
    |
    | Display the corporation Kill Mails
    |
    */

    public function getKillMails($corporationID)
    {

        $corporation_name = DB::table('account_apikeyinfo_characters')
            ->where('corporationID', $corporationID)
            ->first();

        $killmails = DB::table('corporation_killmails')
            ->select(DB::raw('*, `mapDenormalize`.`itemName` AS solarSystemName'))
            ->leftJoin('corporation_killmail_detail', 'corporation_killmails.killID', '=', 'corporation_killmail_detail.killID')
            ->leftJoin('invTypes', 'corporation_killmail_detail.shipTypeID', '=', 'invTypes.typeID')
            ->leftJoin('mapDenormalize', 'corporation_killmail_detail.solarSystemID', '=', 'mapDenormalize.itemID')
            ->where('corporation_killmails.corporationID', $corporationID)
            ->orderBy('corporation_killmail_detail.killTime', 'desc')
            ->get();

        return View::make('corporation.killmails.killmails')
            ->with('killmails', $killmails)
            ->with('corporationID', $corporationID)
            ->with('corporation_name', $corporation_name);
    }

    /*
    |--------------------------------------------------------------------------
    | getListIndustry()
    |--------------------------------------------------------------------------
    |
    | Display the list of corporations that we can display industry for
    |
    */
    public function getListIndustry()
    {

        $corporations = Helpers::getCorporationList();

        if(count($corporations) == 1)
            return Redirect::action('CorporationController@getIndustry', array($corporations[0]->corporationID));

        return View::make('corporation.industry.listindustry')
            ->with('corporations', $corporations);
    }

    /*
    |--------------------------------------------------------------------------
    | getJobs()
    |--------------------------------------------------------------------------
    |
    | Returns the jobs history (running and ended)
    |
    */

    public function getIndustry($corporationID)
    {

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs
        if (!\Auth::isSuperUser() )
            if (!in_array($corporationID, Session::get('valid_keys')) && !\Auth::hasAccess('asset_manager'))
                App::abort(404);

        // Get current working jobs
        $current_jobs = DB::table('corporation_industryjobs as a')
            ->select(DB::raw("
                *, CASE
                when a.stationID BETWEEN 66000000 AND 66014933 then
                    (SELECT s.stationName FROM staStations AS s
                     WHERE s.stationID=a.stationID-6000001)
                when a.stationID BETWEEN 66014934 AND 67999999 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                     WHERE c.stationID=a.stationID-6000000)
                when a.stationID BETWEEN 60014861 AND 60014928 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                     WHERE c.stationID=a.stationID)
                when a.stationID BETWEEN 60000000 AND 61000000 then
                    (SELECT s.stationName FROM staStations AS s
                     WHERE s.stationID=a.stationID)
                when a.stationID>=61000000 then
                    (SELECT IFNULL(
                        (SELECT mapName FROM corporation_assetlist_locations WHERE itemID = a.stationID),
                            (IFNULL((SELECT stationName FROM eve_conquerablestationlist WHERE stationID = a.stationID),
                                (SELECT solarSystemName FROM corporation_industryjobs WHERE id = a.id)
                            ))
                    ))
                else (SELECT m.itemName FROM mapDenormalize AS m
                      WHERE m.itemID=a.stationID)
                end
                AS location, a.stationID as locID"))
            ->where('a.corporationID', $corporationID)
            ->where('endDate', '>', date('Y-m-d H:i:s'))
            ->orderBy('endDate', 'asc')
            ->get();

        // Get the passed jobs
        $finished_jobs = DB::table('corporation_industryjobs as a')
            ->select(DB::raw("
                *, CASE
                when a.stationID BETWEEN 66000000 AND 66014933 then
                    (SELECT s.stationName FROM staStations AS s
                     WHERE s.stationID=a.stationID-6000001)
                when a.stationID BETWEEN 66014934 AND 67999999 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                     WHERE c.stationID=a.stationID-6000000)
                when a.stationID BETWEEN 60014861 AND 60014928 then
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                     WHERE c.stationID=a.stationID)
                when a.stationID BETWEEN 60000000 AND 61000000 then
                    (SELECT s.stationName FROM staStations AS s
                     WHERE s.stationID=a.stationID)
                when a.stationID>=61000000 then
                    (SELECT IFNULL(
                        (SELECT mapName FROM corporation_assetlist_locations WHERE itemID = a.stationID),
                            (IFNULL((SELECT stationName FROM eve_conquerablestationlist WHERE stationID = a.stationID),
                                (SELECT solarSystemName FROM corporation_industryjobs WHERE id = a.id)
                            ))
                    ))
                else (SELECT m.itemName FROM mapDenormalize AS m
                      WHERE m.itemID=a.stationID)
                end
                AS location, a.stationID as locID"))
            ->where('a.corporationID', $corporationID)
            ->where('endDate', '<=', date('Y-m-d H:i:s'))
            ->orderBy('endDate', 'desc')
            ->get();

        // Get the name of the corporation in question
        $corporation_name = DB::table('account_apikeyinfo_characters')
            ->where('corporationID', $corporationID)
            ->first();

        // Return the view
        return View::make('corporation.industry.industry')
            ->with('corporation', $corporation_name)
            ->with('current_jobs', $current_jobs)
            ->with('finished_jobs', $finished_jobs);
    }
}
