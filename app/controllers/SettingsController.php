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

use App\Services\Validators\SettingValidator;
use App\Services\Validators\SeatApiAppValidator;
use App\Services\Settings\SettingHelper as Settings;

class SettingsController extends BaseController
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
    | getSettings()
    |--------------------------------------------------------------------------
    |
    | Get the current settings state
    |
    */

    public function getSettings()
    {

        return View::make('settings.settings')
            ->with('app_name', Settings::getSetting('app_name', true))
            ->with('color_scheme', Settings::getSetting('color_scheme', true))
            ->with('thousand_seperator', Settings::getSetting('thousand_seperator', true))
            ->with('decimal_seperator', Settings::getSetting('decimal_seperator', true))
            ->with('required_mask', Settings::getSetting('required_mask', true))
            ->with('registration_enabled', Settings::getSetting('registration_enabled', true))
            ->with('seatscheduled_character', Settings::getSetting('seatscheduled_character', true))
            ->with('seatscheduled_corporation', Settings::getSetting('seatscheduled_corporation', true))
            ->with('seatscheduled_corporation_assets', Settings::getSetting('seatscheduled_corporation_assets', true))
            ->with('seatscheduled_corporation_wallets', Settings::getSetting('seatscheduled_corporation_wallets', true))
            ->with('seatscheduled_eve', Settings::getSetting('seatscheduled_eve', true))
            ->with('seatscheduled_map', Settings::getSetting('seatscheduled_map', true))
            ->with('seatscheduled_server', Settings::getSetting('seatscheduled_server', true))
            ->with('seatscheduled_notifications', Settings::getSetting('seatscheduled_notifications', true))
            ->with('seatscheduled_queue_cleanup', Settings::getSetting('seatscheduled_queue_cleanup', true));

    }

    /*
    |--------------------------------------------------------------------------
    | postUpdateSetting()
    |--------------------------------------------------------------------------
    |
    | Updating the settings
    |
    */

    public function postUpdateSetting()
    {

        $validation = new SettingValidator;

        if ($validation->passes()) {

            // Global Settings
            Settings::setSetting('app_name', Input::get('app_name'));
            Settings::setSetting('color_scheme', Input::get('color_scheme'));
            Settings::setSetting('thousand_seperator', Input::get('thousand_seperator'));
            Settings::setSetting('decimal_seperator', Input::get('decimal_seperator'));
            Settings::setSetting('required_mask', Input::get('required_mask'));
            Settings::setSetting('registration_enabled', Input::get('registration_enabled'));

            // SeAT Queues
            Settings::setSetting('seatscheduled_character', Input::get('seatscheduled_character'));
            Settings::setSetting('seatscheduled_corporation', Input::get('seatscheduled_corporation'));
            Settings::setSetting('seatscheduled_corporation_assets', Input::get('seatscheduled_corporation_assets'));
            Settings::setSetting('seatscheduled_corporation_wallets', Input::get('seatscheduled_corporation_wallets'));
            Settings::setSetting('seatscheduled_eve', Input::get('seatscheduled_eve'));
            Settings::setSetting('seatscheduled_map', Input::get('seatscheduled_map'));
            Settings::setSetting('seatscheduled_server', Input::get('seatscheduled_server'));
            Settings::setSetting('seatscheduled_notifications', Input::get('seatscheduled_notifications'));
            Settings::setSetting('seatscheduled_queue_cleanup', Input::get('seatscheduled_queue_cleanup'));

            // Were done. Redirect to the setting page, flashing a message
            return Redirect::action('SettingsController@getSettings')
                ->with('success', 'Settings Successfully Saved!');

        } else {

            return Redirect::back()
                ->withInput()
                ->withErrors($validation->errors);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getApiApplications()
    |--------------------------------------------------------------------------
    |
    | Get the current configured API Applications
    |
    */

    public function getApiApplications()
    {

        return View::make('settings.apiapplications')
            ->with('applications', SeatApiApplication::all());
    }

    /*
    |--------------------------------------------------------------------------
    | postNewApiApplication()
    |--------------------------------------------------------------------------
    |
    | Creates a new API Application
    |
    */

    public function postNewApiApplication()
    {

        $validation = new SeatApiAppValidator;

        if($validation->passes()) {

            // Lets go a quick look to see if this application
            // already exists in the database
            if(SeatApiApplication::where('application_name', Input::get('app_name'))->exists())
                return Redirect::back()
                    ->withInput()
                    ->withErrors('This application name is already in use. Please choose another.');

            // Create a new API Application
            $application = new SeatApiApplication;
            $application->application_name = Input::get('app_name');
            $application->application_ip = Input::get('app_src');
            $application->application_login = preg_replace('/\s+/', '', Input::get('app_name')) . str_random(8);
            $application->application_password = str_random(16);
            $application->save();

            return Redirect::back()
                ->with('success', 'The application has been saved!');

        } else {

            return Redirect::back()
                ->withInput()
                ->withErrors($validation->errors);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | getDeleteApiApplication()
    |--------------------------------------------------------------------------
    |
    | Deletes a SeAT API Application
    |
    */

    public function getDeleteApiApplication($application_id)
    {

        // Lets go a quick look to see if this application
        // already exists in the database
        $application = SeatApiApplication::find($application_id);
        if(!$application)
            App::abort(404);

        // Create a new API Application
        $application->delete();

        return Redirect::back()
            ->with('success', 'The application has been deleted!');

    }

}
