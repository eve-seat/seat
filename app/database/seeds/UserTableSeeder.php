<?php

class UserTableSeeder extends Seeder {

    public function run()
    {
    	
  		$this->command->info('Be sure to run this to set the default admin password: php artisan seat:reset');
    }

}