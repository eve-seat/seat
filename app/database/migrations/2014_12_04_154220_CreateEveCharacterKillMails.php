<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterKillMails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_killmails', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->integer('killID');

		  // Indexes
		  $table->index('characterID');
		  $table->index('killID');

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
		Schema::dropIfExists('character_killmails');
	}
}
