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
    | Default Authentication Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the authentication driver that will be utilized.
    | This driver manages the retrieval and authentication of the users
    | attempting to get access to protected areas of your application.
    |
    | Supported: "database", "eloquent"
    |
    */

    'driver' => 'cushion',

    /*
    |--------------------------------------------------------------------------
    | Authentication Model
    |--------------------------------------------------------------------------
    |
    | When using the "Eloquent" authentication driver, we need to know which
    | Eloquent model should be used to retrieve your users. Of course, it
    | is often just the "User" model but you may use whatever you like.
    |
    */

    'model' => 'User',

    /*
    |--------------------------------------------------------------------------
    | Authentication Table
    |--------------------------------------------------------------------------
    |
    | When using the "Database" authentication driver, we need to know which
    | table should be used to retrieve your users. We have chosen a basic
    | default value but you may easily change it to any table you like.
    |
    */

    'table' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Password Reminder Settings
    |--------------------------------------------------------------------------
    |
    | Here you may set the settings for password reminders, including a view
    | that should be used as your password reminder e-mail. You will also
    | be able to set the name of the table that holds the reset tokens.
    |
    | The "expire" time is the number of minutes that the reminder should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'reminder' => array(

        'email' => 'emails.auth.reminder',

        'table' => 'password_reminders',

        'expire' => 60,

    ),

);
