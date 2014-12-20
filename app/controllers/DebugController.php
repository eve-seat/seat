<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

use App\Services\Validators\APIKeyValidator;
use Seat\EveApi\BaseApi;
use Pheal\Pheal;

class DebugController extends BaseController
{

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
