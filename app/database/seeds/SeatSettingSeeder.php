<?php

class SeatSettingSeeder extends Seeder {

    public function run()
    {
		SeatSetting::create(array('setting' => 'app_name', 'value' => 'SeAT'));
		SeatSetting::create(array('setting' => 'color_scheme', 'value' => 'blue'));
		SeatSetting::create(array('setting' => 'required_mask', 'value' => '176693568'));
		SeatSetting::create(array('setting' => 'registration_enabled', 'value' => 'true'));
    }

}