<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationContracts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_contracts', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->integer('contractID');
		  $table->integer('issuerID');
		  $table->integer('issuerCorpID');
		  $table->integer('acceptorID');
		  $table->integer('startStationID');
		  $table->integer('endStationID');
		  $table->string('type');
		  $table->string('status');
		  $table->string('title')->nullable();
		  $table->integer('forCorp');
		  $table->string('availability');
		  $table->dateTime('dateIssued');
		  $table->dateTime('dateExpired')->nullable();
		  $table->dateTime('dateAccepted')->nullable();
		  $table->integer('numDays');
		  $table->dateTime('dateCompleted')->nullable();
		  $table->decimal('price', 22,2);
		  $table->decimal('reward', 22,2);
		  $table->decimal('collateral', 22,2);
		  $table->decimal('buyout', 22,2);
		  $table->integer('volume');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('contractID');

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
		Schema::dropIfExists('corporation_contracts');
	}

}
