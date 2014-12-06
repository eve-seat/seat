<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationKillMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_killmails', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');
		  $table->integer('killID');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('killID');

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
		Schema::dropIfExists('corporation_killmails');
	}

}
