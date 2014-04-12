<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterAccountBalance extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_accountbalance', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('characterID');

		  $table->integer('accountID');
		  $table->integer('accountKey');
		  $table->decimal('balance', 22,2)->nullable();	// Some rich bastards out there

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
		Schema::dropIfExists('character_accountbalance');
	}

}
