<?php namespace App\Services\Validators;
 
class APIKeyValidator extends Validator {
 
    public static $rules = array(
        'keyID' => 'required|numeric',
        'vCode'  => 'required|min:64',
    );
 
}