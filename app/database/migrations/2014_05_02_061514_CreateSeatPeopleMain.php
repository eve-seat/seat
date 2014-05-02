<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatPeopleMain extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seat_people_main', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('personID');
		  $table->integer('characterID');
		  $table->string('characterName');

		  // Indexes
		  $table->index('personID');

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
		Schema::drop('seat_people_main');
	}

}
