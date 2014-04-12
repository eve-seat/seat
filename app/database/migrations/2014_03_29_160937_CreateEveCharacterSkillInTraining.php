<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterSkillInTraining extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_skillintraining', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->dateTime('currentTQTime')->nullable();
		  $table->dateTime('trainingEndTime')->nullable();
		  $table->dateTime('trainingStartTime')->nullable();
		  $table->integer('trainingTypeID')->nullable();
		  $table->integer('trainingStartSP')->nullable();
		  $table->integer('trainingDestinationSP')->nullable();
		  $table->integer('trainingToLevel')->nullable();
		  $table->integer('skillInTraining');

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
		Schema::dropIfExists('character_skillintraining');
	}

}
