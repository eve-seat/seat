<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotificationHashIndexes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('seat_notifications', function($table)
		{

			// Add some indexing
		    $table->index('hash');
		    $table->index('read');
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

			// Drop some indexing
		    $table->dropIndex('seat_notifications_hash_index');
		    $table->dropIndex('seat_notifications_read_index');
		});
	}

}
