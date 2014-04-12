<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveApiCalllist extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('api_calllist', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->string('type');
		  $table->string('name');
		  $table->integer('accessMask');
		  $table->string('description');

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
		Schema::dropIfExists('api_calllist');
	}

}
