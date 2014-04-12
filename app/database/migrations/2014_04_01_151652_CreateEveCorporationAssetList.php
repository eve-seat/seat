<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationAssetList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_assetlist', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->bigInteger('itemID');
		  $table->bigInteger('locationID');
		  $table->bigInteger('typeID');
		  $table->integer('quantity');
		  $table->integer('flag');
		  $table->boolean('singleton');
		  $table->integer('rawQuantity')->default(0);

		  $table->index('corporationID');
		  $table->index('locationID');
		  $table->index('typeID');

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
		Schema::dropIfExists('corporation_assetlist');
	}

}
