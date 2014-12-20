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

class CharacterController extends BaseController
{

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

        // Query the databse for all the characters and some related
        // information
        $characters = DB::table('account_apikeyinfo_characters')
            ->leftJoin('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
            ->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
            ->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
            ->orderBy('seat_keys.isOk', 'asc')
            ->orderBy('account_apikeyinfo_characters.characterName', 'asc')
            ->groupBy('account_apikeyinfo_characters.characterID');

        // Check that we only return characters that the current
        // user has access to. SuperUser() automatically
        // inherits all permissions
        if (!\Auth::hasAccess('recruiter'))
            $characters = $characters->whereIn('seat_keys.keyID', Session::get('valid_keys'))
                ->get();
        else
            $characters = $characters->get();

        // Set an array with the character info that we have
        $character_info = null;
        foreach (DB::table('eve_characterinfo')->get() as $character)
            $character_info[$character->characterID] = $character;

        $last_skills_end = null;
        foreach (DB::table('character_skillqueue')->select('characterID', DB::raw('max(endTime) as endTime'))->groupBy('characterID')->get() as $endTime)
            $last_skills_end[$endTime->characterID] = $endTime;

        return View::make('character.all')
            ->with('characters', $characters)
            ->with('character_info', $character_info)
            ->with('last_skills_end', $last_skills_end);
    }

    /*
    |--------------------------------------------------------------------------
    | getView()
    |--------------------------------------------------------------------------
    |
    | Get some preliminary information about a character, as well as
    | associations to other characters in the same people group
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

        // Check if whave knowledge of this character, else, simply redirect to the
        // public character function
        if(count($character) <= 0)
            return Redirect::action('CharacterController@getPublic', array('characterID' => $characterID))
                ->withErrors('No API key information is available for this character. This is the public view of the character. Submit a API key with this character on for more information.');

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                return Redirect::action('CharacterController@getPublic', array('characterID' => $characterID))
                    ->withErrors('You do not have access to view this character. This is the public view of the character.');

        // Determine the other characters that are on this API key
        $other_characters = DB::table('account_apikeyinfo_characters')
            ->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
            ->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
            ->where('account_apikeyinfo_characters.keyID', $character->keyID)
            ->where('account_apikeyinfo_characters.characterID', '<>', $character->characterID)
            ->get();

        // Get the other characters linked to this key as a person if any
        $key = $character->keyID;   // Small var declaration as I doubt you can use $character->keyID in the closure
        $people = DB::table('seat_people')
            ->leftJoin('account_apikeyinfo_characters', 'seat_people.keyID', '=', 'account_apikeyinfo_characters.keyID')
            ->whereIn('personID', function($query) use ($key) {

                $query->select('personID')
                    ->from('seat_people')
                    ->where('keyID', $key);
            })
            ->groupBy('characterID')
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view')
            ->with('character', $character)
            ->with('other_characters', $other_characters)
            ->with('people', $people);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxCharacterSheet()
    |--------------------------------------------------------------------------
    |
    | Return the character sheet as a ajax response
    |
    */

    public function getAjaxCharacterSheet($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        $character = DB::table('account_apikeyinfo_characters')
            ->leftJoin('account_apikeyinfo', 'account_apikeyinfo_characters.keyID', '=', 'account_apikeyinfo.keyID')
            ->leftJoin('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
            ->join('account_accountstatus', 'account_apikeyinfo_characters.keyID', '=', 'account_accountstatus.keyID')
            ->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
            ->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
            ->leftJoin('invTypes', 'character_skillintraining.trainingTypeID', '=', 'invTypes.typeID')
            ->where('character_charactersheet.characterID', $characterID)
            ->first();

        $character_info = DB::table('eve_characterinfo')
            ->where('characterID', $characterID)
            ->first();

        $employment_history = DB::table('eve_characterinfo_employmenthistory')
            ->where('characterID', $characterID)
            -> orderBy('startDate','desc')
            ->get();

        $skillpoints = DB::table('character_charactersheet_skills')
            ->where('characterID', $characterID)
            ->sum('skillpoints');

        $skill_queue = DB::table('character_skillqueue')
            ->join('invTypes', 'character_skillqueue.typeID', '=', 'invTypes.typeID')
            ->where('characterID', $characterID)
            ->orderBy('queuePosition')
            ->get();

        $jump_clones = DB::table(DB::raw('character_charactersheet_jumpclones as a'))
            ->select(DB::raw("
                *, CASE
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
                    AS location,a.locationId AS locID"))
            ->join('invTypes', 'a.typeID', '=', 'invTypes.typeID')
            ->where('a.characterID', $characterID)
            ->get();

        $implants = DB::table('character_charactersheet_implants')
            ->where('characterID', $characterID)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.character_sheet')
            ->with('character', $character)
            ->with('character_info', $character_info)
            ->with('employment_history', $employment_history)
            ->with('skillpoints', $skillpoints)
            ->with('skill_queue', $skill_queue)
            ->with('jump_clones', $jump_clones)
            ->with('implants', $implants);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxSkills()
    |--------------------------------------------------------------------------
    |
    | Return the character skills as a ajax response
    |
    */

    public function getAjaxSkills($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

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
        foreach ($character_skills_information as $key => $value)
            $character_skills[$value->groupID][] =  array(
                'typeID' => $value->typeID,
                'groupName' => $value->groupName,
                'typeName' => $value->typeName,
                'description' => $value->description,
                'skillpoints' => $value->skillpoints,
                'level' => $value->level
            );

        // Finally, give all this to the view to handle
        return View::make('character.view.character_skills')
            ->with('skill_groups', $skill_groups)
            ->with('character_skills', $character_skills);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxWalletJournal()
    |--------------------------------------------------------------------------
    |
    | Return the character wallet journal as a ajax response
    |
    */

    public function getAjaxWalletJournal($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the wallet journal
        $wallet_journal = DB::table('character_walletjournal')
            ->join('eve_reftypes', 'character_walletjournal.refTypeID', '=', 'eve_reftypes.refTypeID')
            ->where('characterID', $characterID)
            ->orderBy('date', 'desc')
            ->take(25)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.wallet_journal')
            ->with('wallet_journal', $wallet_journal)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxWalletTransactions()
    |--------------------------------------------------------------------------
    |
    | Return the character wallet transactions as a ajax response
    |
    */

    public function getAjaxWalletTransactions($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the Wallet transactions
        $wallet_transactions = DB::table('character_wallettransactions')
            ->where('characterID', $characterID)
            ->orderBy('transactionDateTime', 'desc')
            ->take(25)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.wallet_transactions')
            ->with('wallet_transactions', $wallet_transactions)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxMail()
    |--------------------------------------------------------------------------
    |
    | Return the character mail as a ajax response
    |
    */

    public function getAjaxMail($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the Mail
        $mail = DB::table('character_mailmessages')
            ->where('characterID', $characterID)
            ->orderBy('sentDate', 'desc')
            ->take(25)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.mail')
            ->with('mail', $mail)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxNotifications()
    |--------------------------------------------------------------------------
    |
    | Return the character notifications as a ajax response
    |
    */

    public function getAjaxNotifications($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the Notifications
        $notifications = DB::table('character_notifications')
            ->join('eve_notification_types', 'character_notifications.typeID', '=', 'eve_notification_types.typeID')
            ->join('character_notification_texts', 'character_notifications.notificationID', '=', 'character_notification_texts.notificationID')
            ->where('character_notifications.characterID', $characterID)
            ->orderBy('sentDate', 'desc')
            ->take(25)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.notifications')
            ->with('notifications', $notifications)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxAssets()
    |--------------------------------------------------------------------------
    |
    | Return the character assets as a ajax response
    |
    */

    public function getAjaxAssets($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the assets
        $assets = DB::table(DB::raw('character_assetlist as a'))
            ->select(DB::raw("
                *, CASE
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
                    AS location,a.locationId AS locID"))
            ->join('invTypes', 'a.typeID', '=', 'invTypes.typeID')
            ->join('invGroups', 'invTypes.groupID', '=', 'invGroups.groupID')
            ->where('a.characterID', $characterID)
            ->get();

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
                'groupName' => $value->groupName,
                'volume' => $value->volume * $value->quantity,
            );
            $assets_count++;

            foreach( $assets_contents as $contents) {

                if ($value->itemID == $contents->itemID) { // check what parent content item has

                    // create a sub array 'contents' and put content item info in
                    $assets_list[$value->location][$contents->itemID]['contents'][] = array(
                        'quantity' => $contents->sumquantity,
                        'typeID' => $contents->typeID,
                        'typeName' => $contents->typeName,
                        'groupName' => $contents->groupName,
                        'volume' => $contents->volume * $contents->quantity,
                    );
                    $assets_count++;
                }
            }
        }

        // Finally, give all this to the view to handle
        return View::make('character.view.assets')
            ->with('assets', $assets)
            ->with('assets_list', $assets_list)
            ->with('assets_count', $assets_count)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxContacts()
    |--------------------------------------------------------------------------
    |
    | Return the character contacts as a ajax response
    |
    */

    public function getAjaxContacts($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the contact list
        $contact_list = DB::table('character_contactlist')
            ->where('characterID', $characterID)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.contacts')
            ->with('contact_list', $contact_list)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxContracts()
    |--------------------------------------------------------------------------
    |
    | Return the character contracts as a ajax response
    |
    */

    public function getAjaxContracts($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Character contract list
        $contract_list = DB::table(DB::raw('character_contracts as a'))
            ->select(DB::raw(
                "*, CASE
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
                AS endlocation "))
            ->where('a.characterID', $characterID)
            ->get();

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

            if($value->type == 'Courier')
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

            else
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

            // Loop the Item in contracts and add it to his parent
            foreach( $contract_list_item as $contents)

                // Check the contents of the parent item
                if ($value->contractID == $contents->contractID)

                    // create a sub array 'contents' and put content item info in
                    $contracts_other[$value->contractID]['contents'][] = array(
                        'quantity' => $contents->quantity,
                        'typeID' => $contents->typeID,
                        'typeName' => $contents->typeName,
                        'included' => $contents->included // for "buyer will pay" item
                    );
        }

        // Finally, give all this to the view to handle
        return View::make('character.view.contracts')
            ->with('contracts_courier', $contracts_courier)
            ->with('contracts_other', $contracts_other)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxMarketOrders()
    |--------------------------------------------------------------------------
    |
    | Return the character market orders as a ajax response
    |
    */

    public function getAjaxMarketOrders($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the market orders
        $market_orders = DB::table(DB::raw('character_marketorders as a'))
            ->select(DB::raw(
                "*, CASE
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
                    AS location,a.stationID AS locID"))
            ->join('invTypes', 'a.typeID', '=', 'invTypes.typeID')
            ->join('invGroups', 'invTypes.groupID', '=', 'invGroups.groupID')
            ->where('a.characterID', $characterID)
            ->orderBy('a.issued', 'DESC')
            ->take(500)
            ->get();

        // Order states from: https://neweden-dev.com/Character/Market_Orders
        // Valid states: 0 = open/active, 1 = closed, 2 = expired (or fulfilled), 3 = cancelled, 4 = pending, 5 = character deleted.
        $order_states = array(
            '0' => 'Active',
            '1' => 'Closed',
            '2' => 'Expired / Fulfilled',
            '3' => 'Cancelled',
            '4' => 'Pending',
            '5' => 'Deleted'
        );

        // Finally, give all this to the view to handle
        return View::make('character.view.market_orders')
            ->with('market_orders', $market_orders)
            ->with('order_states', $order_states)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxCalendarEvents()
    |--------------------------------------------------------------------------
    |
    | Return the character calendar events as a ajax response
    |
    */

    public function getAjaxCalendarEvents($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Character calendar events
        $calendar_events = DB::table('character_upcomingcalendarevents')
            ->where('characterID', $characterID)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.calendar_events')
            ->with('calendar_events', $calendar_events)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxStandings()
    |--------------------------------------------------------------------------
    |
    | Return the character standings events as a ajax response
    |
    */

    public function getAjaxStandings($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. . SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Standings
        $agent_standings = DB::table('character_standings_agents')
            ->where('characterID', $characterID)
            ->get();

        $faction_standings = DB::table('character_standings_factions')
            ->where('characterID', $characterID)
            ->get();

        $npc_standings = DB::table('character_standings_npccorporations')
            ->where('characterID', $characterID)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.character_standings')
            ->with('agent_standings', $agent_standings)
            ->with('faction_standings', $faction_standings)
            ->with('npc_standings', $npc_standings)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxKillMails()
    |--------------------------------------------------------------------------
    |
    | Return the killmail information for the character
    |
    */

    public function getAjaxKillMails($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Killmails
        $killmails = DB::table('character_killmails')
            ->select(DB::raw('*, `mapDenormalize`.`itemName` AS solarSystemName'))
            ->leftJoin('character_killmail_detail', 'character_killmails.killID', '=', 'character_killmail_detail.killID')
            ->leftJoin('invTypes', 'character_killmail_detail.shipTypeID', '=', 'invTypes.typeID')
            ->leftJoin('mapDenormalize', 'character_killmail_detail.solarSystemID', '=', 'mapDenormalize.itemID')
            ->where('character_killmails.characterID', $characterID)
            ->orderBy('character_killmail_detail.killTime', 'desc')
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.killmails')
            ->with('characterID', $characterID)
            ->with('killmails', $killmails);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxResearchAgents()
    |--------------------------------------------------------------------------
    |
    | Return the character wallet journal as a ajax response
    |
    */

    public function getAjaxResearchAgents($characterID)
    {
        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get the research agents
        $research = DB::table('character_research')
            ->join('invNames', 'character_research.agentID', '=', 'invNames.itemID')
            ->join('invTypes', 'character_research.skillTypeID', '=', 'invTypes.typeID')
            ->where('characterID', $characterID)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.view.character_research')
            ->with('research', $research)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxPlantearyInteraction()
    |--------------------------------------------------------------------------
    |
    | Return the character standings events as a ajax response
    |
    */

    public function getAjaxPlanetaryInteraction($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Gather the information and add to the array
        $routes = DB::table('character_planetary_pins as source')
            ->join('character_planetary_routes as route', 'source.pinID', '=', 'route.sourcePinID')
            ->join('character_planetary_pins as destination', 'destination.PinID', '=', 'route.destinationPinID')
            ->where('source.characterID', $characterID)
            ->select(
                'source.planetID', 'source.typeName as sourceTypeName', 'source.typeID as sourceTypeID',
                'source.cycleTime', 'source.quantityPerCycle', 'source.installTime', 'source.expiryTime',
                'route.contentTypeID', 'route.contentTypeName', 'route.quantity',
                'destination.typeName as destinationTypeName', 'destination.typeID as destinationTypeID'
            )
            ->get();

        $links = DB::table('character_planetary_pins as source')
            ->join('character_planetary_links as link', 'link.sourcePinID', '=', 'source.pinID')
            ->join('character_planetary_pins as destination', 'link.destinationPinID', '=', 'destination.pinID')
            ->where('source.characterID', $characterID)
            ->select(
                'source.planetID', 'source.typeName as sourceTypeName', 'source.typeID as sourceTypeID',
                'link.linkLevel', 'destination.typeName as destinationTypeName',
                'destination.typeID as destinationTypeID'
            )
            ->get();

        $installations = DB::table('character_planetary_pins')
            ->where('cycleTime', '=', '0')
            ->where('schematicID', '=', '0')
            ->get();

        $planets = DB::table('character_planetary_colonies')
            ->where('characterID', $characterID)
            ->get();

        // Prepare an empty array
        $colonies = array();

        // Populate the planet details
        foreach($planets as $planet)
            $colonies[$planet->planetID] = array(
                'planetID' => $planet->planetID,
                'planetName' => $planet->planetName,
                'planetTypeName' => $planet->planetTypeName,
                'upgradeLevel' => $planet->upgradeLevel,
                'numberOfPins' => $planet->numberOfPins
            );

        // Finally, give all this to the view to handle
        return View::make('character.view.character_pi')
            ->with('colonies', $colonies)
            ->with('routes', $routes)
            ->with('installations', $installations)
            ->with('links', $links)
            ->with('characterID', $characterID);
    }

    /*
    |--------------------------------------------------------------------------
    | getPublic()
    |--------------------------------------------------------------------------
    |
    | *Usually* we will get to this route of we don't have actual information
    | from a API key.
    |
    */

    public function getPublic($characterID)
    {

        // Firstly we will call the character info updator worker
        \Seat\EveApi\Eve\CharacterInfo::Update((int)$characterID);

        // Get the information from the database now
        $character_info = DB::table('eve_characterinfo')
            ->where('characterID', $characterID)
            ->first();

        $employment_history = DB::table('eve_characterinfo_employmenthistory')
            ->where('characterID', $characterID)
            ->get();

        // Finally, give all this to the view to handle
        return View::make('character.public')
            ->with('character_info', $character_info)
            ->with('employment_history', $employment_history);
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

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

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

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

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

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

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

        $skills = explode(',', Input::get('skills'));
        $level = Input::get('level');

        // Ensure we actually got an array...
        if (!is_array($skills))
            App::abort(404);

        $filter = DB::table('character_charactersheet_skills')
            ->join('account_apikeyinfo_characters', 'character_charactersheet_skills.characterID', '=', 'account_apikeyinfo_characters.characterID')
            ->join('invTypes', 'character_charactersheet_skills.typeID', '=', 'invTypes.typeID')
            ->whereIn('character_charactersheet_skills.typeID', array_values($skills))
            ->orderBy('invTypes.typeName')
            ->groupBy('character_charactersheet_skills.characterID', 'character_charactersheet_skills.typeID');

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            $filter = $filter->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

        // Check if we should get all of the levels or a specific one
        if ($level == 'A')
            $filter = $filter->get();
        else
            $filter = $filter->where('character_charactersheet_skills.level', $level)->get();

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

        // Search the assets
        $assets = DB::table(DB::raw('character_assetlist as a'))
            ->select(DB::raw(
                "*, CASE
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
                    AS location,a.locationId AS locID"))
            ->join('invTypes', 'a.typeID', '=', 'invTypes.typeID')
            ->join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'a.characterID');

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            $assets = $assets->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

        // Complete the search
        $assets = $assets->whereIn('invTypes.typeID', Input::get('items'))
            ->groupBy('a.characterID')
            ->orderBy('location')
            ->get();

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

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        $wallet_daily_delta = DB::table('character_walletjournal')
            ->select(DB::raw('DATE(`date`) as day, IFNULL( SUM( amount ), 0 ) AS daily_delta'))
            ->where('characterID', $characterID)
            ->groupBy('day')
            ->get();

        return Response::json($wallet_daily_delta);
    }

    /*
    |--------------------------------------------------------------------------
    | getAjaxIndustry()
    |--------------------------------------------------------------------------
    |
    | Returns the industry jobs (running and ended) as an ajax reponse
    |
    */

    public function getAjaxIndustry($characterID)
    {

        // Check the character existance
        $character = DB::table('account_apikeyinfo_characters')
            ->where('characterID', $characterID)
            ->first();

        // Check if whave knowledge of this character, else, 404
        if(count($character) <= 0)
            App::abort(404);

        // Next, check if the current user has access. Superusers may see all the things,
        // normal users may only see their own stuffs. . SuperUser() inherits 'recruiter'
        if (!\Auth::hasAccess('recruiter'))
            if (!in_array(EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('keyID'), Session::get('valid_keys')))
                App::abort(404);

        // Get current working jobs
        $current_jobs = DB::table('character_industryjobs as a')
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
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                      WHERE c.stationID=a.stationID)
                else (SELECT m.itemName FROM mapDenormalize AS m
                WHERE m.itemID=a.stationID) end
                AS location,a.stationID AS locID"))
            ->where('a.characterID', $characterID)
            ->where('endDate', '>', date('Y-m-d H:i:s'))
            ->orderBy('endDate', 'asc')
            ->get();

        // Get the passed jobs
        $finished_jobs = DB::table('character_industryjobs as a')
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
                    (SELECT c.stationName FROM `eve_conquerablestationlist` AS c
                      WHERE c.stationID=a.stationID)
                else (SELECT m.itemName FROM mapDenormalize AS m
                WHERE m.itemID=a.stationID) end
                AS location,a.stationID AS locID"))
            ->where('a.characterID', $characterID)
            ->where('endDate', '<=', date('Y-m-d H:i:s'))
            ->orderBy('endDate', 'desc')
            ->get();

        // Return the view
        return View::make('character.view.industry')
            ->with('characterID', $characterID)
            ->with('current_jobs', $current_jobs)
            ->with('finished_jobs', $finished_jobs);
    }
}
