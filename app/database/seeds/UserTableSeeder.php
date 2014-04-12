<?php

class UserTableSeeder extends Seeder {

    public function run()
    {
		DB::table('seat_users')->delete();

		User::create(array(
		  'email' => 'foo@bar.com',
		  'username' => 'admin',
		  'password' => Hash::make('seat!admin')
		));
    }

}