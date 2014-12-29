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

use App\Services\Validators\SeatApiKeyValidator;

class KeyController extends BaseApiController
{

    /*
    |--------------------------------------------------------------------------
    | index()
    |--------------------------------------------------------------------------
    |
    | GET
    | Return all the keyID's and vCodes in SeAT
    |
    */

    public function index()
    {

        return Response::json(
            array(
                'error' => false,
                'keys' => SeatKey::all()->toArray()
            ),
            200
        );
    }

    /*
    |--------------------------------------------------------------------------
    | store()
    |--------------------------------------------------------------------------
    |
    | POST
    | Add's a new API key to SeAT
    | Call: /api/v1/key | Data: keyID=%&vCode=%
    |
    */

    public function store()
    {

        $validation = new SeatApiKeyValidator;

        if ($validation->passes()) {

            // Check if we already know about this keyID
            $key_data = SeatKey::withTrashed()
                ->where('keyID', Input::get('keyID'))
                ->first();

            if (!$key_data)
                $key_data = new SeatKey;

            $key_data->keyID = Input::get('keyID');
            $key_data->vCode = Input::get('vCode');
            $key_data->isOk = 1;
            $key_data->lastError = null;
            $key_data->deleted_at = null;
            $key_data->user_id = 1;
            $key_data->save();

            return Response::json(
                array(
                    'error' => false,
                    'status' => 'Key has been added/updated.',
                ),
                200
            );

        } else {

            // if validation fails, respond
            return Response::json(
                array(
                    'error' => true,
                    'message' => $validation->errors->toArray()
                ),
                412
            );
        }

    }

    /*
    |--------------------------------------------------------------------------
    | update()
    |--------------------------------------------------------------------------
    |
    | PUT
    | Just errors
    |
    */

    public function update()
    {

        return Response::json(
            array(
                'error' => true,
                'message' => 'Update keys by sending the details in a POST.'
            ),
            405
        );
    }

    /*
    |--------------------------------------------------------------------------
    | destroy()
    |--------------------------------------------------------------------------
    |
    | DELETE
    | Deletes a API key from SeAT
    |
    */

    public function destroy($id)
    {

        $deleted_rows = SeatKey::where('keyID', $id)->delete();

        if($deleted_rows > 0)
            return Response::json(
                array(
                    'error' => false,
                    'message' => 'Key ' . $id . ' has been deleted.'
                ),
                200
            );

        return Response::json(
            array(
                'error' => true,
                'message' => 'Unknown key.'
            ),
            400
        );

    }

}
