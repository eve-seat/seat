<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterKillMailAttackers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_killmail_attackers', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('killID');

		  // Attacker Information
		  $table->integer('characterID');
		  $table->string('characterName');
		  $table->integer('corporationID');
		  $table->string('corporationName');
		  $table->integer('allianceID')->nullable();
		  $table->string('allianceName')->nullable();
		  $table->integer('factionID')->nullable();
		  $table->string('factionName')->nullable();
		  $table->float('securityStatus');
		  $table->integer('damageDone');
		  $table->integer('finalBlow');
		  $table->integer('weaponTypeID');
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
		Schema::dropIfExists('character_killmail_attackers');
	}

}
