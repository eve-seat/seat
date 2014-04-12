<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEveAccountAccountStatus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_accountstatus', function(Blueprint $table)
		{
		  $table->increments('id');

		  // Id for the one to one relationship from class
		  // EveAccountAPIKeyInfo
		  $table->integer('keyID');

		  $table->dateTime('paidUntil');
		  $table->dateTime('createDate');
		  $table->integer('logonCount');
		  $table->integer('logonMinutes');

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
		Schema::dropIfExists('account_accountstatus');
	}

}
