<?php

class RegisterController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

	/*
	|--------------------------------------------------------------------------
	| getNew()
	|--------------------------------------------------------------------------
	|
	| Return a view for a new registration
	|
	*/

	public function getNew()
	{
		if (Config::get('seat.allow_registration'))
   		return View::make('register.enabled');
		else
   		return View::make('register.disabled');
	}
}