<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChartersToStarbaseDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('corporation_starbasedetail', function(Blueprint $table)
		{
			$table->integer('starbaseCharter')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('corporation_starbasedetail', function(Blueprint $table)
		{
			$table->dropColumn('starbaseCharter')->nullable();
		});
	}

}
