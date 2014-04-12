<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationContractsItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_contracts_items', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('corporationID');
		  $table->integer('contractID');

		  $table->integer('recordID');
		  $table->integer('typeID');
		  $table->integer('quantity');
		  $table->integer('rawQuantity')->nullable();
		  $table->integer('singleton');
		  $table->string('included');

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
		Schema::dropIfExists('corporation_contracts_items');
	}

}
