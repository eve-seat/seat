<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCharacterAssetListContents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('character_assetlist_contents', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the many to one relationship from class
		  // EveEveCharacterInfo
		  $table->integer('characterID');

		  $table->bigInteger('itemID');
		  $table->bigInteger('typeID');
		  $table->integer('quantity');
		  $table->integer('flag');
		  $table->boolean('singleton');
		  $table->integer('rawQuantity')->default(0);

		  $table->index('characterID');
		  $table->index('typeID');

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
		Schema::dropIfExists('character_assetlist_contents');
	}

}
