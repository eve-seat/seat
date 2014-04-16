<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seat_users', function(Blueprint $table)
		{
		  $table->increments('id');
		  
		  $table->string('username', 16);
		  $table->string('password', 64);
		  $table->string('email', 96)->unique();
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
		Schema::dropIfExists('seat_users');
	}

}
