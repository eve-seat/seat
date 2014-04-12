<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveEveRefTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eve_reftypes', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('refTypeID')->unique();
		  $table->text('refTypeName');

		  // Index
		  $table->index('refTypeID');

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
		Schema::dropIfExists('eve_reftypes');
	}

}
