<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationStandingsFactions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_standings_factions', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->integer('fromID');
		  $table->string('fromName');
		  $table->float('standing');

		  // Indexes
		  $table->index('corporationID');

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
		Schema::drop('corporation_standings_factions');
	}

}
