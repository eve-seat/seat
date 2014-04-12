<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterNotificationTexts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_notification_texts', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('notificationID');
		  $table->text('text');

		  // Indexes
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
		Schema::dropIfExists('character_notification_texts');
	}

}
