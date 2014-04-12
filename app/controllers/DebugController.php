<?php

use App\Services\Validators\APIKeyValidator;
use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class DebugController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| getApi()
	|--------------------------------------------------------------------------
	|
	| Prepare a view with API Related information to post for debugging reasons
	|
	*/

	public function getApi()
	{

		// Find the available API calls
		$call_list = EveApiCalllist::all()->groupBy('name');
		return View::make('debug.api')
			->with('call_list', $call_list);
	}

	/*
	|--------------------------------------------------------------------------
	| postQuery()
	|--------------------------------------------------------------------------
	|
	| Work with the received information to prepare a API call to the server
	| and display its end result
	|
	*/

	public function postQuery()
	{

		// We will validate the key pairs as we dont want to cause unneeded errors
		$validation = new APIKeyValidator;

		if ($validation->passes()) {

			// Bootstrap the Pheal Instance
			BaseApi::bootstrap();
			$pheal = new Pheal(Input::get('keyID'), Input::get('vCode'));
			$pheal->scope = strtolower(Input::get('api'));

			// Prepare an array with the arguements that we have received.
			// first the character
			$arguements = array();
			if (strlen(Input::get('characterid')) > 0)
				$arguements = array('characterid' => Input::get('characterid'));
			// Next, process the option arrays
			if (strlen(Input::get('optional1')) > 0)
				$arguements[Input::get('optional1')] = Input::get('optional1value');
			if (strlen(Input::get('optional2')) > 0)
				$arguements[Input::get('optional2')] = Input::get('optional2value');

			// Compute the array for the view to sample the pheal call that will be made
			$call_sample = array('keyID' => Input::get('keyID'), 'vCode' => Input::get('vCode'), 'scope' => Input::get('api'), 'call' => Input::get('call'), 'args' => $arguements);

			try {

				$method = Input::get('call');
				$response = $pheal->$method($arguements);
				
			} catch (Exception $e) {
				
				return View::make('debug.ajax.result')
					->with('call_sample', $call_sample)
					->with('exception', $e);
			}

			return View::make('debug.ajax.result')
				->with('call_sample', $call_sample)
				->with('response', $response->toArray());

		} else {

			return View::make('debug.ajax.result')
				->withErrors($validation->errors);
		}

	}
}