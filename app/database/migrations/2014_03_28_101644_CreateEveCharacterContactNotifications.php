<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterContactNotifications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_contactnotifications', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('characterID');

		  $table->integer('notificationID');
		  $table->integer('senderID');
		  $table->string('senderName');
		  $table->dateTime('sentDate');
		  $table->string('messageData');

		  // Indexes
		  $table->index('characterID');
		  $table->index('notificationID');

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
		Schema::dropIfExists('character_contactnotifications');
	}
}
