<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationStarbaseList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_starbaselist', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->bigInteger('itemID')->unique();
		  $table->integer('typeID');
		  $table->bigInteger('locationID');
		  $table->bigInteger('moonID');
		  $table->integer('state');
		  $table->dateTime('stateTimestamp');
		  $table->dateTime('onlineTimestamp');
		  $table->bigInteger('standingOwnerID');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('itemID');

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
		Schema::drop('corporation_starbaselist');
	}

}
