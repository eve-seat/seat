<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationCorporateContactList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_contactlist_corporate', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->integer('contactID');
		  $table->string('contactName');
		  $table->integer('standing');
		  $table->integer('contactTypeID');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('contactID');

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
		Schema::dropIfExists('corporation_contactlist_corporate');
	}

}
