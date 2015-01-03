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

use App\Services\Validators;
use App\Services\Settings\SettingHelper as Settings;

class ProfileController extends BaseController
{

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

        $user = \Auth::User();
        $groups = \Auth::getUserGroups();

        $key_count = DB::table('seat_keys')
            ->where('user_id', $user->id)
            ->count();

        $characters = \DB::table('account_apikeyinfo_characters')
            ->select('characterID', 'characterName')
            ->join('seat_keys', 'account_apikeyinfo_characters.keyID', '=', 'seat_keys.keyID')
            ->where('seat_keys.user_id', \Auth::User()->id)
            ->get();

        // Prep a small array for the Form builder to let
        // the user choose a 'main' character
        $available_characters = array();
        foreach ($characters as $character_info)
            $available_characters[$character_info->characterID] = $character_info->characterName;

        return View::make('profile.view')
            ->with('user', $user)
            ->with('groups', $groups)
            ->with('key_count', $key_count)
            ->with('available_characters', $available_characters)
            ->with('thousand_seperator', Settings::getSetting('thousand_seperator'))
            ->with('decimal_seperator', Settings::getSetting('decimal_seperator'));
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

        $user = \Auth::User();

        $validation = new Validators\SeatUserPasswordValidator;

        if($validation->passes()) {

            if(Auth::validate(array('email' => Auth::User()->email, 'password' => Input::get('oldPassword')))) {

                $user->password = \Hash::make(Input::get('newPassword_confirmation'));
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

   /*
    |--------------------------------------------------------------------------
    | postSetSettings()
    |--------------------------------------------------------------------------
    |
    | Sets some user configured settings
    |
    */

    public function postSetSettings()
    {

        $validation = new Validators\UserSettingValidator;

        if($validation->passes()) {

            // We will have to lookup the characterID's name
            // quickly before we set the setting, so lets
            // do that.
            $character_name = \DB::table('account_apikeyinfo_characters')
                ->where('characterID', Input::get('main_character_id'))
                ->pluck('characterName');

            Settings::setSetting('color_scheme', Input::get('color_scheme'), \Auth::User()->id);
            Settings::setSetting('thousand_seperator', Input::get('thousand_seperator'), \Auth::User()->id);
            Settings::setSetting('decimal_seperator', Input::get('decimal_seperator'), \Auth::User()->id);
            Settings::setSetting('main_character_id', Input::get('main_character_id'), \Auth::User()->id);
            Settings::setSetting('main_character_name', $character_name, \Auth::User()->id);
            Settings::setSetting('email_notifications', Input::get('email_notifications'), \Auth::User()->id);

            return Redirect::back()
                ->with('success', 'Settings Saved!');

        } else {

            return Redirect::back()
                ->withInput()
                ->withErrors($validation->errors);
        }
    }

   /*
    |--------------------------------------------------------------------------
    | getAccessLog()
    |--------------------------------------------------------------------------
    |
    | Gets the account access history
    |
    */

    public function getAccessLog()
    {

        $access_log = \DB::table('seat_login_history')
            ->where('user_id', \Auth::User()->id)
            ->orderBy('login_date', 'desc')
            ->take(50)
            ->get();

        return View::make('profile.ajax.accesslog')
            ->with('access_log', $access_log);
    }

}
