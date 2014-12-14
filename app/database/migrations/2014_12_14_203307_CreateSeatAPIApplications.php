<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatAPIApplications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seat_api_applications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('application_name');
			$table->string('application_login');
			$table->string('application_password');
			$table->string('application_ip');
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
		Schema::drop('seat_api_applications');
	}

}
