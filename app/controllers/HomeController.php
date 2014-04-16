<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showIndex()
	{

		// Prepare some summarized totals for the home view to display in the
		// widgets

		// EVE Online Server Information
		$server = EveServerServerStatus::find(1);

		// Key Information
		$total_keys = SeatKey::count();
		$total_characters = EveCharacterCharacterSheet::count();
		$total_isk = EveCharacterCharacterSheet::sum('balance');
		$total_skillpoints = EveCharacterCharacterSheetSkills::sum('skillpoints');

		return View::make('home')
			->with('server', $server)
			->with('total_keys', $total_keys)
			->with('total_characters', $total_characters)
			->with('total_isk', $total_isk)
			->with('total_skillpoints', $total_skillpoints);
	}
}