<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterUpcomingCalendarEvents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_upcomingcalendarevents', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');

		  $table->bigInteger('eventID');
		  $table->bigInteger('ownerID');
		  $table->string('ownerName')->nullable();
		  $table->dateTime('eventDate');
		  $table->string('eventTitle');
		  $table->integer('duration');
		  $table->boolean('importance');
		  $table->enum('response', array('Undecided','Accepted','Declined', 'Tentative'));
		  $table->text('eventText');
		  $table->integer('ownerTypeID');

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
		Schema::dropIfExists('character_upcomingcalendarevents');
	}

}
