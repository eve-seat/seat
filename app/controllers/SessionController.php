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

use App\Services\Validators\SeatUserValidator;

class SessionController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /*
    |--------------------------------------------------------------------------
    | getSignIn()
    |--------------------------------------------------------------------------
    |
    | Check if a user can be signed_in with a remember me, else, show a view
    | to allow for a new sign in.
    |
    */

    public function getSignIn()
    {
        if (Auth::check()) {

            // Grab the last login history event id for this user. Need to
            // remember that the event 'auth.login' inserted a new entry
            // so the last one is technically last-1
            $last_login = \SeatLoginHistory::where('user_id', Auth::User()->id)
                ->max('id');

            // With the max ID for this user known, lets get the last login
            // entry from their log
            $last_login = \SeatLoginHistory::where('user_id', Auth::User()->id)
                ->where('id', '<', $last_login)
                ->orderBy('id', 'desc')
                ->first();

            // If this user has no last login, we will welcome them, else
            // we will give some information about the last login
            if ($last_login)
                $welcome_message = 'Welcome back ' . Auth::user()->username . '. Your last login was from ' .
                                    $last_login->login_source . ' which was ' .
                                    Carbon\Carbon::parse($last_login->login_date)->diffFOrHumans() .
                                    ' at ' . $last_login->login_date;
            else
                $welcome_message = 'This looks like your first login! Welcome :)';

            // Return the view with the information
            return Redirect::intended('/')
                ->with('success', $welcome_message);

        }

        return View::make('session.login');
    }

    /*
    |--------------------------------------------------------------------------
    | postSignIn()
    |--------------------------------------------------------------------------
    |
    | Process a signin attempt
    |
    */

    public function postSignIn()
    {
        $email = Input::get('email');
        $password = Input::get('password');
        $remember = Input::get('remember_me');
        $destination = Redirect::back()->withInput();

        $validation = new SeatUserValidator;

        if ($validation->passes()) {

            // First attempt authentication using a email address
            if (Auth::attempt(array('email' => $email, 'password' => $password), ($remember ? true : false)))
                return $destination;

            // Else, we attempt authentication using the username
            elseif (Auth::attempt(array('username' => $email, 'password' => $password), ($remember ? true : false)))
                return $destination;

            else
                return $destination->withErrors('Authentication failure');
        }

        return $destination->withErrors($validation->errors);
    }

    /*
    |--------------------------------------------------------------------------
    | getSignOut()
    |--------------------------------------------------------------------------
    |
    | Sign out a user session
    |
    */

    public function getSignOut()
    {
        Auth::logout();

        return Redirect::action('SessionController@getSignIn')
            ->with('success', 'Successfully signed out');
    }
}
