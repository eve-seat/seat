<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterNotifications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_notifications', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->integer('notificationID');

		  $table->integer('typeID');
		  $table->integer('senderID');
		  $table->string('senderName');
		  $table->dateTime('sentDate');
		  $table->integer('read');

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
		Schema::dropIfExists('character_notifications');
	}

}
