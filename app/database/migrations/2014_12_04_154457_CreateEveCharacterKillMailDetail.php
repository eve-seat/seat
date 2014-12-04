<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterKillMailDetail extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_killmail_detail', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('killID');
		  $table->integer('solarSystemID');
		  $table->dateTime('killTime');
		  $table->integer('moonID');

		  // Victim Information
		  $table->integer('characterID');
		  $table->string('characterName');
		  $table->integer('corporationID');
		  $table->string('corporationName');
		  $table->integer('allianceID')->nullable();
		  $table->string('allianceName')->nullable();
		  $table->integer('factionID')->nullable();
		  $table->string('factionName')->nullable();
		  $table->integer('damageTaken');
		  $table->integer('shipTypeID');

		  // Indexes
		  $table->index('killID');
		  $table->index('characterID');

		  $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('character_killmail_detail');
	}
}
