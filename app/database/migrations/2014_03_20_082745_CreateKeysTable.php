<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seat_keys', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('user_id');
		  $table->integer('keyID')->unsigned();
		  $table->string('vCode', 64);
		  $table->tinyInteger('isOk')->default(1);
		  $table->string('lastError')->nullable();

		  $table->timestamps();
		  $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('seat_keys');
	}

}