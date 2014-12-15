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
            ->with('required_mask', Settings::getSetting('required_mask', true))
            ->with('registration_enabled', Settings::getSetting('registration_enabled', true));
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

        if (Request::ajax()) {

            $validation = new SettingValidator;

            if ($validation->passes()) {

                Settings::setSetting('app_name', Input::get('app_name'));
                Settings::setSetting('color_scheme', Input::get('color_scheme'));
                Settings::setSetting('required_mask', Input::get('required_mask'));
                Settings::setSetting('registration_enabled', Input::get('registration_enabled'));

                return View::make('settings.settings')
                    ->with('app_name', Settings::getSetting('app_name', true))
                    ->with('color_scheme', Settings::getSetting('color_scheme', true))
                    ->with('required_mask', Settings::getSetting('required_mask', true))
                    ->with('registration_enabled', Settings::getSetting('registration_enabled', true));

            } else {

                return View::make('settings.ajax.errors')
                    ->withErrors($validation->errors);
            }
        }
    }
}
