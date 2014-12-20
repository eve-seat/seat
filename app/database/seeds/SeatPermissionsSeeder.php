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

class SeatPermissionsSeeder extends Seeder
{

    public function run()
    {
        // Temporarily disable mass assignment restrictions
        Eloquent::unguard();

        DB::table('seat_permissions')->truncate();

        SeatPermissions::create(array('permission' => 'pos_manager'));
        SeatPermissions::create(array('permission' => 'wallet_manager'));
        SeatPermissions::create(array('permission' => 'recruiter'));
        SeatPermissions::create(array('permission' => 'asset_manager'));
        SeatPermissions::create(array('permission' => 'contract_manger'));
        SeatPermissions::create(array('permission' => 'market_manager'));
        SeatPermissions::create(array('permission' => 'key_manager'));
        SeatPermissions::create(array('permission' => 'queue_manager'));
    }
}
