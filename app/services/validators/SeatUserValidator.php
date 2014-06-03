<?php namespace App\Services\Validators;
 
class SeatUserValidator extends Validator {
 
    public static $rules = array(
        'email' => 'required|email',
        'password'  => 'required|min:6',
        'first_name'  => 'required',
        'last_name'  => 'required',
    );
 
}