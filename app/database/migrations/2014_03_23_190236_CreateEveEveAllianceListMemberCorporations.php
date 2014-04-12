<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveEveAllianceListMemberCorporations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eve_alliancelist_membercorporations', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveAllianceList
		  $table->integer('allianceID');

		  $table->integer('corporationID');
		  $table->dateTime('startDate');

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
		Schema::dropIfExists('eve_alliancelist_membercorporations');
	}
}
