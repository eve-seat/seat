<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationSheetWalletDivisions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_corporationsheet_walletdivisions', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->integer('accountKey');
		  $table->string('description');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('accountKey');

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
		Schema::dropIfExists('corporation_corporationsheet_walletdivisions');
	}

}
