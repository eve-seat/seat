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

class AuthController extends BaseApiController
{

    /*
    |--------------------------------------------------------------------------
    | store()
    |--------------------------------------------------------------------------
    |
    | POST
    | Checks if credentials passed via api call are valid
    | Call: /api/v1/authenticate | Data: username=%&password=%
    |
    */

    public function store()
    {
        // get inputs from the api
        $username = Request::get('username');
        $password = Request::get('password');

        // determine whether username or email
        $identifier = filter_var(Input::get('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = array($identifier => $username, 'password' => $password);

        // check if creds are valid
        if (Auth::validate($credentials)) {

            // get the relevant user
            $user = \User::where($identifier, '=', $username)
                ->first();

            // build the groups array and then loop through the collection to turn it into an array
            // unserialising permissions as we go
            $user_groups = array();

            foreach (\Auth::getUserGroups($user) as $index => $group) {

                $group['permissions'] = unserialize($group['permissions']);
                array_push($user_groups, $group->toArray());
            }

            // return user and group info
            return Response::json(array(
                'error' => false,
                'user' => $user->toArray(),
                'groups' => $user_groups),
                200
            );

        } else {

            // if validation fails, respond
            return Response::json(array(
                'error' => true,
                'message' => 'user authentication failed'),
                401
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | index()
    |--------------------------------------------------------------------------
    |
    | GET
    | Just errors
    |
    */

    public function index()
    {
        return Response::json(array(
            'error' => true,
            'message' => 'this endpoint can only be accessed through POST'),
            401
        );
    }

    /*
    |--------------------------------------------------------------------------
    | update()
    |--------------------------------------------------------------------------
    |
    | PUT
    | Just errors
    |
    */

    public function update()
    {
        return Response::json(array(
            'error' => true,
            'message' => 'this endpoint can only be accessed through POST'),
            401
        );
    }

    /*
    |--------------------------------------------------------------------------
    | destroy()
    |--------------------------------------------------------------------------
    |
    | DELETE
    | Just errors
    |
    */

    public function destroy()
    {
        return Response::json(array(
            'error' => true,
            'message' => 'this endpoint can only be accessed through POST'),
            401
        );
    }

}
