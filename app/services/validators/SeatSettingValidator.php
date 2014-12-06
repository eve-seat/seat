<?php namespace App\Services\Validators;
 
class SettingValidator extends Validator {
 
    public static $rules = array(
        'app_name' => 'required',
        'registration_enabled'  => 'required',
        'required_mask'  => 'required|numeric|min:176693568',
        'color_scheme'  => 'required',
    );
 
}