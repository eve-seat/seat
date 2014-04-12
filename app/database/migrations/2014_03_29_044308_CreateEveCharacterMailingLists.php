<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterMailingLists extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_mailinglists', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->integer('listID');
		  $table->string('displayName');

		  // Indexes
		  $table->index('characterID');
		  $table->index('listID');

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
		Schema::dropIfExists('character_mailinglists');
	}

}
