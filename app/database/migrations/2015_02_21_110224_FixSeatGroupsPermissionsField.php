<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixSeatGroupsPermissionsField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `seat_groups` CHANGE COLUMN `permissions` `permissions` VARCHAR(1500) NOT NULL COLLATE utf8_unicode_ci AFTER `name`;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `seat_groups` CHANGE COLUMN `permissions` `permissions` VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci AFTER `name`;');
	}

}
