<?php

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

    Route::controller('permissions', 'PermissionsController');

    Route::controller('helpers', 'HelperController');
    Route::controller('user', 'UserController');
    Route::controller('debug', 'DebugController');

    Route::controller('profile', 'ProfileController');
    Route::controller('help', 'HelpController');

});

Route::when('user/*', 'auth.superuser');
