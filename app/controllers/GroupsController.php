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

class GroupsController extends BaseController
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
    | Get details about ALL THE THINGS!
    |
    */

    public function getAll()
    {

        $groups = \Auth::findAllGroups();
        $counter = array();

        foreach($groups as $group) {

            $users = \Auth::findAllUsersInGroup($group);
            $counter[$group->name] = count($users);
        }

        return View::make('groups.all')
            ->with('groups', $groups)
            ->with('counter', $counter);
    }

    /*
    |--------------------------------------------------------------------------
    | getDetail()
    |--------------------------------------------------------------------------
    |
    | Get the shizzle about this group
    |
    */

    public function getDetail($groupID)
    {

        $group = \Auth::findGroupById($groupID);
        $users = \Auth::findAllUsersInGroup($group);
        $available_permissions = SeatPermissions::all();
        $permissions = \Auth::getGroupPermissions($group);

        return View::make('groups.detail')
            ->with('group', $group)
            ->with('users', $users)
            ->with('has_permissions', $permissions)
            ->with('available_permissions', $available_permissions);
    }

    /*
    |--------------------------------------------------------------------------
    | getDetail()
    |--------------------------------------------------------------------------
    |
    | Update this shizzle!
    |
    */

    public function postUpdateGroup($groupID)
    {

        $permissions = Input::except('_token');
        $available_permissions = SeatPermissions::all();
        $group = \Auth::findGroupById($groupID);

        $group->permissions = serialize($permissions);
        $group->save();

        if ($group->save())
            return Redirect::action('GroupsController@getDetail', $groupID)
                ->with('success', 'Permissions updated!');

        return Redirect::action('GroupsController@getDetail', $groupID)
            ->withErrors('Permissions failed to update :(');
    }

    /*
    |--------------------------------------------------------------------------
    | postNewGroup()
    |--------------------------------------------------------------------------
    |
    | Add this!
    |
    */

    public function postNewGroup()
    {

        $new_group = Input::All();
        $validation = new Validators\SeatGroupValidator($new_group);

        if ($validation->passes()) {

            // Create the group
            $group = \Auth::createGroup(array(
                'name' => Input::get('groupName')
            ));

            if($group) {
                return Redirect::action('GroupsController@getAll')
                    ->with('success', 'Group has been added!');
            } else {
                return Redirect::action('GroupsController@getAll')
                 ->withErrors('The robot seems to think that this group already exists...');
            }

        } else {

            return Redirect::action('GroupsController@getAll')
                ->withErrors($validation->errors);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getDeleteGroup()
    |--------------------------------------------------------------------------
    |
    | This group can go to hell!
    |
    */

    public function getDeleteGroup($groupID)
    {

        $group = \Auth::findGroupById($groupID);

        if($group->name == 'Administrators')
            return Redirect::action('GroupsController@getAll')
                ->withErrors('You cannot delete the Administrators group, stupid!');

        \Auth::deleteGroup($group);

        return Redirect::action('GroupsController@getAll')
            ->with('success', 'Group has been deleted');
    }

    /*
    |--------------------------------------------------------------------------
    | getRemoveUser()
    |--------------------------------------------------------------------------
    |
    | Remove a user from a group
    |
    */

    public function getRemoveUser($userID, $groupID)
    {

        $group = \Auth::findGroupById($groupID);
        $user = \User::find($userID);

        if($userID == 1 AND $group->name == 'Administrators')
            return Redirect::action('GroupsController@getDetail', array('groupID' => $groupID))
                    ->withErrors('You cant remove the admin from this group!');

        \Auth::removeUserFromGroup($user, $group);

        return Redirect::action('GroupsController@getDetail', array('groupID' => $groupID))
            ->with('success', 'User has been removed!');
    }
}
