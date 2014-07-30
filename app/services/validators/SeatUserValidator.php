<?php namespace App\Services\Validators;
 
class SeatUserValidator extends Validator {
 
    public static $rules = array(
        'email' => 'required|email',
        'username' => 'required',
        'password'  => 'required|min:6',
    );
 
}