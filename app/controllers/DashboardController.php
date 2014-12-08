<?php

class DashboardController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /*
    |--------------------------------------------------------------------------
    | getDashboard()
    |--------------------------------------------------------------------------
    |
    | Gets the SeAT Dashboard. For now, this actually just redirects to the
    | home page.
    |
    */

	public function getDashboard()
	{
		return View::make('home');
	}

    /*
    |--------------------------------------------------------------------------
    | getSearch()
    |--------------------------------------------------------------------------
    |
    | Perform a search through data available in the SeAT database
    |
    */

    public function getSearch()
    {
        if (Request::ajax()) {

            // Process the searches!
            $characters = DB::table('account_apikeyinfo_characters')
                ->leftJoin('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
                ->join('character_charactersheet', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
                ->join('character_skillintraining', 'account_apikeyinfo_characters.characterID', '=', 'character_skillintraining.characterID')
                ->orderBy('seat_keys.isOk', 'asc')
                ->orderBy('account_apikeyinfo_characters.characterName', 'asc')
                ->groupBy('account_apikeyinfo_characters.characterID')
                ->where('characterName', 'like', '%' . Input::get('q') . '%');

            // Ensure we only get result for characters we have access to
            if (!Sentry::getUser()->hasAccess('recruiter'))
                $characters = $characters->whereIn('seat_keys.keyID', Session::get('valid_keys'))
                    ->get();
            else
                $characters = $characters->get();

            return View::make('search')
                ->with('characters', $characters);
        }
    }
}