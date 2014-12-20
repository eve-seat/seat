<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastLoginDateAndSource extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		// Create the new columns
		Schema::table('seat_users', function(Blueprint $table)
		{
			$table->dateTime('last_login')->after('email');
			$table->string('last_login_source')->after('last_login');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop the new columns
		Schema::table('seat_users', function(Blueprint $table)
		{
			$table->dropColumn('last_login');
			$table->dropColumn('last_login_source');
		});
	}

}
