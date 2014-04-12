<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationAssetListLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_assetlist_locations', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->bigInteger('itemID');
		  $table->string('itemName');
		  $table->double('x');
		  $table->double('y');
		  $table->double('z');

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
		Schema::dropIfExists('corporation_assetlist_locations');
	}

}
