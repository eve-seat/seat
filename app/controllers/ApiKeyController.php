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
					$jobID = \Queue::push('Seat\EveQueues\Full\Character', array('keyID' => $keyID, 'vCode' => $vCode));
					\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $keyID, 'api' => 'Character', 'scope' => 'Eve', 'status' => 'Queued'));
					break;

				case 'Corporation':
					$jobID = \Queue::push('Seat\EveQueues\Full\Corporation', array('keyID' => $keyID, 'vCode' => $vCode));
					\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $keyID, 'api' => 'Corporation', 'scope' => 'Eve', 'status' => 'Queued'));
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
			App::abord(404);

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
			$jobID = \Queue::push('Seat\EveQueues\Full\Character', array('keyID' => $key->keyID, 'vCode' => $key->vCode));
			\SeatQueueInformation::create(array('jobID' => $jobID, 'ownerID' => $key->keyID, 'api' => 'Character', 'scope' => 'Eve', 'status' => 'Queued'));

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
			App::abord(404);

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
			App::abord(404);

		$key->delete();

		// Delete the information that we have for this key too
		\EveAccountAPIKeyInfo::where('keyID', $keyID)->delete();

		return Redirect::action('ApiKeyController@getAll')
			->with('success', 'Key has been deleted');

	}
}