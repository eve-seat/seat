<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveServerServerStatus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('server_serverstatus', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->dateTime('currentTime');
		  $table->string('serverOpen');
		  $table->integer('onlinePlayers');

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
		Schema::dropIfExists('server_serverstatus');
	}

}
