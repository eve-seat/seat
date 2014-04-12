<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveEveNotificationTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eve_notification_types', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('typeID')->unique();
		  $table->string('description');

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
		Schema::dropIfExists('eve_notification_types');
	}

}
