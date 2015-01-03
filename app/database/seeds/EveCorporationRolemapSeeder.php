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

/* see http://oldforums.eveonline.com/?a=topic&threadID=1250101 */

class EveCorporationRolemapSeeder extends Seeder
{

    public function run()
    {
		DB::table('eve_corporation_rolemap')->delete();

        DB::table('eve_corporation_rolemap')->insert(array(

            array('roleID' => 1,                  'roleName' => 'Director'),
            array('roleID' => 128,                'roleName' => 'Personnel Manager'),
            array('roleID' => 256,                'roleName' => 'Accountant'),
            array('roleID' => 512,                'roleName' => 'Security Officer'),
            array('roleID' => 1024,               'roleName' => 'Factory Manager'),
            array('roleID' => 2048,               'roleName' => 'Station Manager'),
            array('roleID' => 4096,               'roleName' => 'Auditor'),
            array('roleID' => 134217728,          'roleName' => 'Can take from Wallet Division 1'),
            array('roleID' => 268435456,          'roleName' => 'Can take from Wallet Division 2'),
            array('roleID' => 536870912,          'roleName' => 'Can take from Wallet Division 3'),
            array('roleID' => 1073741824,         'roleName' => 'Can take from Wallet Division 4'),
            array('roleID' => 2147483648,         'roleName' => 'Can take from Wallet Division 5'),
            array('roleID' => 4294967296,         'roleName' => 'Can take from Wallet Division 6'),
            array('roleID' => 8589934592,         'roleName' => 'Can take from Wallet Division 7'),
            array('roleID' => 2199023255552,      'roleName' => 'Equipment Config'),
            array('roleID' => 562949953421312,    'roleName' => 'Can Rent Office'),
            array('roleID' => 1125899906842624,   'roleName' => 'Can Rent Factory Slot'),
            array('roleID' => 2251799813685248,   'roleName' => 'Can Rent Research Slot'),
            array('roleID' => 4503599627370496,   'roleName' => 'Junior Accountant'),
            array('roleID' => 9007199254740992,   'roleName' => 'Starbase Config'),
            array('roleID' => 18014398509481984,  'roleName' => 'Trader'),
            array('roleID' => 36028797018963968,  'roleName' => 'Chat Manager'),
            array('roleID' => 72057594037927936,  'roleName' => 'Contract Manager'),
            array('roleID' => 144115188075855872, 'roleName' => 'Infrastructure Tactical Officer'),
            array('roleID' => 288230376151711744, 'roleName' => 'Starbase Caretaker'),
            array('roleID' => 576460752303423488, 'roleName' => 'Fitting Manager'),
            array('roleID' => 8192,               'roleName' => 'Can take from Hangar 1'),
            array('roleID' => 16384,              'roleName' => 'Can take from Hangar 2'),
            array('roleID' => 32768,              'roleName' => 'Can take from Hangar 3'),
            array('roleID' => 65536,              'roleName' => 'Can take from Hangar 4'),
            array('roleID' => 131072,             'roleName' => 'Can take from Hangar 5'),
            array('roleID' => 262144,             'roleName' => 'Can take from Hangar 6'),
            array('roleID' => 524288,             'roleName' => 'Can take from Hangar 7'),
            array('roleID' => 1048576,            'roleName' => 'Can query Hangar 1'),
            array('roleID' => 2097152,            'roleName' => 'Can query Hangar 2'),
            array('roleID' => 4194304,            'roleName' => 'Can query Hangar 3'),
            array('roleID' => 8388608,            'roleName' => 'Can query Hangar 4'),
            array('roleID' => 16777216,           'roleName' => 'Can query Hangar 5'),
            array('roleID' => 33554432,           'roleName' => 'Can query Hangar 6'),
            array('roleID' => 67108864,           'roleName' => 'Can query Hangar 7'),
            array('roleID' => 4398046511104,      'roleName' => 'Can take Container from Hangar 1'),
            array('roleID' => 8796093022208,      'roleName' => 'Can take Container from Hangar 2'),
            array('roleID' => 17592186044416,     'roleName' => 'Can take Container from Hangar 3'),
            array('roleID' => 35184372088832,     'roleName' => 'Can take Container from Hangar 4'),
            array('roleID' => 70368744177664,     'roleName' => 'Can take Container from Hangar 5'),
            array('roleID' => 140737488355328,    'roleName' => 'Can take Container from Hangar 6'),
            array('roleID' => 281474976710656,    'roleName' => 'Can take Container from Hangar 7'),
        ));
    }
}
