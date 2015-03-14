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

/*
|--------------------------------------------------------------------------
| security.log
|--------------------------------------------------------------------------
|
| Log security events to the database.
|
| This security log event can be fired with:
|   Event::fire('security.log', array(7, 'Test Message', 'bob'));
|
*/

Event::listen('security.log', function($event_id, $message = null, $triggered_for = null) {

    // Lets define a few known event types. Logging on an event
    // should be as trivial as:
    //  Event::fire('security.log', array(3));
    $security_event_ids = array(

        // Users
        1 => 'An Administrator has created a new user',
        2 => 'A new user has been registered',
        3 => 'A user account has been activated',
        4 => 'A user has been deleted',
        5 => 'A user has been modified',

        // Groups
        6 => 'A group has been created',
        7 => 'A group has been deleted',
        8 => 'A group has been modified',

        // Users to groups
        9 => 'A user has been added to a group',
        10 => 'A user has been removed from a group',

        // Permissions to Groups
        11 => 'A permission was added to a group',
        12 => 'A permission has been revoked from a group',

        // Failed login
        13 => 'Account has failed successful login more than 3 times',

        // Failed ACL check
        14 => 'User failed critical permissions check',

        // Impersonation
        15 => 'A user has been impersonated',

        // Session
        16 => 'A user has signed in',
        17 => 'A user has signed out'

    );

    // Check that this is a known eventID
    if (!array_key_exists($event_id, $security_event_ids))
        throw new \Exception('Tried to log a unknown security event id.');

    // Prepare the security event log entry...
    $log_entry = new SecurityLog;
    $log_entry->event_type_id = $event_id;
    $log_entry->triggered_by = \Auth::user()->username;
    $log_entry->triggered_for = $triggered_for;
    $log_entry->path = Request::path();
    $log_entry->message = implode('. ', array($security_event_ids[$event_id], $message));
    $log_entry->user_ip = Request::getClientIp();
    $log_entry->user_agent = Request::header('User-Agent');
    $log_entry->valid_keys = implode(',', Session::get('valid_keys'));
    $log_entry->corporation_affiliations = implode(',', Session::get('corporation_affiliations'));

    // ... and attach it to the user that caused it
    \Auth::User()->security_logs()->save($log_entry);

    // If we want to track the last event_id, we will encrypt it
    // in a cached value to pull into say a view or something
    // later.
    Cache::put('last_error_ref', Crypt::encrypt($log_entry->id), 5);

});
