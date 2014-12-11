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

class HelpController extends BaseController
{

    /*
    |--------------------------------------------------------------------------
    | Help Controller
    |--------------------------------------------------------------------------
    |
    */

    public function getHelp()
    {

        // Get the current version
        $current_version = \Config::get('seat.version');

        // Try determine how far back we are on releases
        $versions_behind = 0;

        try {

            // Check the releases from Github for eve-seat/seat
            $headers = array('Accept' => 'application/json');
            $request = Requests::get('https://api.github.com/repos/eve-seat/seat/releases', $headers);

            if ($request->status_code == 200) {

                $release_data = json_decode($request->body);

                // Try and determine if we are up to date
                if ($release_data[0]->tag_name == 'v'.$current_version) {

                } else {

                    foreach ($release_data as $release) {

                        if ($release->tag_name == 'v'.$current_version)
                            break;
                        else
                            $versions_behind++;
                    }
                }

            } else {

                $release_data = null;
            }

        } catch (Exception $e) {

            $release_data = null;
        }

        return View::make('help.help')
            ->with('release_data', $release_data)
            ->with('versions_behind', $versions_behind);
    }
}
