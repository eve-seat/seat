<?php namespace App\Services\Validators;
 
class SeatGroupValidator extends Validator {
 
    public static $rules = array(
        'groupName' => 'required',
    );
 
}