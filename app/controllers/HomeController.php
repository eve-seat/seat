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
		// If the user has 0 keys, we can 0 all of the values
		// If the user has keys, determine values only applicable to
		// this users keys
		if (!Sentry::getUser()->isSuperUser()) {

			if (count(Session::get('valid_keys')) > 0) {

				$total_keys = SeatKey::whereIn('keyID', Session::get('valid_keys'))->count();
				$total_characters = EveCharacterCharacterSheet::join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
					->whereIn('keyID', Session::get('valid_keys'))
					->count();
				$total_isk = EveCharacterCharacterSheet::join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
					->whereIn('keyID', Session::get('valid_keys'))
					->sum('balance');
				$total_skillpoints = EveCharacterCharacterSheetSkills::join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet_skills.characterID')
					->whereIn('keyID', Session::get('valid_keys'))
					->sum('skillpoints');

			} else {

				$total_keys = $total_characters = $total_isk = $total_skillpoints = 0;

			}

		} else {

			// Super user gets all of the data!
			$total_keys = SeatKey::count();
			$total_characters = EveCharacterCharacterSheet::count();
			$total_isk = EveCharacterCharacterSheet::sum('balance');
			$total_skillpoints = EveCharacterCharacterSheetSkills::sum('skillpoints');

		}

		return View::make('home')
			->with('server', $server)
			->with('total_keys', $total_keys)
			->with('total_characters', $total_characters)
			->with('total_isk', $total_isk)
			->with('total_skillpoints', $total_skillpoints);
	}
}