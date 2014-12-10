<?php

class GroupsController extends BaseController {

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
	| build all groups table
	|
	*/

	public function getAll()
	{
		$groups = Sentry::findAllGroups();
		$counter = array();

		foreach($groups as $group)
		{
			$users = Sentry::findAllUsersInGroup($group);
			$counter[$group->name] = count($users);
		}
		
		return View::make('groups.all')
			->with('groups', $groups)
			->with('counter', $counter);
	}

	/*
	|--------------------------------------------------------------------------
	| getDetail()
	|--------------------------------------------------------------------------
	|
	| Show all of the user details
	|
	*/

	public function getDetail($groupID)
	{

	}

}