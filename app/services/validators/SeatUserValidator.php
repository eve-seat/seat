<?php namespace App\Services\Validators;
 
class SeatUserValidator extends Validator {
 
    public static $rules = array(
        'email' => 'required|email',
        'username' => 'required|min:6',
        'password'  => 'required|min:6',
    );
 
}