<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationAssetListLocationsAddMapData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('corporation_assetlist_locations', function($table)
		{
		    $table->integer('mapID')->nullable()->default(null);
		    $table->string('mapName')->nullable()->default(null);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('corporation_assetlist_locations', function($table)
		{
		    $table->dropColumn('mapID');
		    $table->dropColumn('mapName');
		});
	}

}
