<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveMapJumps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('map_jumps', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('solarSystemID')->unique();
		  $table->integer('shipJumps');

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
		Schema::dropIfExists('map_jumps');
	}

}
