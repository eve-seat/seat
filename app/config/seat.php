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

return array(

    /*
    |--------------------------------------------------------------------------
    | SeAT Version
    |--------------------------------------------------------------------------
    |
    */

    'version' => '0.14.1',

    /*
    |--------------------------------------------------------------------------
    | Ban Limit
    |--------------------------------------------------------------------------
    |
    | Specifies the amount of times a API call should fail before it should
    | should be banned from being called again
    |
    */

    'ban_limit' => 20,

    /*
    |--------------------------------------------------------------------------
    | Ban Grance Period
    |--------------------------------------------------------------------------
    |
    | Specifies the amount of minutes a key should live in the cache when
    | counting the bans for it.
    |
    | It is important to note that the actual schedule at which API calls are
    | made should be taken into account when setting this Value. ie: For
    | character API Updates, which occur hourly, it would take 10 hours
    | to reach the limit and get banned. If we set the ban_grace key
    | annything below 600, we will never reach a point where a ban
    | will occur
    |
    */

    'ban_grace' => 60 * 24,

    /*
    |--------------------------------------------------------------------------
    | Eve API Error Count Maximum
    |--------------------------------------------------------------------------
    |
    | Specifies the maximum amount of errors that should occur before the SeAT
    | API updator workers will stop processing update jobs. This allows us
    | be aware of the fact that the EVE API may be down/sick and prevent
    | the storm ahead with updates.
    |
    */

    'error_limit' => 60,

);
