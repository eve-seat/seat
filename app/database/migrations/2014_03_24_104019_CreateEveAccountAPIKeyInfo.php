<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveAccountAPIKeyInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_apikeyinfo', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('keyID');
		  $table->integer('accessMask');
		  $table->enum('type', array('Account','Character','Corporation'));
		  $table->dateTime('expires')->nullable();

		  $table->index('keyID');

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
		Schema::dropIfExists('account_apikeyinfo');
	}
}
