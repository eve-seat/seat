<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CruisCorporationIndustryApiChanges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		// Clean up the table by removing all of the existing data
		DB::table('corporation_industryjobs')->truncate();

		// Remove Old Colums
		Schema::table('corporation_industryjobs', function(Blueprint $table)
		{
			$table->dropColumn('assemblyLineID');
			$table->dropColumn('containerID');
			$table->dropColumn('installedItemID');
			$table->dropColumn('installedItemLocationID');
			$table->dropColumn('installedItemQuantity');
			$table->dropColumn('installedItemProductivityLevel');
			$table->dropColumn('installedItemMaterialLevel');
			$table->dropColumn('installedItemLicensedProductionRunsRemaining');
			$table->dropColumn('licensedProductionRuns');
			$table->dropColumn('installedInSolarSystemID');
			$table->dropColumn('containerLocationID');
			$table->dropColumn('materialMultiplier');
			$table->dropColumn('charMaterialMultiplier');
			$table->dropColumn('timeMultiplier');
			$table->dropColumn('charTimeMultiplier');
			$table->dropColumn('installedItemTypeID');
			$table->dropColumn('outputTypeID');
			$table->dropColumn('containerTypeID');
			$table->dropColumn('installedItemCopy');
			$table->dropColumn('completed');
			$table->dropColumn('completedSuccessfully');
			$table->dropColumn('installedItemFlag');
			$table->dropColumn('outputFlag');
			$table->dropColumn('completedStatus');
			$table->dropColumn('installTime');
			$table->dropColumn('beginProductionTime');
			$table->dropColumn('endProductionTime');
			$table->dropColumn('pauseProductionTime');
		});

		// Add new ones
		Schema::table('corporation_industryjobs', function(Blueprint $table)
		{
			$table->string('installerName');
			$table->bigInteger('facilityID');
			$table->integer('solarSystemID');
			$table->string('solarSystemName');
			$table->bigInteger('stationID');
			$table->bigInteger('blueprintID');
			$table->integer('blueprintTypeID');
			$table->string('blueprintTypeName');
			$table->bigInteger('blueprintLocationID');
			$table->decimal('cost', 22,2);
			$table->integer('teamID');
			$table->integer('licensedRuns');
			$table->integer('probability');
			$table->integer('productTypeID');
			$table->string('productTypeName');
			$table->integer('status');
			$table->integer('timeInSeconds');
			$table->dateTime('startDate');
			$table->dateTime('endDate');
			$table->dateTime('pauseDate');
			$table->dateTime('completedDate');
			$table->integer('completedCharacterID');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		// Remove the new columns
		Schema::table('corporation_industryjobs', function(Blueprint $table)
		{
			$table->dropColumn('installerName');
			$table->dropColumn('facilityID');
			$table->dropColumn('solarSystemID');
			$table->dropColumn('solarSystemName');
			$table->dropColumn('stationID');
			$table->dropColumn('blueprintID');
			$table->dropColumn('blueprintTypeID');
			$table->dropColumn('blueprintTypeName');
			$table->dropColumn('blueprintLocationID');
			$table->dropColumn('cost');
			$table->dropColumn('teamID');
			$table->dropColumn('licensedRuns');
			$table->dropColumn('probability');
			$table->dropColumn('productTypeID');
			$table->dropColumn('productTypeName');
			$table->dropColumn('status');
			$table->dropColumn('timeInSeconds');
			$table->dropColumn('startDate');
			$table->dropColumn('endDate');
			$table->dropColumn('pauseDate');
			$table->dropColumn('completedDate');
			$table->dropColumn('completedCharacterID');
		});

		// Add the old Colums
		Schema::table('corporation_industryjobs', function(Blueprint $table)
		{

			$table->integer('assemblyLineID');
			$table->bigInteger('containerID');
			$table->bigInteger('installedItemID');
			$table->bigInteger('installedItemLocationID');
			$table->integer('installedItemQuantity');
			$table->integer('installedItemProductivityLevel');
			$table->integer('installedItemMaterialLevel');
			$table->integer('installedItemLicensedProductionRunsRemaining');
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
			$table->integer('completedStatus');
			$table->dateTime('installTime');
			$table->dateTime('beginProductionTime');
			$table->dateTime('endProductionTime');
			$table->dateTime('pauseProductionTime');
		});
	}
}
