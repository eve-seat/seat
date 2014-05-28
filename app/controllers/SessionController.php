<?php

class SessionController extends BaseController {

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

		return View::make('session.login');
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
		$email = Input::get('email');
		$password = Input::get('password');
		$remember = Input::get('remember_me');

		$destination = Redirect::back()
			->withInput();

		try {
			if (Sentry::authenticate(array('email' => $email, 'password' => $password), $remember == 'yes')) {
				$destination = Redirect::intended('/');
			}
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
			$destination = $destination->withErrors('Please enter a login');
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
			$destination = $destination->withErrors('Please enter a password');
		}
		catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
			$destination = $destination->withErrors('Authentication failure');
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
			$destination = $destination->withErrors('User not found');
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
			$destination = $destination->withErrors('User not activated');
		}
		return $destination;
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
		return Redirect::action('SessionController@getSignIn')
			->with('success', 'Successfully signed out');
	}
}