<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Fix5CorporationSheetOptionalAlliance extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::unprepared("ALTER TABLE `corporation_corporationsheet` CHANGE `allianceName` `allianceName` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT ''");
		DB::unprepared("ALTER TABLE `corporation_corporationsheet` CHANGE `allianceID` `allianceID` INT(11)  NULL");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::unprepared("ALTER TABLE `corporation_corporationsheet` CHANGE `allianceName` `allianceName` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NOT NULL  DEFAULT ''");
		DB::unprepared("ALTER TABLE `corporation_corporationsheet` CHANGE `allianceID` `allianceID` INT(11)  NOT NULL");
	}

}
