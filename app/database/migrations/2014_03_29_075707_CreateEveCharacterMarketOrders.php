<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterMarketOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_marketorders', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');
		  $table->bigInteger('orderID');
		  $table->integer('charID');
		  $table->integer('stationID');
		  $table->integer('volEntered');
		  $table->integer('volRemaining');
		  $table->integer('minVolume');
		  $table->integer('orderState');
		  $table->integer('typeID');
		  $table->integer('range');
		  $table->integer('duration');
		  $table->decimal('escrow', 22,2);
		  $table->decimal('price', 22,2);
		  $table->integer('bid');
		  $table->dateTime('issued');

		  // Indexes
		  $table->index('characterID');
		  $table->index('orderID');

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
		Schema::dropIfExists('character_marketorders');
	}

}
