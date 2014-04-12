<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMemberTracking extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_member_tracking', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('corporationID');
			$table->integer('characterID');
			$table->string('name');
			$table->dateTime('startDateTime');
			$table->bigInteger('baseID');
			$table->string('base');
			$table->string('title');
			$table->dateTime('logonDateTime');
			$table->dateTime('logoffDateTime');
			$table->integer('locationID');
			$table->string('location');
			$table->integer('shipTypeID');
			$table->string('shipType');
			$table->string('roles');
			$table->string('grantableRoles');

			// Indexes
			$table->index('characterID');
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
		Schema::drop('corporation_member_tracking');
	}

}
