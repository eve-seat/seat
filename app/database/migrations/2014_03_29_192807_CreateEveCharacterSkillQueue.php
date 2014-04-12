<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterSkillQueue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_skillqueue', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->integer('queuePosition');
		  $table->integer('typeID');
		  $table->integer('level');
		  $table->integer('startSP');
		  $table->integer('endSP');
		  $table->dateTime('startTime')->nullable(); // If current queue is paused this will be null
		  $table->dateTime('endTime')->nullable(); // If current queue is paused this will be null

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
		Schema::dropIfExists('character_skillqueue');
	}

}
