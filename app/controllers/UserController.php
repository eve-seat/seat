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

class UserController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | __construct()
    |--------------------------------------------------------------------------
    |
    | Sets up the class to ensure that CSRF tokens are validated on the POST
    | verb
    |
    */

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

    /*
    |--------------------------------------------------------------------------
    | getAll()
    |--------------------------------------------------------------------------
    |
    | Get all of the users in the database
    |
    */

    public function getAll()
    {

        $users = \User::all();

        return View::make('user.all')
            ->with(array('users' => $users));
    }

    /*
    |--------------------------------------------------------------------------
    | postNewUser()
    |--------------------------------------------------------------------------
    |
    | Registers a new user in the database
    |
    */

    public function postNewUser()
    {

        // Grab the inputs and validate them
        $new_user = Input::only(
            'email', 'username', 'password', 'first_name', 'last_name', 'is_admin'
        );

        $validation = new Validators\SeatUserRegisterValidator;

        // Should the form validation pass, continue to attempt to add this user
        if ($validation->passes()) {

            // Because users are soft deleted, we need to check if if
            // it doesnt actually exist first.
            $user = \User::withTrashed()
                ->where('email', Input::get('email'))
                ->orWhere('username', Input::get('username'))
                ->first();

            // If we found the user, restore it and set the
            // new values found in the post
            if($user)
                $user->restore();
            else
                $user = new \User;

            // With the user object ready, work the update
            $user->email = Input::get('email');
            $user->username = Input::get('username');
            $user->password = Hash::make(Input::get('password'));
            $user->activated = 1;

            if (Input::get('is_admin') == 'yes') {

                $adminGroup = \Auth::findGroupByName('Administrators');
                $user->addGroup($adminGroup);
            }

            $user->save();

            return Redirect::action('UserController@getAll')
                ->with('success', 'User ' . Input::get('email') . ' has been added');

        } else {

            return Redirect::back()
                    ->withInput()
                ->withErrors($validation->errors);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getDetail()
    |--------------------------------------------------------------------------
    |
    | Show all of the user details
    |
    */

    public function getDetail($userID)
    {

        $user = Auth::findUserById($userID);
        $allGroups = Auth::findAllGroups();
        $tmp = Auth::getUserGroups($user);
        $hasGroups = array();

        foreach($tmp as $group)
            $hasGroups = array_add($hasGroups, $group->name, '1');

        return View::make('user.detail')
            ->with('user', $user)
            ->with('availableGroups', $allGroups)
            ->with('hasGroups', $hasGroups);
    }

    /*
    |--------------------------------------------------------------------------
    | getImpersonate()
    |--------------------------------------------------------------------------
    |
    | Impersonate a user
    |
    */

    public function getImpersonate($userID)
    {

        // Find the user
        $user = Auth::findUserById($userID);

        // Attempt to authenticate using the user->id
        Auth::loginUsingId($userID);

        return Redirect::action('HomeController@showIndex')
            ->with('warning', 'You are now impersonating ' . $user->email);
    }

    /*
    |--------------------------------------------------------------------------
    | postUpdateUser()
    |--------------------------------------------------------------------------
    |
    | Changes a user's details
    |
    */

    public function postUpdateUser()
    {

        // Find the user
        $user = Auth::findUserById(Input::get('userID'));

        // Find the administrators group
        $admin_group = Auth::findGroupByName('Administrators');

        // ... and check that it exists
        if(!$admin_group)
            return Redirect::back()
                ->withInput()
                ->withErrors('Administrators group could not be found');

        $user->email = Input::get('email');

        if (Input::get('username') != '')
            $user->username = Input::get('username');

        if (Input::get('password') != '')
            $user->password = Hash::make(Input::get('password'));

        $groups = Input::except('_token', 'username', 'password', 'first_name', 'last_name', 'userID', 'email');

        // Delete all the permissions the user has now
        \GroupUserPivot::where('user_id', '=', $user->id)
            ->delete();

        // Restore the permissions.
        //
        // NB Todo. Check that we not revoking 'Administrors' access from
        // the site admin
        foreach($groups as $group => $value) {

            $thisGroup = Auth::findGroupByName(str_replace("_", " ", $group));
            Auth::addUserToGroup($user, $thisGroup);
        }

        if ($user->save())
            return Redirect::action('UserController@getDetail', array($user->getKey()))
                ->with('success', 'User has been updated');
        else
            return Redirect::back()
                ->withInput()
                ->withErrors('Error updating user');
    }

    /*
    |--------------------------------------------------------------------------
    | getDeleteUser()
    |--------------------------------------------------------------------------
    |
    | Deletes a user from the database
    |
    */

    public function getDeleteUser($userID)
    {

        $user = Auth::findUserById($userID);

        // Lets return the keys that this user owned back
        // to the admin user
        \SeatKey::where('user_id', $user->id)
            ->update(array('user_id' => 1));

        $user->forceDelete();

        return Redirect::action('UserController@getAll')
            ->with('success', 'User has been deleted');
    }
}
