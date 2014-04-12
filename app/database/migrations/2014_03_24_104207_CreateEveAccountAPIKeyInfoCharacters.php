<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveAccountAPIKeyInfoCharacters extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_apikeyinfo_characters', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveAccountAPIKeyInfo
		  $table->integer('keyID');

		  $table->integer('characterID');
		  $table->string('characterName');
		  $table->integer('corporationID');
		  $table->string('corporationName');

		  $table->index('characterID');
		  $table->index('characterName');

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
		Schema::dropIfExists('account_apikeyinfo_characters');
	}
}
