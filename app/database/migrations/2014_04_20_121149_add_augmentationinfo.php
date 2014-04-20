<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAugmentationinfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
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
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
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
	}

}
