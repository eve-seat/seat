<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatBannedCall extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('banned_calls', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->integer('ownerID');
		  $table->string('api');
		  $table->string('scope');
		  $table->string('hash', 32);
		  $table->integer('accessMask')->default(0)->nullable();
		  $table->string('reason')->nullable();

		  // Indexes
		  $table->index('ownerID');
		  $table->index('api');
		  $table->index('hash');

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
		Schema::dropIfExists('banned_calls');
	}
}
