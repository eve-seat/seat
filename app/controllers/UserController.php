<?php

class UserController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

	/*
	|--------------------------------------------------------------------------
	| getSignIn()
	|--------------------------------------------------------------------------
	|
	| Check if a user can be signed_in with a remember me, else, show a view
	| to allow for a new sign in.
	|
	*/

	public function getSignIn()
	{
		if (Sentry::check()) {
			return Redirect::intended('/')
				->with('success', 'Welcome back' . Sentry::getUser()->first_name);
		}

		return View::make('user.login');
	}

	/*
	|--------------------------------------------------------------------------
	| postSignIn()
	|--------------------------------------------------------------------------
	|
	| Process a signin attempt
	|
	*/

	public function postSignIn()
	{
		$username = Input::get('username');
		$password = Input::get('password');
		$remember = Input::get('remember_me');

		if (Sentry::authenticate(array('email' => $username, 'password' => $password), $remember == 'yes')) {
			return Redirect::intended('/');
		}

		return Redirect::back()
			->withInput()
			->withErrors('Authentication Failure');
	}

	/*
	|--------------------------------------------------------------------------
	| getSignOut()
	|--------------------------------------------------------------------------
	|
	| Sign out a user session
	|
	*/

	public function getSignOut()
	{
		Sentry::logout();
		return Redirect::action('UserController@getSignIn')
			->with('success', 'Successfully signed out');
	}
}