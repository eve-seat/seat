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
		return View::make('register.register');
	}
}