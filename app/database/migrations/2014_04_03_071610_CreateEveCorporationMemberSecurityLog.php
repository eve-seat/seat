<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMemberSecurityLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_msec_log', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('corporationID');
			$table->integer('characterID');

			$table->string('characterName');
			$table->dateTime('changeTime');
			$table->integer('issuerID');
			$table->string('issuerName');
			$table->string('roleLocationType');

			$table->string('hash')->unique();

			// Indexes
			$table->index('characterID');
			$table->index('corporationID');
			$table->index('hash');

			$table->timestamps();
		});
	}

	// changeTime,characterID,issuerID,roleLocationType
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('corporation_msec_log');
	}

}
