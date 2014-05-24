<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterPlanetaryColonies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_planetary_colonies', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->string('characterName');
		  $table->integer('solarSystemID');
		  $table->string('solarSystemName');
		  $table->integer('planetID');
		  $table->string('planetName');
		  $table->integer('planetTypeID');
		  $table->string('planetTypeName');
		  $table->dateTime('lastUpdate');
		  $table->integer('upgradeLevel');
		  $table->integer('numberOfPins');

		  // Indexes
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
		Schema::dropIfExists('character_planetary_colonies');
	}

}
