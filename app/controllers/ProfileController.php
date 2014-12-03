<?php

use App\Services\Validators;

class ProfileController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

	/*
	|--------------------------------------------------------------------------
	| getNew()
	|--------------------------------------------------------------------------
	|
	| Return a view for a new registration
	|
	*/

	public function getView()
	{

		$user = Sentry::getUser();
		$groups = $user->getGroups();

		$key_count = DB::table('seat_keys')
			->where('user_id', $user->id)
			->count();

		return View::make('profile.view')
			->with('user', $user)
			->with('groups', $groups)
			->with('key_count', $key_count);
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

			if($validation->passes()) {

				if(Sentry::checkPassword(Input::get('oldPassword'))) {

					$user->password = Input::get('newPassword_confirmation');
					$user->save();

					return Redirect::action('ProfileController@getView')
						->with('success', 'Your password has successfully been changed.');

				} else {

					return Redirect::action('ProfileController@getView')
						->withInput()
						->withErrors('Your current password did not match.');
				}

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