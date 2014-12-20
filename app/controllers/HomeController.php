<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

class HomeController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |   Route::get('/', 'HomeController@showWelcome');
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
        if (!\Auth::isSuperUser()) {

            if (count(Session::get('valid_keys')) > 0) {

                $total_keys = SeatKey::whereIn('keyID', Session::get('valid_keys'))->count();
                $total_characters = EveCharacterCharacterSheet::join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
                    ->join('account_apikeyinfo', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
                    ->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'))
                    ->where('account_apikeyinfo.type','!=','Corporation')
                    ->count();
                $total_isk = EveCharacterCharacterSheet::join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet.characterID')
                    ->join('account_apikeyinfo', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
                    ->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'))
                    ->where('account_apikeyinfo.type','!=','Corporation')
                    ->sum('balance');
                $total_skillpoints = EveCharacterCharacterSheetSkills::join('account_apikeyinfo_characters', 'account_apikeyinfo_characters.characterID', '=', 'character_charactersheet_skills.characterID')
                    ->join('account_apikeyinfo', 'account_apikeyinfo.keyID', '=', 'account_apikeyinfo_characters.keyID')
                    ->whereIn('account_apikeyinfo_characters.keyID', Session::get('valid_keys'))
                    ->where('account_apikeyinfo.type','!=','Corporation')
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
