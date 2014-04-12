<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveEveErrorList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('eve_errorlist', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('errorCode')->unique();
		  $table->text('errorText');

		  // Index
		  $table->index('errorCode');

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
		Schema::dropIfExists('eve_errorlist');
	}

}
