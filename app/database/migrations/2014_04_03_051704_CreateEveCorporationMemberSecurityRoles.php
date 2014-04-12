<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMemberSecurityRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_msec_roles', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('characterID');
			$table->integer('corporationID');
			$table->string('name');

			$table->bigInteger('roleID');
			$table->string('roleName');

			// Indexes
			$table->index('characterID');
			$table->index('corporationID');
			$table->index('roleID');

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
		Schema::drop('corporation_msec_roles');
	}

}
