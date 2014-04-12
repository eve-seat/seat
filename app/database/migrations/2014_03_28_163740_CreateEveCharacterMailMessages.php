<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterMailMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_mailmessages', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('characterID');
		  $table->integer('messageID');

		  $table->integer('senderID');
		  $table->string('senderName');
		  $table->dateTime('sentDate');
		  $table->string('title');
		  $table->integer('toCorpOrAllianceID')->nullable();
		  $table->text('toCharacterIDs')->nullable();
		  $table->integer('toListID')->nullable();

		  // Indexes
		  $table->index('characterID');
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
		Schema::dropIfExists('character_mailmessages');
	}

}
