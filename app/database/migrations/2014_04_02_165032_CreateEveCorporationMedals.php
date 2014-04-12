<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMedals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_medals', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');
		  $table->integer('medalID');
		  $table->string('title');
		  $table->text('description');
		  $table->integer('creatorID');
		  $table->dateTime('created');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('medalID');

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
		Schema::dropIfExists('corporation_medals');
	}
}
