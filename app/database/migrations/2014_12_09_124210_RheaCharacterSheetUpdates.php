<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RheaCharacterSheetUpdates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Drop the old clone colums
		Schema::table('character_charactersheet', function(Blueprint $table)
		{
			$table->dropColumn('cloneTypeID');
			$table->dropColumn('cloneName');
			$table->dropColumn('cloneSkillPoints');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Restore the old augmentationColumns
		Schema::table('character_charactersheet', function(Blueprint $table)
		{
			$table->string('cloneTypeID')->nullable();
			$table->integer('cloneName')->nullable();
			$table->string('cloneSkillPoints')->nullable();
		});
	}

}
