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
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('prefix' => 'account'), function() {

    Route::controller('login', 'SessionController');
    Route::controller('password', 'RemindersController');
    Route::controller('register', 'RegisterController');

});

Route::group(array('before' => 'auth|csrf|key.required'), function() {

    Route::get('/', 'HomeController@showIndex');

    Route::controller('api-key', 'ApiKeyController');
    Route::controller('dashboard', 'DashboardController');

    Route::group(array('prefix' => 'character'), function() {
        Route::controller('mail', 'MailController');
    });

    Route::controller('character', 'CharacterController');
    Route::controller('corporation', 'CorporationController');
    Route::controller('eve', 'EveController');
    Route::controller('queue', 'QueueController');
    Route::controller('notification', 'NotificationController');

    // Super users are the only ones that should be accessing
    // the following controllers
    Route::group(array('prefix' => 'configuration', 'before' => 'auth.superuser'), function() {

        Route::controller('user', 'UserController');
        Route::controller('settings', 'SettingsController');
        Route::controller('groups', 'GroupsController');
    });

    Route::controller('helpers', 'HelperController');
    Route::controller('debug', 'DebugController');
    Route::controller('profile', 'ProfileController');
    Route::controller('help', 'HelpController');

});

// api route group
Route::group(array('prefix' => 'api/v1', 'before' => 'auth.api'), function()
{
    Route::resource('authenticate', 'ApiAuthController');
});
