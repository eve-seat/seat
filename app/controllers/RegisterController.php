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

        if (SeatSetting::find('registration_enabled')->value)
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

            // Let's register a user.
            $user = Sentry::register(array(
                'email'    => Input::get('email'),
                'username'    => Input::get('username'),
                'password' => Input::get('password'),
            ));

            // Let's get the activation code
            $data = array(
                'activation_code' => $user->getActivationCode(),
                'user_id' => Crypt::encrypt($user->id)
            );

            // Send the mail with the activation code
            Mail::send('emails.auth.register', $data, function($message) {

                $message->to(Input::get('email'), 'SeAT User')
                    ->subject('SeAT Account Confirmation');
            });

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

    public function getActivate($user_id, $activation_code)
    {

         $user = Sentry::findUserById(Crypt::decrypt($user_id));

         if ($user->attemptActivation($activation_code)) {

            Sentry::login($user);

            return Redirect::action('HomeController@showIndex')
                ->with('success', 'Account successfully activated! Welcome :)');
         }
    }
}
