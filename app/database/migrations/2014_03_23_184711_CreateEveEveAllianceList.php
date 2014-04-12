<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveEveAllianceList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eve_alliancelist', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->string('name');
		  $table->string('shortName');
		  $table->integer('allianceID');
		  $table->integer('executorCorpID');
		  $table->integer('memberCount');
		  $table->dateTime('startDate');

		  // Indexes
		  $table->index('allianceID');

		  $table->timestamps();
		});
	}

	// name,shortName,allianceID,executorCorpID,memberCount,startDate

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('eve_alliancelist');
	}

}
