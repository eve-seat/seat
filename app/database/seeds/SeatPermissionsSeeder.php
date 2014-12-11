<?php

class SeatPermissionsSeeder extends Seeder {

    public function run()
    {
        // Temporarily disable mass assignment restrictions
        Eloquent::unguard();

		SeatPermissions::create(array('permission' => 'pos_manager'));
		SeatPermissions::create(array('permission' => 'wallet_manager'));
		SeatPermissions::create(array('permission' => 'recruiter'));
		SeatPermissions::create(array('permission' => 'asset_manager'));
		SeatPermissions::create(array('permission' => 'contract_manger'));
		SeatPermissions::create(array('permission' => 'market_manager'));
		SeatPermissions::create(array('permission' => 'key_manager'));
    }

}