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

class DashboardController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /*
    |--------------------------------------------------------------------------
    | getDashboard()
    |--------------------------------------------------------------------------
    |
    | Gets the SeAT Dashboard. For now, this actually just redirects to the
    | home page.
    |
    */

    public function getDashboard()
    {
        return View::make('home');
    }

    /*
    |--------------------------------------------------------------------------
    | getSearch()
    |--------------------------------------------------------------------------
    |
    | Perform a search through data available in the SeAT database
    |
    */

    public function getSearch()
    {
        if (Request::ajax()) {

            /*
            |--------------------------------------------------------------------------
            | Check that the user is either a superadmin, or has at least some keys
            |--------------------------------------------------------------------------
            */

            if(!Auth::isSuperUser() && count(Session::get('valid_keys')) <= 0)
                return View::make('layouts.components.flash')
                    ->withErrors('No API Keys are defined to show you any information. Please enter at least one.');

            /*
            |--------------------------------------------------------------------------
            | Search Characters
            |--------------------------------------------------------------------------
            */

            $characters = DB::table('account_apikeyinfo_characters')
                ->leftJoin('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
                ->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
                ->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
                ->orderBy('seat_keys.isOk', 'asc')
                ->orderBy('account_apikeyinfo_characters.characterName', 'asc')
                ->groupBy('account_apikeyinfo_characters.characterID')
                ->where('characterName', 'like', '%' . Input::get('q') . '%');

            // Ensure we only get result for characters we have access to
            if (!\Auth::hasAccess('recruiter'))
                $characters = $characters->whereIn('seat_keys.keyID', Session::get('valid_keys'))
                    ->get();
            else
                $characters = $characters->get();

            /*
            |--------------------------------------------------------------------------
            | Search Character Assets
            |--------------------------------------------------------------------------
            */

            $character_assets = DB::table(DB::raw('character_assetlist as a'))
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

            // If the user is not a superuser, filter the results down to keys they own
            if (!\Auth::isSuperUser() )
                $character_assets = $character_assets->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

            // Complete the search
            $character_assets = $character_assets->where('invTypes.typeName', 'like', '%' . Input::get('q') . '%')
                ->orderBy('location')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Search Character Contact Lists
            |--------------------------------------------------------------------------
            */

            // Search character contact lists
            $character_contactlist = DB::table('character_contactlist')
                ->join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_contactlist.characterID')
                ->where('character_contactlist.contactName', 'like', '%' . Input::get('q') . '%');

            // Ensure we only get result for characters we have access to
            if (!\Auth::hasAccess('recruiter'))
                $character_contactlist = $character_contactlist->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'))
                    ->get();
            else
                $character_contactlist = $character_contactlist->get();

            /*
            |--------------------------------------------------------------------------
            | Search Character Mail
            |--------------------------------------------------------------------------
            */

            $character_mail = DB::table('character_mailmessages')
                ->join('account_apikeyinfo_characters', 'character_mailmessages.characterID', '=', 'account_apikeyinfo_characters.characterID')
                ->join('character_mailbodies', 'character_mailmessages.messageID', '=', 'character_mailbodies.messageID')
                ->where('character_mailmessages.senderName', 'like', '%' . Input::get('q') . '%')
                ->orWhere('character_mailmessages.title', 'like', '%' . Input::get('q') . '%')
                ->orWhere('character_mailbodies.body', 'like', '%' . Input::get('q') . '%');

            // Ensure we only get result for characters we have access to
            if (!\Auth::hasAccess('recruiter'))
                $character_mail = $character_mail->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

            $character_mail = $character_mail
                ->groupBy('character_mailmessages.messageID')
                ->orderBy('character_mailmessages.sentDate', 'desc')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Search Character Standings
            |--------------------------------------------------------------------------
            */

            $character_standings = DB::table('character_standings_factions')
                ->join('account_apikeyinfo_characters', 'character_standings_factions.characterID', '=', 'account_apikeyinfo_characters.characterID')
                ->where('character_standings_factions.fromName', 'like', '%' . Input::get('q') . '%');

            // Ensure we only get result for characters we have access to
            if (!\Auth::hasAccess('recruiter'))
                $character_standings = $character_standings->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'));

            $character_standings = $character_standings
                ->orderBy('character_standings_factions.standing', 'desc')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Corporation Names
            |--------------------------------------------------------------------------
            */

            $corporation_names = array_flip(DB::table('account_apikeyinfo_characters')
                ->lists('corporationID', 'corporationName'));

            /*
            |--------------------------------------------------------------------------
            | Search Corporation Assets
            |--------------------------------------------------------------------------
            */

            $corporation_assets = DB::table(DB::raw('corporation_assetlist as a'))
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
                ->join('invTypes', 'a.typeID', '=', 'invTypes.typeID');

            // If the user is not a superuser, filter the results down to keys they own
            if (!\Auth::hasAccess('asset_manager'))
                $corporation_assets = $corporation_assets->whereIn('a.corporationID', Session::get('corporation_affiliations'));

            // Complete the search
            $corporation_assets = $corporation_assets->where('invTypes.typeName', 'like', '%' . Input::get('q') . '%')
                ->orderBy('location')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Search Corporation Standings
            |--------------------------------------------------------------------------
            */

            $corporation_standings = DB::table('corporation_standings_factions')
                ->where('corporation_standings_factions.fromName', 'like', '%' . Input::get('q') . '%');

            // Ensure we only get result for characters we have access to
            if (!\Auth::hasAccess('recruiter'))
                $corporation_standings = $corporation_standings->whereIn('corporation_standings_factions.corporationID', Session::get('corporation_affiliations'));

            $corporation_standings = $corporation_standings
                ->orderBy('corporation_standings_factions.standing', 'desc')
                ->get();

            // Return the AJAX response
            return View::make('search')
                ->with('keyword', Input::get('q'))
                ->with('characters', $characters)
                ->with('character_assets', $character_assets)
                ->with('character_contactlist', $character_contactlist)
                ->with('character_mail', $character_mail)
                ->with('character_standings', $character_standings)
                ->with('corporation_names', $corporation_names)
                ->with('corporation_assets', $corporation_assets)
                ->with('corporation_standings', $corporation_standings)
                ;

        } else {

            // Not a ajax request? Go away :>
            App::abort(404);
        }
    }
}
