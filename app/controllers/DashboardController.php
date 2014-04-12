<?php

class DashboardController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }

	public function getDashboard()
	{
		return View::make('home');
	}
}