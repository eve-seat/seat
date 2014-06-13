<?php

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
}