<?php

use App\Services\Permissions\PermissionHelper;

class PermissionsController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| getShow()
	|--------------------------------------------------------------------------
	|
	| Show the corporations that permissions can be set for
	|
	*/

	public function getShowAll()
	{

		// First, ensure that this user has the minimum access required. The
		// user should be at least a superuser of a director of one of the corporations
		// he has characters in
		if (!Sentry::getUser()->isSuperUser())
			App::abort(404);

		// Get the corporations
		$corporations = EveCorporationCorporationSheet::all();

		return View::make('permissions.all')
			->with('corporations', $corporations);
	}

	/*
	|--------------------------------------------------------------------------
	| getCorporation()
	|--------------------------------------------------------------------------
	|
	| Get the members of a corporatino and their permissions
	|
	*/

	public function getCorporation($corporationID)
	{

		// Very first check is to ensure the user has the required access
		if (!Sentry::getUser()->isSuperUser())
			App::abort(404);

		// Lets get the SeAT accounts with keys having members in this corporation
		$seat_users = DB::table('users')
			->select(DB::raw('`users`.`id`'), DB::raw('`users`.`email`'))
			->join('seat_keys', 'seat_keys.user_id', '=', 'users.id')
			->join('account_apikeyinfo_characters', 'seat_keys.keyID', '=', 'account_apikeyinfo_characters.keyID')
			->where('account_apikeyinfo_characters.corporationID', $corporationID)
			->groupBy('users.id')
			->get();

		// Get all of the configured groups in the system and prepare a array of it
		$available_groups = array();
		foreach (Sentry::findAllGroups() as $available_group)
			$available_groups[] = $available_group->name;

		// With the seat_users now known, we will loop over them and extract all of the
		// groups that they belong to
		$group_memberships = array();
		foreach ($seat_users as $user)
			foreach (Sentry::findUserById($user->id)->getGroups() as $group)
				$group_memberships[$user->id][] = $group->name;

		// Get the accounts & characters for each SeAT user
		$character_information = DB::table('account_apikeyinfo_characters')
			->join('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
			->where('account_apikeyinfo_characters.corporationID', $corporationID)
			->get();

		// Loop the array and prepare something for the template
		$user_characters = array();
		foreach ($character_information as $char)
			$user_characters[$char->user_id][] = array('characterID' => $char->characterID, 'characterName' => $char->characterName);

		return View::make('permissions.ajax.detail')
			->with('seat_users', $seat_users)
			->with('user_characters', $user_characters)
			->with('available_groups', $available_groups)
			->with('group_memberships', $group_memberships);
	}

	/*
	|--------------------------------------------------------------------------
	| postSetPermission()
	|--------------------------------------------------------------------------
	|
	| Sets a permission
	|
	*/

	public function postSetPermission()
	{

		// Very first check is to ensure the user has the required access
		if (!Sentry::getUser()->isSuperUser())
			App::abort(404);

		$group = Input::get('group');
		$user = Input::get('user');

		// Lets validate the post values.
		// First the group
		try {

			$group = Sentry::findGroupByName($group);
			
		} catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {

			App::abort(404);
		}

		// Then the user
		try {

			$user = Sentry::findUserById($user);	

		} catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
			
			App::abort(404);

		} catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
			
			App::abort(401);
		}

		// Determine if the user is in the group. If so, remove the user. If not, add the user
		if ($user->inGroup($group))
			$user->removeGroup($group);
		else
			$user->addGroup($group);
	}
}