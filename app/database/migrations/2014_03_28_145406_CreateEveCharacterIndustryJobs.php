<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterIndustryJobs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_industryjobs', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('characterID');
		  $table->integer('jobID');
		  $table->integer('assemblyLineID');
		  $table->bigInteger('containerID');
		  $table->bigInteger('installedItemID');
		  $table->bigInteger('installedItemLocationID');
		  $table->integer('installedItemQuantity');
		  $table->integer('installedItemProductivityLevel');
		  $table->integer('installedItemMaterialLevel');
		  $table->integer('installedItemLicensedProductionRunsRemaining');
		  $table->bigInteger('outputLocationID');
		  $table->integer('installerID');
		  $table->integer('runs');
		  $table->integer('licensedProductionRuns');
		  $table->integer('installedInSolarSystemID');
		  $table->integer('containerLocationID');
		  $table->float('materialMultiplier');
		  $table->float('charMaterialMultiplier');
		  $table->float('timeMultiplier');
		  $table->float('charTimeMultiplier');
		  $table->integer('installedItemTypeID');
		  $table->integer('outputTypeID');
		  $table->integer('containerTypeID');
		  $table->boolean('installedItemCopy');
		  $table->boolean('completed');
		  $table->boolean('completedSuccessfully');
		  $table->integer('installedItemFlag');
		  $table->integer('outputFlag');
		  $table->integer('activityID');
		  $table->integer('completedStatus');
		  $table->dateTime('installTime');
		  $table->dateTime('beginProductionTime');
		  $table->dateTime('endProductionTime');
		  $table->dateTime('pauseProductionTime');

		  // Indexes
		  $table->index('characterID');
		  $table->index('jobID');

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
		Schema::dropIfExists('character_industryjobs');
	}

}
