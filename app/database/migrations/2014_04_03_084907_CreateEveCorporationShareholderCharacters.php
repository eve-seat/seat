<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationShareholderCharacters extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_shareholder_characters', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('corporationID');
			$table->integer('shareholderID');
			$table->string('shareholderName');
			$table->integer('shareholderCorporationID');
			$table->string('shareholderCorporationName');
			$table->integer('shares');

			// Indexes
			$table->index('shareholderID');
			$table->index('corporationID');

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
		Schema::drop('corporation_shareholder_characters');
	}

}
