<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHashToNotifications extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('seat_notifications', function($table)
		{
		    $table->string('hash')->after('read');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('seat_notifications', function($table)
		{
		    $table->dropColumn('hash');
		});
	}

}
