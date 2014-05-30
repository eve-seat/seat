<?php

class RemindersController extends BaseController {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
		return View::make('password.remind');
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind()
	{
		switch ($response = Password::remind(Input::only('email'),
			function($message) {
				$message->subject('SeAT Password Reset'); }))
		{
			case Password::INVALID_USER:
				return Redirect::back()
					->withErrors(Lang::get($response));

			case Password::REMINDER_SENT:
				return Redirect::action('SessionController@getSignIn')
					->with('success', Lang::get($response));
		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token)) App::abort(404);

		return View::make('password.reset')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Input::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = Password::reset($credentials, function($user, $password)
		{
			// We actually don't care about the Auth user that is returned, use its email to find the Sentry user
			$sentryUser = Sentry::findUserByLogin($user->email);
			$sentryUser->password = $password;
			$sentryUser->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
				return Redirect::back()
					->withErrors(Lang::get($response));

			case Password::PASSWORD_RESET:
				return Redirect::action('SessionController@getSignIn')
					->with('success', 'Your password has been successfully reset');
		}
	}

}