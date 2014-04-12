<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMarketOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_marketorders', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');
		  $table->bigInteger('orderID');
		  $table->integer('charID');
		  $table->integer('stationID');
		  $table->integer('volEntered');
		  $table->integer('volRemaining');
		  $table->integer('minVolume');
		  $table->integer('orderState');
		  $table->integer('typeID');
		  $table->integer('range');
		  $table->integer('accountKey');
		  $table->integer('duration');
		  $table->decimal('escrow', 22,2);
		  $table->decimal('price', 22,2);
		  $table->integer('bid');
		  $table->dateTime('issued');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('orderID');
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
		Schema::dropIfExists('corporation_marketorders');
	}

}
