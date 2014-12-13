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

/*
|--------------------------------------------------------------------------
| Application Event Listeners
|--------------------------------------------------------------------------
|
| Below you will find the the application event listeners. These each
| have a specific definted role, of which an explanation should be
| found at each event definition
|
*/

/*
|--------------------------------------------------------------------------
| auth.login
|--------------------------------------------------------------------------
|
| Each time we have a login happen, record the time that the login happened
| as well as the source IP address the login occured from.
|
| We will also add a new entry in the login history for the client.
|
*/

Event::listen('auth.login', function($user) {

    // Update the table with the latest time and
    // source IP of the login
    $user->last_login = new DateTime;
    $user->last_login_source = Request::getClientIp();
    $user->save();

    // Once that is saved, lets create a new SeatLoginHistory
    // and record the same information, together with the
    // user_agent_string used.
    $history = new \SeatLoginHistory;
    $history->login_date = new DateTime;
    $history->login_source = Request::getClientIp();
    $history->user_agent = Request::header('User-Agent');

    // Save the login history for the user
    $user->logins()->save($history);
});
