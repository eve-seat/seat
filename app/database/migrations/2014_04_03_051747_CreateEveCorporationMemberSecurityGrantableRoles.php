<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMemberSecurityGrantableRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_msec_grantable_roles', function(Blueprint $table)
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
		Schema::drop('corporation_msec_grantable_roles');
	}

}
