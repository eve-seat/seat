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

class LogController extends BaseController
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
    | getViewSecurity()
    |--------------------------------------------------------------------------
    |
    | Get the Security Event Log
    |
    */

    public function getViewSecurity()
    {

        $logs = DB::table('security_log')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return View::make('logs.security')
            ->with('logs', $logs);

    }

    /*
    |--------------------------------------------------------------------------
    | postLookupSecurityEvent()
    |--------------------------------------------------------------------------
    |
    | Lookup a user provided event
    |
    */

    public function postLookupSecurityEvent()
    {

        $event = DB::table('security_log')
            ->where('id', Crypt::decrypt(Input::get('event')))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return View::make('logs.security')
            ->with('logs', $event);

    }

}
