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

class SeatUserMigrate extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seat:user-migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate users from Sentry to \Auth';

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
        $this->line('');
        $this->error('WARNING!!! This will OVERWRITE any user accounts created since upgrading to \Auth!');
        $this->line('');
        $this->error('Please note: Any users which have registered but not activated their account will');
        $this->error('need to re-register.');
        $this->line('');

        // Be annoying and ask if the user is sure he wants to continue
        if ($this->confirm('Are you sure you want to migrate all users? [yes|no]', true)) {

            $this->comment('Reading in users...');
            $this->line('');

            //wipe out the tables first
            \DB::table('seat_users')->truncate();
            \DB::table('seat_group_user')->truncate();

            //get the sentry users
            $users = \DB::table('users')->get();

            foreach ($users as $user) {

                //only migrate activated users - the activation keys arent compatible
                if($user->activated == 1) {

                    $this->comment('Copying user '.$user->username.'...');

                    //create new user
                    $new_user = new \User;
                    $new_user->id = $user->id;
                    $new_user->username = $user->username;

                    //check for admin / bogus email
                    if($user->email == 'admin')
                        $new_user->email = 'admin@seat.local';
                    else
                        $new_user->email = $user->email;

                    //copy over user vars
                    $new_user->password = $user->password;
                    $new_user->last_login = $user->last_login;
                    $new_user->created_at = $user->created_at;
                    $new_user->updated_at = $user->updated_at;
                    $new_user->deleted_at = $user->deleted_at;
                    $new_user->activated = 1;
                    $new_user->save();

                    $this->info('User Copied!');

                    $this->comment('Copying permissions for user ' . $user->username . '...');

                    //read in all assignment from pivot
                    $permissions = \DB::table('users_groups')
                        ->where('user_id', '=', $user->id)
                        ->get();

                    foreach ($permissions as $permission) {

                        //get the group for the pivot row
                        $group = \DB::table('groups')
                            ->where('id', '=', $permission->group_id)
                            ->first();

                        //check if this group already exists
                        $new_group = \Auth::findGroupByName($group->name);

                        //if so - add user or create and then add user
                        if($new_group) {

                            \Auth::addUserToGroup($new_user, $new_group);

                        } else {

                            // Sentry stored the permission as JSON, so we will
                            // have to decode that which returns a object,
                            // and cast the result to a array()
                            $created = \Auth::createGroup(
                                array(
                                    'name' => $group->name,
                                    'permissions' => (array)json_decode($group->permissions)
                                )
                            );

                            \Auth::addUserToGroup($new_user, $created);
                        }

                    }

                    $this->info('Permissions Copied!');
                    $this->info('Done user '.$user->username.'!');
                    $this->line('');

                }
            }
        }
    }
}
