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
        $groups = $user->getGroups();

        $key_count = DB::table('seat_keys')
            ->where('user_id', $user->id)
            ->count();

        return View::make('profile.view')
            ->with('user', $user)
            ->with('groups', $groups)
            ->with('key_count', $key_count);
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

            if(Auth::validate(array('email' => $email, 'password' => Hash::make(Input::get('oldPassword'))))) {

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
}
