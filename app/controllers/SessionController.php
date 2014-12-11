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
        if (Sentry::check())
            return Redirect::intended('/')
                ->with('success', 'Welcome back' . Sentry::getUser()->first_name);

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

        try {

            $field = filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            Cartalyst\Sentry\Users\Eloquent\User::setLoginAttributeName($field);

            if(Sentry::authenticate(array($field => $email, 'password' => $password), $remember == 'yes'))
                $destination = Redirect::intended('/');

        } catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {

            $destination = $destination->withErrors('Please enter a login');

        } catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {

            $destination = $destination->withErrors('Please enter a password');

        } catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {

            $destination = $destination->withErrors('Authentication failure');

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

            $destination = $destination->withErrors('User not found');

        } catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {

            $destination = $destination->withErrors('User not activated');

        } catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {

            $destination = $destination->withErrors('User has been suspended due to too many failed login attempts. Please try again later.');
        }

        return $destination;
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
        Sentry::logout();

        return Redirect::action('SessionController@getSignIn')
            ->with('success', 'Successfully signed out');
    }
}
