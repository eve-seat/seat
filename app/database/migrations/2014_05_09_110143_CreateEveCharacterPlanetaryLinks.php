<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterPlanetaryLinks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_planetary_links', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->integer('planetID');

		  $table->bigInteger('sourcePinID');
		  $table->bigInteger('destinationPinID');
		  $table->integer('linkLevel');

		  // Indexes
		  $table->index('sourcePinID');
		  $table->index('destinationPinID');
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
		Schema::dropIfExists('character_planetary_links');
	}

}
