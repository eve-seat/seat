<?php

use App\Services\Validators;

class UserController extends BaseController {

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
	| Get all of the users in the database
	|
	*/

	public function getAll()
	{
		$users = Sentry::findAllUsers();

		return View::make('user.all')
			->with(array('users' => $users));
	}

	/*
	|--------------------------------------------------------------------------
	| postNewUser()
	|--------------------------------------------------------------------------
	|
	| Registers a new user in the database
	|
	*/

	public function postNewUser()
	{

		// Grab the inputs and validate them
		$new_user = Input::only(
			'email', 'password', 'first_name', 'last_name', 'is_admin'
		);

		$validation = new Validators\SeatUserValidator($new_user);

		// Should the form validation pass, continue to attempt to add this user
		if ($validation->passes()) {

			if ($user = Sentry::register(array('email' => Input::get('email'), 'password' => Input::get('password'), 'first_name' => Input::get('first_name'), 'last_name' => Input::get('last_name')), true)) {

				if (Input::get('is_admin') == 'yes') {

					try {

						$adminGroup = Sentry::findGroupByName('Administrators');

					} catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {

						return Redirect::back()
							->withInput()
							->withErrors('Administrators group could not be found');
					}

					$user->addGroup($adminGroup);
				}

				return Redirect::action('UserController@getAll')
					->with('success', 'User ' . Input::get('email') . ' has been added');

			} else {

				return Redirect::back()
					->withInput()
					->withErrors('Error adding user');
			}

		} else {

			return Redirect::back()
					->withInput()
				->withErrors($validation->errors);
		}


	}

	/*
	|--------------------------------------------------------------------------
	| getDetail()
	|--------------------------------------------------------------------------
	|
	| Show all of the user details
	|
	*/

	public function getDetail($userID)
	{

		try {

			$user = Sentry::findUserById($userID);

		} catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

			App::abort(404);
		}

		return View::make('user.detail')
			->with('user', $user);
	}

	/*
	|--------------------------------------------------------------------------
	| getImpersonate()
	|--------------------------------------------------------------------------
	|
	| Impersonate a user
	|
	*/

	public function getImpersonate($userID)
	{

		try {

			$user = Sentry::findUserById($userID);

		} catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

			App::abort(404);
		}

	 	Sentry::login($user);

 		return Redirect::action('HomeController@showIndex')
 			->with('warning', 'You are now impersonating ' . $user->email);
	}

	/*
	|--------------------------------------------------------------------------
	| postUpdateUser()
	|--------------------------------------------------------------------------
	|
	| Changes a user's details
	|
	*/

	public function postUpdateUser()
	{

		try {

			$user = Sentry::findUserById(Input::get('userID'));

		} catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

			App::abort(404);
		}

		try {

			$adminGroup = Sentry::findGroupByName('Administrators');

		} catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {

			return Redirect::back()
				->withInput()
				->withErrors('Administrators group could not be found');
		}

		$user->email = Input::get('email');

		if (Input::get('password') != '')
			$user->password = Input::get('password');

		$user->first_name = Input::get('first_name');
		$user->last_name = Input::get('last_name');

		if (Input::get('is_admin') == 'yes')
			$user->addGroup($adminGroup);
		else
			$user->removeGroup($adminGroup);

		if ($user->save())
			return Redirect::action('UserController@getDetail', array($user->getKey()))
				->with('success', 'User has been updated');
		else
			return Redirect::back()
				->withInput()
				->withErrors('Error updating user');
	}

	/*
	|--------------------------------------------------------------------------
	| getDeleteUser()
	|--------------------------------------------------------------------------
	|
	| Deletes a user from the database
	|
	*/

	public function getDeleteUser($userID)
	{

		try {

			$user = Sentry::findUserById($userID);
			$user->delete();
		}

		catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

			App::abort(404);
		}

		return Redirect::action('UserController@getAll')
			->with('success', 'User has been deleted');
	}

	/*
	|--------------------------------------------------------------------------
	| postChangePassword()
	|--------------------------------------------------------------------------
	|
	| Changes the password but sends an email to the user 
	| to confirm the password change
	|
	*/

	public function postChangePassword()
	{
		try {

			$user = Sentry::getUser();

			$validation = new Validators\SeatUserPasswordValidator;

			if($validation->passes()){

				if(Sentry::checkPassword(Input::get('oldPassword'))){
					$user->password = Input::get('newPassword_confirmation');
					$user->save();
					return Redirect::action('ProfileController@getView')
						->with('success', 'Your password has successfully been changed.');	

				} else {

				return Redirect::action('ProfileController@getView')
						->withInput()
						->withErrors('Your current password did not match.');
				} // end checkPassword

			} else {

				return Redirect::action('ProfileController@getView')
					->withInput()
					->withErrors($validation->errors);

			}

		}

		catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

			App::abort(404);
		}

	}

}