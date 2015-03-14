<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecurityLog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('security_log', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('user_id');
			$table->integer('event_type_id');
			$table->string('triggered_by');
			$table->string('triggered_for')->nullable();
			$table->string('path')->nullable();
			$table->string('message');
			$table->string('user_ip')->nullable();
			$table->string('user_agent')->nullable();
			$table->string('valid_keys')->nullable();
			$table->string('corporation_affiliations')->nullable();

			// Indexes
			$table->index('event_type_id');
			$table->index('triggered_by');
			$table->index('triggered_for');

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
		Schema::dropIfExists('security_log');
	}

}
