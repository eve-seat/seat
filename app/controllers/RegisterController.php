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

class RegisterController extends BaseController
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

    public function getNew()
    {

        if (Settings::getSetting('registration_enabled'))
            return View::make('register.enabled');
        else
            return View::make('register.disabled');
    }

    /*
    |--------------------------------------------------------------------------
    | postNew()
    |--------------------------------------------------------------------------
    |
    | Process a new account registration
    |
    */

    public function postNew()
    {

        $validation = new Validators\SeatUserRegisterValidator;

        if ($validation->passes()) {

            // Check that we don't already have the user that is
            // attempting registration
            if (\User::where('email', Input::get('email'))->orWhere('username', Input::get('username'))->first())
                return Redirect::back()
                    ->withInput()
                    ->withErrors('The chosen username or email address is already taken.');

            // Let's register a user.
            $user = new \User;
            $user->email = Input::get('email');
            $user->username = Input::get('username');
            $user->password = Hash::make(Input::get('password'));
            $user->activation_code = str_random(24);
            $user->activated = 0;
            $user->save();

            // Prepare data to be sent along with the email. These
            // are accessed by their keys in the email template
            $data = array(
                'activation_code' => $user->activation_code
            );

            // Send the email with the activation link
            Mail::send('emails.auth.register', $data, function($message) {

                $message->to(Input::get('email'), 'New SeAT User')
                    ->subject('SeAT Account Confirmation');
            });

            // And were done. Redirect to the login again
            return Redirect::action('SessionController@getSignIn')
                ->with('success', 'Successfully registered a new account. Please check your email for the activation link.');

        } else {

            return Redirect::back()
                ->withInput()
                ->withErrors($validation->errors);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getActivate()
    |--------------------------------------------------------------------------
    |
    | Attempt to activate a new account
    |
    */

    public function getActivate($activation_code)
    {

        // We start by looking up the activation code that we got.
        $user = \User::where('activation_code', $activation_code)->first();

        // If we got the user that matched the activation code,
        // set the account to active and null the code
        if ($user) {

            $user->activation_code = null;
            $user->activated = 1;
            $user->save();

            Auth::loginUsingId($user->id);

            return Redirect::action('HomeController@showIndex')
                ->with('success', 'Account successfully activated! Welcome ' . $user->username . ' :)');

        } else {

            return Redirect::action('SessionController@getSignIn')
                ->withErrors('Something does not look right with the link you clicked.');
        }
    }
}
