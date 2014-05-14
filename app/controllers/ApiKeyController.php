<?php

use App\Services\Validators\APIKeyValidator;
use Pheal\Pheal;

use Seat\EveApi;
use Seat\EveApi\BaseApi;
use Seat\EveApi\Account;


class ApiKeyController extends BaseController {

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

		$keys = DB::table('seat_keys')
			->select('seat_keys.keyID', 'seat_keys.isOk', 'seat_keys.lastError', 'account_apikeyinfo.accessMask', 'account_apikeyinfo.type', 'account_apikeyinfo.expires', DB::raw('count(`banned_calls`.`ownerID`) bans'))
			->leftJoin('account_apikeyinfo', 'seat_keys.keyID', '=', 'account_apikeyinfo.keyID')
			->leftJoin('banned_calls', 'seat_keys.keyID', '=', 'banned_calls.ownerID')
			->groupBy('seat_keys.keyID')
			->get();

		// Prepare the key information and characters for the view
		$key_information = array();
		foreach ($keys as $key) {
			
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

			// Add the key characters
			foreach (EveAccountAPIKeyInfoCharacters::where('keyID', $key->keyID)->get() as $character) {
				
				$key_information[$key->keyID]['characters'][] = array(
					'characterID' => $character->characterID,
					'characterName' => $character->characterName
				);
			}
		}

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

				// Get API Account Status Information
				try {
					$status_info = $pheal->accountScope->AccountStatus();

				} catch (\Pheal\Exceptions\PhealException $e) {

					return View::make('keys.ajax.errors')
						->withErrors(array('error' => $e->getCode() . ': ' . $e->getMessage()));
				}

				// TODO: Think about adding a entry to the cache to mark a particular key as
				// valid

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
			$key_data = SeatKey::withTrashed()->where('keyID', $keyID)->first();

			if (!$key_data)
				$key_data = new SeatKey;

			$key_data->keyID = $keyID;
			$key_data->vCode = $vCode;
			$key_data->isOk = 1;
			$key_data->lastError = null;
			$key_data->deleted_at = null;
			$key_data->user_id = 1; // TODO: Fix this when the proper user management occurs
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
					$jobID = \App\Services\Queue\QueueHelper::addToQueue('\Full\Character', $keyID, $vCode, 'Character', 'Eve');
					break;

				case 'Corporation':
					$jobID = \App\Services\Queue\QueueHelper::addToQueue('\Full\Corporation', $keyID, $vCode, 'Corporation', 'Eve');
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

		$key_information = DB::table('seat_keys')
			->select('seat_keys.keyID', 'seat_keys.vCode', 'seat_keys.isOk', 'seat_keys.lastError', 'account_apikeyinfo.accessMask', 'account_apikeyinfo.type', 'account_apikeyinfo.expires')
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

		//TODO: Cached untill for the characerID's

		return View::make('keys.detail')
			->with('key_information', $key_information)
			->with('key_characters', $key_characters)
			->with('key_bans', $key_bans)
			->with('recent_jobs', $recent_jobs);
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

		// Get the full key and vCode
		$key = SeatKey::where('keyID', $keyID)->first();

		if (!$key)
			App::abort(404);

		// Check that there is not already an outstanding job for this keyID
		$queue_check = DB::table('queue_information')
			->where('status', 'Queued')
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
			$jobID = \App\Services\Queue\QueueHelper::addToQueue('\Full\Character', $key->keyID, $key->vCode, 'Character', 'Eve', true);

			return Response::json(array('state' => 'new', 'jobID' => $jobID));
		} else {

			return Response::json(array('state' => 'error', 'jobID' => null));
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

	public function getDeleteKey($keyID)
	{

		// Get the full key and vCode
		$key = SeatKey::where('keyID', $keyID)->first();

		if (!$key)
			App::abort(404);

		$key->delete();

		// Delete the information that we have for this key too
		\EveAccountAPIKeyInfo::where('keyID', $keyID)->delete();

		return Redirect::action('ApiKeyController@getAll')
			->with('success', 'Key has been deleted');

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
	| getPeople()
	|--------------------------------------------------------------------------
	|
	| Get all of the API keys in the database together with the characters that
	| are currently associated with it
	|
	*/

	public function getPeople()
	{

		// Prepare the people information
		$people = array();
		foreach (\SeatPeople::all() as $person) {

			// For every person, get the keyID and characters on that key
			$people[$person->personID][] = array(

				'personID' => $person->personID,
				'keyID' => $person->keyID,
				'characters' => EveAccountAPIKeyInfoCharacters::where('keyID', $person->keyID)->get(),
				'main' => $person->main()->first()
			);
		}

		// var_dump($people[1][0]['main']->characterName);die();

		$unaffiliated_keys = DB::table('seat_keys')
			// ->leftJoin('account_apikeyinfo_characters', 'seat_keys.keyID', '=', 'account_apikeyinfo_characters.keyID')
			->whereNotIn('seat_keys.keyID', function($query) {

				$query->from('seat_people')
					->select('keyID')
					->get();
			})
			->get();

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

	public function getNewGroup($characterID)
	{

		// First, off, start by checking that the key this characterID beongs to is
		// not already assigned to another group, and that the characterID is actually
		// valid
		$character_key = DB::table('account_apikeyinfo_characters')
			->where('characterID', $characterID)
			->pluck('keyID');

		if (!$character_key || SeatPeople::where('keyID', $character_key)->first())
			App::abort('404');

		// Ok, so the key is not already affiliated with another person, so lets create
		// a new person, and assign this key and main

		$person = new SeatPeople;
		$person->personID = DB::table('seat_people')->max('id') + 1;
		$person->keyID = $character_key;
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

		return Redirect::action('ApiKeyController@getPeople')
			->with('success', 'Key ' . Input::get('affected-key') . ' has been added to person group ' . Input::get('personid'));
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
}
