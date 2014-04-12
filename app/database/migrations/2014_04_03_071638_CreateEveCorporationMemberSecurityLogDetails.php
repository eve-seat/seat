<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMemberSecurityLogDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_msec_log_details', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('hash');
			$table->text('oldRoles');
			$table->text('newRoles');

			// Indexes
			$table->index('hash');

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
		Schema::drop('corporation_msec_log_details');
	}

}
