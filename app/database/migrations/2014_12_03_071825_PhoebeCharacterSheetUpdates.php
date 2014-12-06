<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PhoebeCharacterSheetUpdates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		// Drop the old attributeEnhancers columns
		Schema::table('character_charactersheet', function(Blueprint $table)
		{
			$table->dropColumn('intelligenceAugmentatorName');
			$table->dropColumn('intelligenceAugmentatorValue');
			$table->dropColumn('memoryAugmentatorName');
			$table->dropColumn('memoryAugmentatorValue');
			$table->dropColumn('charismaAugmentatorName');
			$table->dropColumn('charismaAugmentatorValue');
			$table->dropColumn('perceptionAugmentatorName');
			$table->dropColumn('perceptionAugmentatorValue');
			$table->dropColumn('willpowerAugmentatorName');
			$table->dropColumn('willpowerAugmentatorValue');
		});

		// Create the new columns
		Schema::table('character_charactersheet', function(Blueprint $table)
		{
			$table->integer('homeStationID')->after('willpower');
			$table->string('factionName')->after('willpower')->nullable();
			$table->integer('factionID')->after('willpower')->nullable();
			$table->integer('cloneTypeID')->after('willpower');
			$table->integer('freeRespecs')->after('willpower');
			$table->dateTime('cloneJumpDate')->after('willpower');
			$table->dateTime('lastRespecDate')->after('willpower');
			$table->dateTime('lastTimedRespec')->after('willpower');
			$table->dateTime('remoteStationDate')->after('willpower');
			$table->dateTime('jumpActivation')->after('willpower');
			$table->dateTime('jumpFatigue')->after('willpower');
			$table->dateTime('jumpLastUpdate')->after('willpower');
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
			$table->string('intelligenceAugmentatorName')->nullable();
			$table->integer('intelligenceAugmentatorValue')->nullable();
			$table->string('memoryAugmentatorName')->nullable();
			$table->integer('memoryAugmentatorValue')->nullable();
			$table->string('charismaAugmentatorName')->nullable();
			$table->integer('charismaAugmentatorValue')->nullable();
			$table->string('perceptionAugmentatorName')->nullable();
			$table->integer('perceptionAugmentatorValue')->nullable();
			$table->string('willpowerAugmentatorName')->nullable();
			$table->integer('willpowerAugmentatorValue')->nullable();
		});

		// Drop the new columns
		// Drop the old attributeEnhancers columns
		Schema::table('character_charactersheet', function(Blueprint $table)
		{
			$table->dropColumn('homeStationID');
			$table->dropColumn('factionName');
			$table->dropColumn('factionID');
			$table->dropColumn('cloneTypeID');
			$table->dropColumn('freeRespecs');
			$table->dropColumn('cloneJumpDate');
			$table->dropColumn('lastRespecDate');
			$table->dropColumn('lastTimedRespec');
			$table->dropColumn('remoteStationDate');
			$table->dropColumn('jumpActivation');
			$table->dropColumn('jumpFatigue');
			$table->dropColumn('jumpLastUpdate');
		});
	}
}
