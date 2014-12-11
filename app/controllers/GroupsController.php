<?php

use App\Services\Validators;

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
	| Get details about ALL THE THINGS!
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
	| Get the shizzle about this group
	|
	*/

	public function getDetail($groupID)
	{
		try 
		{
			$group = Sentry::findGroupById($groupID);
			$users = Sentry::findAllUsersInGroup($group);
			$availablePermissions = SeatPermissions::all();
		} 
		catch (Cartalyst\Sentry\Groups\GroupExistsException $e) 
		{
			App::abort(404);
		}

		return View::make('groups.detail')
			->with('group', $group)
			->with('users', $users)
			->with('hasPermissions', $group->getPermissions())
			->with('availablePermissions', $availablePermissions);
	}

	/*
	|--------------------------------------------------------------------------
	| getDetail()
	|--------------------------------------------------------------------------
	|
	| Update this shizzle!
	|
	*/

	public function postUpdateGroup($groupID)
	{
		$permissions = Input::except('_token');

		$group = Sentry::findGroupById($groupID);
		$group->permissions = $permissions;

		if ($group->save())
	    {
	        return Redirect::action('GroupsController@getDetail', $groupID)
				->with('success', 'Permissions updated!');
	    }
	    else
	    {
	        return Redirect::action('GroupsController@getDetail', $groupID)
				->withErrors('Permissions failed to update :(');
	    }
	}

	/*
	|--------------------------------------------------------------------------
	| postNewGroup()
	|--------------------------------------------------------------------------
	|
	| Add this!
	|
	*/

	public function postNewGroup()
	{

		$newGroup = Input::All();
		$validation = new Validators\SeatGroupValidator($newGroup);

		if ($validation->passes()) 
		{
			try
			{
			    // Create the group
			    $group = Sentry::createGroup(array(
			        'name'        => Input::get('groupName')
			    ));

			    return Redirect::action('GroupsController@getAll')
					->with('success', 'Group has been added!');
			}
			catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
			{
			    return Redirect::action('GroupsController@getAll')
					->withErrors('The robot seems to think that this group already exists...');
			}
		}
		else
		{
			return Redirect::action('GroupsController@getAll')
					->withErrors('You need to provide a name for the new group!');
		}
	}

	/*
	|--------------------------------------------------------------------------
	| getDeleteGroup()
	|--------------------------------------------------------------------------
	|
	| This group can go to hell!
	|
	*/

	public function getDeleteGroup($groupID)
	{
		try
		{
		    $group = Sentry::findGroupById($groupID);
		    
		    if($group->name == "Administrators")
		    {
		    	return Redirect::action('GroupsController@getAll')
					->withErrors('You cannot delete the Administrators group, stupid!');
		    }

		    $group->delete();

		    return Redirect::action('GroupsController@getAll')
				->with('success', 'Group has been deleted');
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
		    App::abort(404);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| getRemoveUser()
	|--------------------------------------------------------------------------
	|
	| ACCESS DENIED!
	|
	*/

	public function getRemoveUser($userID, $groupID)
	{
		
	}

}