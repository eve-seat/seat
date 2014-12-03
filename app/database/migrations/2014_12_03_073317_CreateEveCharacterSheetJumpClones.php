<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterSheetJumpClones extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_charactersheet_jumpclones', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterCharacterSheet
		  $table->integer('characterID');

		  $table->integer('jumpCloneID');
		  $table->integer('typeID');
		  $table->bigInteger('locationID');
		  $table->string('cloneName')->nullable();

		  // Indexes
		  $table->index('characterID');
		  $table->index('jumpCloneID');

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
		Schema::dropIfExists('character_charactersheet_jumpclones');
	}
}
