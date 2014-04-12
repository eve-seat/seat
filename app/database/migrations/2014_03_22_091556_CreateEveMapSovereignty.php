<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveMapSovereignty extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('map_sovereignty', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('solarSystemID')->unique();
		  $table->integer('allianceID');
		  $table->integer('factionID');
		  $table->string('solarSystemName');
		  $table->integer('corporationID');

		  // Indexes
		  $table->index('allianceID');
		  $table->index('solarSystemName');

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
		Schema::dropIfExists('map_sovereignty');
	}

}
