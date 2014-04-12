<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterMailBodies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_mailbodies', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('messageID')->unique();

		  $table->text('body');

		  // Indexes
		  $table->index('messageID');

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
		Schema::dropIfExists('character_mailbodies');
	}
}
