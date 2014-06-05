<?php namespace App\Services\Validators;
 
class SeatUserRegisterValidator extends Validator {
 
    public static $rules = array(
        'email' => 'required|email|unique:users',
        'password'  => 'required|min:6|confirmed',
        'password_confirmation'  => 'required|min:6',
    );
 
}