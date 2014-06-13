<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	// Users have keys allocated to them from the `seat_keys` table.
	// We can check if the user is authed and if so, get the keyID's
	// this user is valid for.

	// We will also get a list of corporation ID's that the user is
	// affiliated to. This can be used to check the permissions then
	// later for specific functions such as starbases etc.

	// We will also check if the user has any director roles in any
	// of the affiliated corporations. Directors will be allowed to
	// assign permissions to other members of their corporation
	if (Sentry::check()) {

		// Valid API Keys
		$valid_keys = SeatKey::where('user_id', Sentry::getUser()->id)
			->lists('keyID');

		Session::put('valid_keys', $valid_keys);

		// Affiliated corporationID's.
		if (!empty($valid_keys)) {

			// Get the list of corporationID's that the user is affiliated with
			$corporation_affiliation = EveAccountAPIKeyInfoCharacters::whereIn('keyID', $valid_keys)
				->groupBy('corporationID')
				->lists('corporationID');

			Session::put('corporation_affiliations', $corporation_affiliation);

			// Determine which corporations the user is a director for
			$is_director = EveCorporationMemberSecurityRoles::whereIn('corporationID', $corporation_affiliation)
				->where('roleID', 1)
				->groupBy('corporationID')
				->lists('corporationID');

			Session::put('is_director', $is_director);

		} else {

			// Just to ensure that we dont have some strange errors later, lets
			// define a empty array in the session for corporation_affiliations
			Session::put('corporation_affiliations', array());
			Session::put('is_director', array());
		}
	}
});

App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (!Sentry::check())
		return Redirect::action('SessionController@getSignIn')
			->with('warning', 'Please Sign In first to continue');
});


Route::filter('auth.superuser', function()
{
	if (!Sentry::check() || !Sentry::getUser()->isSuperUser())
		return Redirect::to('/');
});


/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (!Sentry::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{

	// Check ajax requests for token validity via the header.
	// app.js has the code to grab form tokens and put it in
	// a header, well validate it here if its a post AJAX
	if (Request::ajax() && Request::getMethod() == 'POST')
	{
		if (Session::getToken() != Request::header('X-CSRF-Token'))
		{
			throw new Illuminate\Session\TokenMismatchException;
		}

	} else {
	    if (Request::getMethod() == 'POST' && Session::token() != Input::get('_token'))
	    {
	        throw new Illuminate\Session\TokenMismatchException;
	    }
	}
});

/*
|--------------------------------------------------------------------------
| Key Required Filter
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('key.required', function()
{

	// The below array defines a few routes that require
	// the user to have keys defined before anything useful
	// can be shown
	$key_requied_routes = array(
		"api-key/people*", "corporation/*", "character/*"
	);

	// Loop over the required routes, and ensure that there
	// are keys as required
	foreach ($key_requied_routes as $match) {

		// Check if the current request matches $match
		if (Request::is($match)) {

			// Check that we havea some valid keys defined in Session::get('valid_keys')
			if (count(Session::get('valid_keys')) <= 0)
				return Redirect::action('ApiKeyController@getNewKey')
					->with('warning', 'No API Keys are defined to show you any information. Please enter at least one.');
		}
	}

});