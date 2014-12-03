<?php

use App\Services\Validators\SettingValidator;

class SettingsController extends BaseController {

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
		if (Request::ajax()) 
		{
			$validation = new SettingValidator;

			if ($validation->passes()) 
			{
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
			} 
			else 
			{
				return View::make('settings.ajax.errors')
					->withErrors($validation->errors);
			}
		}
	}

}