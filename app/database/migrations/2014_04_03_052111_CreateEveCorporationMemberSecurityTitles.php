<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationMemberSecurityTitles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_msec_titles', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('characterID');
			$table->integer('corporationID');
			$table->string('name');

			$table->bigInteger('titleID');
			$table->string('titleName');

			// Indexes
			$table->index('characterID');
			$table->index('corporationID');
			$table->index('titleID');

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
		Schema::drop('corporation_msec_titles');
	}

}
