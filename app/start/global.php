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
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

    app_path().'/commands',
    app_path().'/controllers',
    app_path().'/models',
    app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
    Log::error($exception);

    // Based on the state of the application, we will
    // either render the pretty error 500, or the
    // detailed stack trace information
    if (!Config::get('app.debug'))
        return Response::view('errors.500', array(), 500);
});

/*
|--------------------------------------------------------------------------
| Application NotFound Handler
|--------------------------------------------------------------------------
|
*/

App::missing(function($exception)
{
    return Response::view('errors.notFound', array(), 404);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
    return Response::view('maintenance', array (), 503);
});

/*
|--------------------------------------------------------------------------
| Custom Auth Extension
|--------------------------------------------------------------------------
|
*/

Auth::extend('cushion', function()
{
    $model = Config::get('auth.model');
    $provider = new \Illuminate\Auth\EloquentUserProvider(\App::make('hash'), $model);

    return new \App\Services\Auth\SeatGuard($provider, \App::make('session.store'));
});

/*
|--------------------------------------------------------------------------
| Settings in Views
|--------------------------------------------------------------------------
|
| Settings should only be calculated once for views. Unless explicitly
| called, the settings array() may be used to get the required
| Setting
|
*/

View::share('settings', App\Services\Settings\SettingHelper::getAllSettings());

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

/*
|--------------------------------------------------------------------------
| Require The Events File
|--------------------------------------------------------------------------
|
| Next we will load the events file for the application. This gives us
| a nice separate location to store our event information and allows
| for neat declarations of them
|
*/

require app_path().'/events.php';
