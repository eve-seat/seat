<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationWalletJournal extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_walletjournal', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('corporationID');

			// Hash for transaction uniqueness as it appears that
			// refID's may cycle...
			$table->string('hash')->unique();

			$table->integer('accountKey');
			$table->bigInteger('refID');
			$table->dateTime('date');
			$table->integer('refTypeID');
			$table->string('ownerName1');
			$table->integer('ownerID1');
			$table->string('ownerName2');
			$table->integer('ownerID2');
			$table->string('argName1');
			$table->integer('argID1');
			$table->decimal('amount', 22,2);
			$table->decimal('balance', 22,2);
			$table->string('reason');
			$table->integer('owner1TypeID');
			$table->integer('owner2TypeID');

			// Indexes
			$table->index('corporationID');
			$table->index('refID');
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
		Schema::drop('corporation_walletjournal');
	}

}
