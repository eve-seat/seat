<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterWalletTransactions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_wallettransactions', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('characterID');

		  // Hash for transaction uniqueness as it appears that
		  // refID's may cycle...
		  $table->string('hash')->unique();

		  $table->bigInteger('transactionID');
		  $table->dateTime('transactionDateTime');
		  $table->integer('quantity');
		  $table->string('typeName');
		  $table->integer('typeID');
		  $table->decimal('price', 22,2);
		  $table->integer('clientID');
		  $table->string('clientName');
		  $table->integer('stationID');
		  $table->string('stationName');
		  $table->enum('transactionType', array('buy','sell'));
		  $table->enum('transactionFor', array('personal','corporation'));
		  $table->bigInteger('journalTransactionID');
		  $table->integer('clientTypeID');

		  // Indexes
		  $table->index('characterID');
		  $table->index('transactionID');

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
		Schema::dropIfExists('character_wallettransactions');
	}

}
