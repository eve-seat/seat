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
use Carthalyst\Sentry as Sentry;

class SeatReset extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the SeAT Web Administrator Password.';

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

        // Print a warning about what this command will do
        $this->error('WARNING!!! This will RESET the current Administrator password!');
        $this->line('');

        // Get the password from the user
        $password = $this->secret('What is the new password to use for the admin user? : ');
        $password2 = $this->secret('Retype that password please: ');

        // Ensure that the user typed the password twice
        // without a problem.
        if ($password <> $password2) {

            $this->error('The passwords do not match. Not resetting');
            return;
        }

        $this->info('The passwords match. Resetting to the new ' . strlen($password) . ' char one.');

        // Attempt to find the admin user usnig Sentry helper functions.
        // If the user does not exist, we create it.
        try {

            $admin = \Sentry::findUserByLogin('admin');

        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {

            \Sentry::register(array(
                'email'     => 'admin',
                'username'  => 'admin',
                'password'  => $password,
            ), true);   // Set the account to be active

            $admin = \Sentry::findUserByLogin('admin');
        }

        // Next, we check for the existance of the admin group and create it if it
        // does not exist
        try {

            $adminGroup = \Sentry::findGroupByName('Administrators');

        } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {

            \Sentry::createGroup(array(
                'name'        => 'Administrators',
                'permissions' => array(
                    'superuser' => 1,
                ),
            ));

            $adminGroup = \Sentry::findGroupByName('Administrators');
        }

        // Set the password and group membership for the admin user.
        $admin->username = 'admin';
        $admin->password = $password;
        $admin->save();
        $admin->addGroup($adminGroup);

        $this->info('Password has been changed successfully.');
    }
}
