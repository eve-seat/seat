<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveMapKills extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('map_kills', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('solarSystemID')->unique();
		  $table->integer('shipKills');
		  $table->integer('factionKills');
		  $table->integer('podKills');

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
		Schema::dropIfExists('map_kills');
	}

}
