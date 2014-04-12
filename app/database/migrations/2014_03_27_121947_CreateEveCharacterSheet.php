<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterSheet extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_charactersheet', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('characterID');

		  $table->string('name');
		  $table->dateTime('DoB');
		  $table->string('race');
		  $table->string('bloodLine');
		  $table->string('ancestry');
		  $table->string('gender');
		  $table->string('corporationName');
		  $table->integer('corporationID');
		  $table->string('cloneName');
		  $table->integer('cloneSkillPoints');
		  $table->decimal('balance', 22,2)->nullable();	// Some rich bastards out there

		  // Really dont see why we need to make another table just for these attribs. 
		  // Soooo, just gonna slap 'em in here.
		  $table->integer('intelligence');
		  $table->integer('memory');
		  $table->integer('charisma');
		  $table->integer('perception');
		  $table->integer('willpower');

		  // Indexes
		  $table->index('characterID');
		  $table->index('name');

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
		Schema::dropIfExists('character_charactersheet');
	}

}
