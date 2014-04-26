<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCorporationContract extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('corporation_contracts', function(Blueprint $table)
			{
				$table->integer('assigneeID')->after('issuerCorpID');
			});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::table('corporation_contracts', function(Blueprint $table)
			{
				$table->dropColumn('assigneeID');
			});
	}

}
