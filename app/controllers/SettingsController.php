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
            ->with('app_name', SeatSetting::find('app_name')->value)
            ->with('color_scheme', SeatSetting::find('color_scheme')->value)
            ->with('required_mask', SeatSetting::find('required_mask')->value)
            ->with('registration_enabled', SeatSetting::find('registration_enabled')->value);
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

                $app_name = SeatSetting::find('app_name');
                $color_scheme = SeatSetting::find('color_scheme');
                $required_mask = SeatSetting::find('required_mask');
                $registration_enabled = SeatSetting::find('registration_enabled');

                $app_name->value = Input::get('app_name');
                $color_scheme->value = Input::get('color_scheme');
                $required_mask->value = Input::get('required_mask');
                $registration_enabled->value = Input::get('registration_enabled');

                $app_name->save();
                $color_scheme->save();
                $required_mask->save();
                $registration_enabled->save();

                return View::make('settings.settings')
                    ->with('app_name', SeatSetting::find('app_name')->value)
                    ->with('color_scheme', SeatSetting::find('color_scheme')->value)
                    ->with('required_mask', SeatSetting::find('required_mask')->value)
                    ->with('registration_enabled', SeatSetting::find('registration_enabled')->value);

            } else {

                return View::make('settings.ajax.errors')
                    ->withErrors($validation->errors);
            }
        }
    }
}
