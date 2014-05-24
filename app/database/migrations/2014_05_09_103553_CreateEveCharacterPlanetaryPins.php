<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterPlanetaryPins extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_planetary_pins', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->bigInteger('pinID');
		  $table->integer('characterID');
		  $table->integer('planetID');

		  $table->integer('typeID');
		  $table->string('typeName');
		  $table->integer('schematicID');
		  $table->dateTime('lastLaunchTime');
		  $table->integer('cycleTime');
		  $table->integer('quantityPerCycle');
		  $table->dateTime('installTime');
		  $table->dateTime('expiryTime');
		  $table->integer('contentTypeID');
		  $table->string('contentTypeName');
		  $table->integer('contentQuantity');
		  $table->double('longitude');
		  $table->double('latitude');

		  // Indexes
		  $table->index('pinID');
		  $table->index('characterID');
		  $table->index('planetID');

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
		Schema::dropIfExists('character_planetary_pins');
	}

}
