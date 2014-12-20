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

use App\Services\Validators\APIKeyValidator;
use App\Services\Settings\SettingHelper as Settings;
use Pheal\Pheal;

use Seat\EveApi;
use Seat\EveApi\BaseApi;
use Seat\EveApi\Account;


class ApiKeyController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | __construct()
    |--------------------------------------------------------------------------
    |
    | Sets up the class to ensure that CSRF tokens are validated on the POST
    | verb
    |
    */

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /*
    |--------------------------------------------------------------------------
    | getAll()
    |--------------------------------------------------------------------------
    |
    | Get all of the API keys in the database together with the characters that
    | are currently associated with it
    |
    */

    public function getAll()
    {

        // Get all of the keys in SeAT
        $keys = DB::table('seat_keys')
            ->select(
                'seat_keys.keyID', 'seat_keys.isOk', 'seat_keys.lastError', 'account_apikeyinfo.accessMask',
                'account_apikeyinfo.type', 'account_apikeyinfo.expires', DB::raw('count(`banned_calls`.`ownerID`) bans')
            )
            ->leftJoin('account_apikeyinfo', 'seat_keys.keyID', '=', 'account_apikeyinfo.keyID')
            ->leftJoin('banned_calls', 'seat_keys.keyID', '=', 'banned_calls.ownerID');

        // If the current user is not a admin, only get the keys that they own
        if (!\Auth::isSuperUser())
            $keys = $keys->where('seat_keys.user_id', \Auth::User()->id);

        // And complete the query
        $keys = $keys->groupBy('seat_keys.keyID')
            ->get();

        // Prepare the key information and characters for the view
        $key_information = array();
        foreach ($keys as $key) {

            // We will set the key information for they key...
            $key_information[$key->keyID] = array(

                'keyID' => $key->keyID,
                'isOk' => $key->isOk,
                'lastError' => $key->lastError,
                'ban_count' => $key->bans,
                'accessMask' => $key->accessMask,
                'type' => $key->type,
                'expires' => $key->expires,
                'expires_human' => (is_null($key->expires) ? 'Never' : \Carbon\Carbon::parse($key->expires)->diffForHumans()),
            );

            // ... and loop and add the key characters to the
            // ['characters'] key of $key_information
            foreach (EveAccountAPIKeyInfoCharacters::where('keyID', $key->keyID)->get() as $character)
                $key_information[$key->keyID]['characters'][] = array(
                    'characterID' => $character->characterID,
                    'characterName' => $character->characterName
                );
        }

        // Return this to the view
        return View::make('keys.all')
            ->with(array('key_info' => $key_information));
    }

    /*
    |--------------------------------------------------------------------------
    | getNewKey()
    |--------------------------------------------------------------------------
    |
    | Return a view to add new keys
    |
    */

    public function getNewKey()
    {
        return View::make('keys.new');
    }

    /*
    |--------------------------------------------------------------------------
    | postNewKey()
    |--------------------------------------------------------------------------
    |
    | Check a keyID and vCode for validity and return information about its
    | existance in the database & information retreived from the EVE API
    |
    */

    public function postNewKey()
    {
        if (Request::ajax()) {

            // We will validate the key pais as we dont want to cause unneeded errors
            $validation = new APIKeyValidator;

            if ($validation->passes()) {

                // Setup a pheal instance and get some API data :D
                BaseApi::bootstrap();
                $pheal = new Pheal(Input::get('keyID'), Input::get('vCode'));

                // Get API Key Information
                try {

                    $key_info = $pheal->accountScope->APIKeyInfo();

                } catch (\Pheal\Exceptions\PhealException $e) {

                    return View::make('keys.ajax.errors')
                        ->withErrors(array('error' => $e->getCode() . ': ' . $e->getMessage()));
                }

                // Here, based on the type of key, we will either call some further information,
                // or just display what we have learned so far.
                if ($key_info->key->type == 'Corporation') {

                    // Just return the view for corporation keys
                    return View::make('keys.ajax.corporation')
                        ->with('keyID', Input::get('keyID'))
                        ->with('vCode', Input::get('vCode'))
                        ->with('key_info', $key_info)
                        ->with('existance', SeatKey::where('keyID', Input::get('keyID'))->count());
                }
                elseif($key_info->key->accessMask < Settings::getSetting('required_mask')) {

                    return View::make('keys.ajax.errors')
                        ->withErrors(array('error' => 'Invalid API Mask!'));
                }

                // Get API Account Status Information
                try {

                    $status_info = $pheal->accountScope->AccountStatus();

                } catch (\Pheal\Exceptions\PhealException $e) {

                    return View::make('keys.ajax.errors')
                        ->withErrors(array('error' => $e->getCode() . ': ' . $e->getMessage()));
                }

                // Return the view
                return View::make('keys.ajax.character')
                    ->with('keyID', Input::get('keyID'))
                    ->with('vCode', Input::get('vCode'))
                    ->with('key_info', $key_info)
                    ->with('status_info', $status_info)
                    ->with('existance', SeatKey::where('keyID', Input::get('keyID'))->count());

            } else {

                return View::make('keys.ajax.errors')
                    ->withErrors($validation->errors);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getAdd()
    |--------------------------------------------------------------------------
    |
    | Check a keyID & vCode for validity and add it to the database. If the
    | keyID already exists, it will be set to enabled and updated to the
    | received vCode.
    |
    */

    public function getAdd($keyID, $vCode)
    {

        $validation = new APIKeyValidator(array('keyID' => $keyID, 'vCode' => $vCode));

        if ($validation->passes()) {

            // Check if we have this key in the db, even those that are soft deleted
            $key_data = SeatKey::withTrashed()
                ->where('keyID', $keyID)
                ->first();

            if (!$key_data)
                $key_data = new SeatKey;

            $key_data->keyID = $keyID;
            $key_data->vCode = $vCode;
            $key_data->isOk = 1;
            $key_data->lastError = null;
            $key_data->deleted_at = null;
            $key_data->user_id = \Auth::User()->id;
            $key_data->save();

            // Queue a job to update this API **now**
            $access = EveApi\BaseApi::determineAccess($keyID);
            if (!isset($access['type'])) {

                return Redirect::action('ApiKeyController@getAll')
                    ->with('warning', 'Key was successfully added, but a update job was not submitted.');
            }

            // Based in the key type, push a update job
            switch ($access['type']) {
                case 'Character':

                    // Do a fresh AccountStatus lookup
                    Account\AccountStatus::update($keyID, $vCode);
                    $jobID = \App\Services\Queue\QueueHelper::addToQueue('\Seat\EveQueues\Full\Character', $key_data->keyID, $key_data->vCode, 'Character', 'Eve');
                    break;

                case 'Corporation':
                    $jobID = \App\Services\Queue\QueueHelper::addToQueue('\Seat\EveQueues\Full\Corporation', $key_data->keyID, $key_data->vCode, 'Corporation', 'Eve');
                    break;

                default:
                    $jobID = 'Unknown';
                    break;
            }

            return Redirect::action('ApiKeyController@getAll')
                ->with('success', 'Key was successfully added and job ' . $jobID . ' was queued to update it.');

        } else {

            return Redirect::action('ApiKeyController@getNewKey')
                    ->withErrors($validation->errors);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getDetail()
    |--------------------------------------------------------------------------
    |
    | Find all of the keyID related details stored in the database, along with
    | queue information for it.
    |
    */

    public function getDetail($keyID)
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array($keyID, Session::get('valid_keys')))
                App::abort(404);

        $key_information = DB::table('seat_keys')
            ->select(
                'seat_keys.keyID', 'seat_keys.vCode', 'seat_keys.isOk', 'seat_keys.lastError',
                'account_apikeyinfo.accessMask', 'account_apikeyinfo.type', 'account_apikeyinfo.expires'
            )
            ->leftJoin('account_apikeyinfo', 'seat_keys.keyID', '=', 'account_apikeyinfo.keyID')
            ->where('seat_keys.keyID', $keyID)
            ->first();

        $key_characters = DB::table('account_apikeyinfo_characters')
            ->where('keyID', $keyID)
            ->get();

        $key_bans = DB::table('banned_calls')
            ->where('ownerID', $keyID)
            ->get();

        $recent_jobs = DB::table('queue_information')
            ->where('ownerID', $keyID)
            ->orderBy('created_at', 'desc')
            ->take(25)
            ->get();

        $key_owner = DB::table('seat_keys')
            ->join('seat_users', 'seat_keys.user_id', '=', 'seat_users.id')
            ->where('seat_keys.keyID', $keyID)
            ->get();

        return View::make('keys.detail')
            ->with('key_information', $key_information)
            ->with('key_characters', $key_characters)
            ->with('key_bans', $key_bans)
            ->with('recent_jobs', $recent_jobs)
            ->with('key_owner', $key_owner);
    }

    /*
    |--------------------------------------------------------------------------
    | getUpdateJob()
    |--------------------------------------------------------------------------
    |
    | Checks for the existance of a key and submits a job to update it. Only
    | character keys will be processed, and a new job will only be submitted
    | if an existing one is not already waiting
    |
    */

    public function getUpdateJob($keyID)
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array($keyID, Session::get('valid_keys')))
                App::abort(404);

        // Get the full key and vCode
        $key = SeatKey::where('keyID', $keyID)->first();

        if (!$key)
            App::abort(404);

        // Check that there is not already an outstanding job for this keyID
        $queue_check = DB::table('queue_information')
            ->whereIn('status', array('Queued', 'Working'))
            ->where('ownerID', $key->keyID)
            ->first();

        if ($queue_check)
            return Response::json(array('state' => 'existing', 'jobID' => $queue_check->jobID));

        // Else, queue a job for this
        $access = EveApi\BaseApi::determineAccess($key->keyID);

        if (!isset($access['type']))
            return Response::json(array('state' => 'error', 'jobID' => null));

        // Only process Character keys here
        if ($access['type'] == 'Character') {

            // Do a fresh AccountStatus lookup
            Account\AccountStatus::update($keyID, $key->vCode);
            $jobID = \App\Services\Queue\QueueHelper::addToQueue('\Seat\EveQueues\Full\Character', $key->keyID, $key->vCode, 'Character', 'Eve');

            return Response::json(array('state' => 'new', 'jobID' => $jobID));

        } elseif( $access['type'] == 'Corporation' ){

            $jobID = \App\Services\Queue\QueueHelper::addToQueue('\Seat\EveQueues\Full\Corporation', $key->keyID, $key->vCode, 'Corporation', 'Eve');

            return Response::json(array('state' => 'new', 'jobID' => $jobID));

        } else {

            return Response::json(array('keyID' => $key->keyID, 'vCode' => $key->vCode, 'state' => 'error', 'jobID' => null));
        }

    }

    /*
    |--------------------------------------------------------------------------
    | getEnableKey()
    |--------------------------------------------------------------------------
    |
    | Re-enables a disabled key
    |
    */

    public function getEnableKey($keyID)
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array($keyID, Session::get('valid_keys')))
                App::abort(404);

        // Get the full key and vCode
        $key = SeatKey::where('keyID', $keyID)->first();

        if (!$key)
            App::abort(404);

        $key->isOk = 1;
        $key->lastError = null;
        $key->save();

        return Redirect::action('ApiKeyController@getDetail', array('keyID' => $keyID))
            ->with('success', 'Key has been re-enabled');

    }

    /*
    |--------------------------------------------------------------------------
    | getDeleteKey()
    |--------------------------------------------------------------------------
    |
    | Deletes a key form the database
    |
    */

    public function getDeleteKey($keyID, $delete_all_info = false)
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array($keyID, Session::get('valid_keys')))
                App::abort(404);

        // Ensure the user is allowed to delete this key
        if (!\Auth::hasAccess('key_manager'))
            App::abort(404);

        // Get the full key and vCode
        $key = SeatKey::where('keyID', $keyID)->first();

        if (!$key)
            App::abort(404);

        // Based on delete_all_info, we will either just delete the key,
        // or all of the information associated with it
        switch ((bool)$delete_all_info) {

            case true:

                // Check if we can determine if this is a corporation or account/char key.
                $type = \EveAccountAPIKeyInfo::where('keyID', $keyID)->pluck('type');

                // Check if the type is set
                if ($type) {

                    // For corporation keys, we will delete corporation stuff, duhr
                    if ($type == "Corporation") {

                        // Most of the data for corporations is stored with the corporationID
                        // as key. To get this ID, we need to find the character attached to
                        // this key, and then the corporation for that character
                        $characters = BaseApi::findKeyCharacters($keyID);
                        $corporationID = BaseApi::findCharacterCorporation($characters[0]);

                        // With the corporationID now known, go ahead and cleanup the database
                        \EveCorporationAccountBalance::where('corporationID', $corporationID)->delete();
                        \EveCorporationAssetList::where('corporationID', $corporationID)->delete();
                        \EveCorporationAssetListContents::where('corporationID', $corporationID)->delete();
                        \EveCorporationAssetListLocations::where('corporationID', $corporationID)->delete();
                        \EveCorporationContactListAlliance::where('corporationID', $corporationID)->delete();
                        \EveCorporationContactListCorporate::where('corporationID', $corporationID)->delete();
                        \EveCorporationContracts::where('corporationID', $corporationID)->delete();
                        \EveCorporationContractsItems::where('corporationID', $corporationID)->delete();
                        \EveCorporationCorporationSheet::where('corporationID', $corporationID)->delete();
                        \EveCorporationCorporationSheetDivisions::where('corporationID', $corporationID)->delete();
                        \EveCorporationCorporationSheetWalletDivisions::where('corporationID', $corporationID)->delete();
                        \EveCorporationIndustryJobs::where('corporationID', $corporationID)->delete();
                        \EveCorporationMarketOrders::where('corporationID', $corporationID)->delete();
                        \EveCorporationMedals::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberMedals::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityGrantableRoles::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityGrantableRolesAtBase::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityGrantableRolesAtHQ::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityGrantableRolesAtOther::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityLog::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityRoles::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityRolesAtBase::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityRolesAtHQ::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityRolesAtOther::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberSecurityTitles::where('corporationID', $corporationID)->delete();
                        \EveCorporationMemberTracking::where('corporationID', $corporationID)->delete();
                        \EveCorporationShareholderCharacters::where('corporationID', $corporationID)->delete();
                        \EveCorporationShareholderCorporations::where('corporationID', $corporationID)->delete();
                        \EveCorporationStandingsAgents::where('corporationID', $corporationID)->delete();
                        \EveCorporationStandingsFactions::where('corporationID', $corporationID)->delete();
                        \EveCorporationStandingsNPCCorporations::where('corporationID', $corporationID)->delete();
                        \EveCorporationStarbaseDetail::where('corporationID', $corporationID)->delete();
                        \EveCorporationStarbaseList::where('corporationID', $corporationID)->delete();
                        \EveCorporationWalletJournal::where('corporationID', $corporationID)->delete();
                        \EveCorporationWalletTransactions::where('corporationID', $corporationID)->delete();

                    } else {

                        // And for character stuff, we delete character stuff

                        // Here we need to be careful now. It may happen that we have more than 1 key
                        // for a character, so we have to be aware of this. It adds a factor of
                        // complexity to the whole thing.
                        $characters = BaseApi::findKeyCharacters($keyID);

                        // Now that we know about all of the characters, we will loop over them and check
                        // that we only have 1 key for them. If more than one keys have this character, we will
                        // simply ignore the cleanup and add a message about it
                        foreach ($characters as $id => $character) {

                            // Check how many keys know about this character
                            if (\EveAccountAPIKeyInfoCharacters::where('characterID', $character)->count() > 1) {

                                    // Write a log entry about this
                                    \Log::warning('Character ' . $character . ' is recorded on another key and will not been cleaned up');

                                    // Remove this character from $characters
                                    unset($characters[$id]);
                            }
                        }

                        // So we now have an array of characterID's that can be cleaned up. Lets do that
                        if (count($characters) > 0) {

                            \EveCharacterAccountBalance::whereIn('characterID', $characters)->delete();
                            \EveCharacterAssetList::whereIn('characterID', $characters)->delete();
                            \EveCharacterAssetListContents::whereIn('characterID', $characters)->delete();
                            \EveCharacterCharacterSheet::whereIn('characterID', $characters)->delete();
                            \EveCharacterCharacterSheetSkills::whereIn('characterID', $characters)->delete();
                            \EveCharacterContactList::whereIn('characterID', $characters)->delete();
                            \EveCharacterContactListAlliance::whereIn('characterID', $characters)->delete();
                            \EveCharacterContactListCorporate::whereIn('characterID', $characters)->delete();
                            \EveCharacterContactNotifications::whereIn('characterID', $characters)->delete();
                            \EveCharacterContracts::whereIn('characterID', $characters)->delete();
                            \EveCharacterContractsItems::whereIn('characterID', $characters)->delete();
                            \EveCharacterIndustryJobs::whereIn('characterID', $characters)->delete();
                            // Intentionally ignoring the mail related information as this has a lot of overlap
                            // and is almost always usefull
                            \EveCharacterMarketOrders::whereIn('characterID', $characters)->delete();
                            \EveCharacterPlanetaryColonies::whereIn('characterID', $characters)->delete();
                            \EveCharacterPlanetaryLinks::whereIn('characterID', $characters)->delete();
                            \EveCharacterPlanetaryPins::whereIn('characterID', $characters)->delete();
                            \EveCharacterPlanetaryRoutes::whereIn('characterID', $characters)->delete();
                            \EveCharacterResearch::whereIn('characterID', $characters)->delete();
                            \EveCharacterSkillInTraining::whereIn('characterID', $characters)->delete();
                            \EveCharacterSkillQueue::whereIn('characterID', $characters)->delete();
                            \EveCharacterStandingsAgents::whereIn('characterID', $characters)->delete();
                            \EveCharacterStandingsFactions::whereIn('characterID', $characters)->delete();
                            \EveCharacterStandingsNPCCorporations::whereIn('characterID', $characters)->delete();
                            \EveCharacterUpcomingCalendarEvents::whereIn('characterID', $characters)->delete();
                            \EveCharacterWalletJournal::whereIn('characterID', $characters)->delete();
                            \EveCharacterWalletTransactions::whereIn('characterID', $characters)->delete();
                        }
                    }

                    // Finally, delete the key and redirect
                    $key->delete();

                    // Delete the information that we have for this key too
                    \EveAccountAPIKeyInfo::where('keyID', $keyID)->delete();
                    \EveAccountAPIKeyInfoCharacters::where('keyID', $keyID)->delete();

                    return Redirect::action('ApiKeyController@getAll')
                        ->with('success', 'Key has been deleted');

                } else {

                    // So, we are unable to determine the key type, so maybe this is
                    // a invalid one or whatever. Just get rid of it.

                    // Delete the API Key
                    $key->delete();

                    // Delete the information that we have for this key too
                    \EveAccountAPIKeyInfo::where('keyID', $keyID)->delete();
                    \EveAccountAPIKeyInfoCharacters::where('keyID', $keyID)->delete();

                    return Redirect::action('ApiKeyController@getAll')
                        ->with('success', 'Key has been deleted');
                }

                break;

            case false:

                // Delete the API Key
                $key->delete();

                // Delete the information that we have for this key too
                \EveAccountAPIKeyInfo::where('keyID', $keyID)->delete();
                \EveAccountAPIKeyInfoCharacters::where('keyID', $keyID)->delete();

                return Redirect::action('ApiKeyController@getAll')
                    ->with('success', 'Key has been deleted');

                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getRemoveBan()
    |--------------------------------------------------------------------------
    |
    | Removes a ban that has been imposed on a key for a specific API call
    |
    */

    public function getRemoveBan($id)
    {

        \EveBannedCall::where('id', $id)
            ->delete();

        return Response::json();
    }

    /*
    |--------------------------------------------------------------------------
    | getRemoveAllBans()
    |--------------------------------------------------------------------------
    |
    | Removes all bans and enables all keys
    |
    */

    public function getRemoveAllBans()
    {

        // Ensure that this is SuperUser
        if (!\Auth::isSuperUser())
            App::abort(404);

        // Trash all of the banned calls information
        EveBannedCall::truncate();

        // Enable all of the keys
        SeatKey::where('isOk', 0)
            ->update(array('isOk' => 1, 'lastError' => null));


        // Redirect with a message
        return Redirect::action('ApiKeyController@getAll')
            ->with('success', 'All API keys have been enabled and their bans removed.');
    }

    /*
    |--------------------------------------------------------------------------
    | getPeople()
    |--------------------------------------------------------------------------
    |
    | Get all of the API keys in the database together with the characters that
    | are currently associated with it
    |
    */

    public function getPeople()
    {

        // Prepare the people information. Super admins get to see
        // everything, where users should only see their own stuff
        if (\Auth::isSuperUser()) {

            // SUUPER ADMIN Show all the things
            $people = array();
            foreach (\SeatPeople::Join('seat_people_main', 'seat_people_main.personID', '=', 'seat_people.personID')->orderBy('seat_people_main.characterName', 'ASC')->get() as $person)

                // For every person, get the keyID and characters on that key
                $people[$person->personID][] = array(

                    'personID' => $person->personID,
                    'keyID' => $person->keyID,
                    'characters' => EveAccountAPIKeyInfoCharacters::where('account_apikeyinfo_characters.keyID', $person->keyID)->get(),
                    'main' => $person->main()->first()
                );

            $unaffiliated_keys = DB::table('seat_keys')
                ->whereNotIn('seat_keys.keyID', function($query) {

                    $query->from('seat_people')
                        ->select('keyID')
                        ->get();
                })
                ->get();

        } else {

            // Ensure that we only get the data for this user
            $people = array();
            foreach (SeatPeople::whereIn('keyID', Session::get('valid_keys'))->get() as $person)

                // For every person, get the keyID and characters on that key
                $people[$person->personID][] = array(

                    'personID' => $person->personID,
                    'keyID' => $person->keyID,
                    'characters' => EveAccountAPIKeyInfoCharacters::where('keyID', $person->keyID)->get(),
                    'main' => $person->main()->first()
                );

            $unaffiliated_keys = DB::table('seat_keys')
                ->whereIn('keyID', Session::get('valid_keys'))
                ->whereNotIn('seat_keys.keyID', function($query) {

                    $query->from('seat_people')
                        ->select('keyID')
                        ->get();
                })
                ->get();
        }

        // Prepare an array with all the key characters too
        $unaffiliated = array();
        foreach ($unaffiliated_keys as $key)
            $unaffiliated[$key->keyID] = EveAccountAPIKeyInfoCharacters::where('keyID', $key->keyID)->get();

        return View::make('keys.people')
            ->with(array('people' => $people))
            ->with(array('unaffiliated' => $unaffiliated));
    }

    /*
    |--------------------------------------------------------------------------
    | getNewGroup()
    |--------------------------------------------------------------------------
    |
    | Create a New People Group from a characterID
    |
    */

    public function getNewGroup($keyID, $characterID)
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array($keyID, Session::get('valid_keys')))
                App::abort(404);

        // Check that the key and character ids exist and is valid
        if (!\SeatKey::where('keyID', $keyID) || !\EveAccountAPIKeyInfoCharacters::where('characterID', $characterID))
            App::abort(404);

        // Next, check that the key this characterID beongs to is
        // not already assigned to another group, and that the characterID is actually
        // valid
        $character_key = DB::table('account_apikeyinfo_characters')
            ->where('keyID', $keyID)
            ->first();

        if (!$character_key)
            return Redirect::action('ApiKeyController@getPeople')
                ->withErrors('This key is already assigned to a people group');

        // Ok, so the key is not already affiliated with another person, so lets create
        // a new person, and assign this key and main
        $person = new SeatPeople;
        $person->personID = DB::table('seat_people')->max('id') + 1;
        $person->keyID = $keyID;
        $person->save();

        // Get the main information
        $main = new SeatPeopleMain;
        $main->characterID = $characterID;
        $main->characterName = EveAccountAPIKeyInfoCharacters::where('characterID', $characterID)->pluck('characterName');
        $person->main()->save($main);

        return Redirect::action('ApiKeyController@getPeople')
            ->with('success', 'A new group was created with ' . $main->characterName . ' as the main character.');
    }

    /*
    |--------------------------------------------------------------------------
    | postAddToGroup()
    |--------------------------------------------------------------------------
    |
    | Add a keyID to a person group
    |
    */

    public function postAddToGroup()
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array(Input::get('affected-key'), Session::get('valid_keys')))
                App::abort(404);

        // Lets do some quick checks to ensure that the key and person exist
        if (!SeatPeople::where('personID', Input::get('personid'))->first() || !SeatKey::where('keyID', Input::get('affected-key'))->first())
            return Redirect::action('ApiKeyController@getPeople')
                ->withErrors('Whatever you just tried simply wont work.');

        // Next, we check that this keyID is not already part of a person.
        if (SeatPeople::where('keyID', Input::get('affected-key'))->first())
            return Redirect::action('ApiKeyController@getPeople')
                ->withErrors('This key is already part of a person...');

        // Lastly, add the key to the person
        $person = new SeatPeople;
        $person->personID = Input::get('personid');
        $person->keyID = Input::get('affected-key');
        $person->save();

        $people = \SeatPeopleMain::where('personID', '=', Input::get('personid'))->first();

        return Redirect::action('ApiKeyController@getPeople')
            ->with('success', 'Key ' . Input::get('affected-key') . ' has been added to the group ' . $people->characterName);
    }

    /*
    |--------------------------------------------------------------------------
    | getDeleteFromGroup()
    |--------------------------------------------------------------------------
    |
    | Delete a keyID from a person group
    |
    */

    public function getDeleteFromGroup($keyID)
    {

        // Ensure that this user may access the data for $keyID
        if (!\Auth::isSuperUser())
            if (!in_array($keyID, Session::get('valid_keys')))
                App::abort(404);

        // Lets do some quick checks to ensure that the key exists
        if (!SeatPeople::where('keyID', $keyID)->first())
            return Redirect::action('ApiKeyController@getPeople')
                ->withErrors('Whatever you just tried simply wont work.');

        // Get the personID so that we can check if the group is now empty
        // and delete if needed
        $personid = SeatPeople::where('keyID', $keyID)->pluck('personID');

        // Perform the delete
        SeatPeople::where('keyID', $keyID)->delete();

        // Count the remainder of keys for this person. If its empty, delete everything.
        if (SeatPeople::where('personID', $personid)->count() <= 0) {

            SeatPeople::where('personID', $personid)->delete();
            SeatPeopleMain::where('personID', $personid)->delete();
        }

        return Redirect::action('ApiKeyController@getPeople')
            ->with('success', 'Key ' . $keyID . ' has been delete from the person group');
    }

    /*
    |--------------------------------------------------------------------------
    | getSetGroupMain()
    |--------------------------------------------------------------------------
    |
    | Set a new main for a group
    |
    */

    public function getSetGroupMain($personid, $characterid)
    {

        // Lets do some quick checks to ensure that the person exists
        if (!SeatPeople::where('personID', $personid)->first())
            return Redirect::action('ApiKeyController@getPeople')
                ->withErrors('Whatever you just tried simply wont work.');

        // Delete the old main
        SeatPeopleMain::where('personID', $personid)->delete();

        $person = SeatPeople::with('main')->where('personID', $personid)->first();

        // Get the main information
        $main = new SeatPeopleMain;
        $main->characterID = $characterid;
        $main->characterName = EveAccountAPIKeyInfoCharacters::where('characterID', $characterid)->pluck('characterName');
        $person->main()->save($main);
        $person->save();

        return Redirect::action('ApiKeyController@getPeople')
            ->with('success', 'Group has had its main updated to ' . $main->characterName);
    }


    /*
    |--------------------------------------------------------------------------
    | postTransferOwnership()
    |--------------------------------------------------------------------------
    |
    | Set a new owner for a API key
    |
    */

    public function postTransferOwnership()
    {

        // Ensure that this user is a super admin
        if (!\Auth::isSuperUser())
            App::abort(404);

        // Find the API Key and user...
        $key_information  = SeatKey::where('keyID', Input::get('keyID'))->first();
        $seat_user = User::where('id', Input::get('accountid'))->first();

        // ... and check that they exist
        if (!$key_information || !$seat_user)
            App::abort(404);

        $key_information->user_id = Input::get('accountid');
        $key_information->save();

        return Redirect::action('ApiKeyController@getDetail', array(Input::get('keyID')))
            ->with('success', 'API Key Ownership has been transferred');
    }
}
