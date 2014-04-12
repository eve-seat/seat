<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationAccountBalance extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_accountbalance', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->bigInteger('corporationID');

		  $table->integer('accountID');
		  $table->integer('accountKey');
		  $table->decimal('balance', 22,2);

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
		Schema::dropIfExists('corporation_accountbalance');
	}

}
