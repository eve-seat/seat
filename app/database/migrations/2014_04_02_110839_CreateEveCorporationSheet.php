<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveCorporationSheet extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporation_corporationsheet', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('corporationID');

		  $table->string('corporationName');
		  $table->string('ticker');
		  $table->integer('ceoID');
		  $table->string('ceoName');
		  $table->integer('stationID');
		  $table->string('stationName');
		  $table->text('description');
		  $table->string('url');
		  $table->integer('allianceID');
		  $table->integer('factionID');
		  $table->string('allianceName');
		  $table->decimal('taxRate', 22,2);
		  $table->integer('memberCount');
		  $table->integer('memberLimit');
		  $table->integer('shares');

		  // Really dont see why we need to make another table just for these attribs. 
		  // Soooo, just gonna slap 'em in here.
		  $table->integer('graphicID');
		  $table->integer('shape1');
		  $table->integer('shape2');
		  $table->integer('shape3');
		  $table->integer('color1');
		  $table->integer('color2');
		  $table->integer('color3');

		  // Indexes
		  $table->index('corporationID');
		  $table->index('corporationName');

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
		Schema::dropIfExists('corporation_corporationsheet');
	}

}
