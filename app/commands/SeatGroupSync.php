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

namespace Seat\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SeatGroupSync extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:groupsync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensures the application has all the SeAT access groups present.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        // We will search for groups and create them if they are not present.
        // Lets create an array that we can easily append to in case we need
        // more groups
        $groups = array(

            // This group is for POS Managers to allow access to the POS
            // Management View
            array(
                'name' => 'POS Managers',
                'permissions' => array(
                    'pos_manager' => 1
                )
            ),

            // Wallet Manager group is to allow access to corporation wallets
            // and Ledgers
            array(
                'name' => 'Wallet Managers',
                'permissions' => array(
                    'wallet_manager' => 1
                )
            ),

            // Recruiters group allows access to all character sheets and keys
            array(
                'name' => 'Recruiters',
                'permissions' => array(
                    'recruiter' => 1
                )
            ),

            // Asset Manager group is to allow access to Posses and Assets
            array(
                'name' => 'Asset Managers',
                'permissions' => array(
                    'pos_manager' => 1,
                    'asset_manager' => 1
                )
            ),

            // Contract Manager group is to allow access to Posses and Assets
            array(
                'name' => 'Contract Managers',
                'permissions' => array(
                    'contract_manager' => 1
                )
            ),

            // Market Manager group is to allow access to Market Orders and Assets
            array(
                'name' => 'Market Managers',
                'permissions' => array(
                    'market_manager' => 1
                )
            ),

            // Key Managers Are Allowed to delete API Keys
            array(
                'name' => 'Key Manager',
                'permissions' => array(
                    'key_manager' => 1
                )
            )
        );

        // Loop over $groups and check || create as needed
        foreach ($groups as $group_entry) {

            $group = \Auth::findGroupByName($group_entry['name']);

            if(!$group) {

                $this->info('[info] Group ' . $group_entry['name'] . ' was not found. Creating it.');

                $group = \Auth::createGroup(array(
                        'name' => $group_entry['name'],
                        'permissions' => $group_entry['permissions']
                    ));
                $this->line('[ok] Group ' . $group_entry['name'] . ' created.');
            }
        }
    }
}
