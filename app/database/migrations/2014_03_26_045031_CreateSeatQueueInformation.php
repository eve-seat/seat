<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatQueueInformation extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('queue_information', function(Blueprint $table)
		{
		  $table->increments('id');

		  $table->string('jobID')->unique();
		  $table->integer('ownerID');
		  $table->string('api');
		  $table->string('scope');
		  $table->string('output')->default(null)->nullable();
		  $table->enum('status', array('Queued','Working','Done', 'Error'));

		  // Indexes
		  $table->index('jobID');
		  $table->index('ownerID');
		  $table->index('status');

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
		Schema::dropIfExists('queue_information');
	}

}
