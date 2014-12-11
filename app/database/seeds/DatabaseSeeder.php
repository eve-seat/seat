<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
		$this->call('EveApiCalllistTableSeeder');
        	$this->call('EveNotificationTypesSeeder');
		$this->call('EveCorporationRolemapSeeder');
		$this->call('SeatSettingSeeder');
		$this->call('SeatPermissionsSeeder');
	}
}
