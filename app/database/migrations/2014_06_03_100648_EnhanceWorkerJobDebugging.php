<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EnhanceWorkerJobDebugging extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('queue_information', function($table)
		{
		    $table->dropColumn('output');
		});

		Schema::table('queue_information', function($table)
		{
		    $table->text('output')->default(null)->nullable()->after('status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('queue_information', function($table)
		{
		    $table->dropColumn('output');
		});

		Schema::table('queue_information', function($table)
		{
		    $table->string('output')->default(null)->nullable()->after('status');
		});
	}

}
