<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoginHistory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('seat_login_history', function(Blueprint $table)
        {
            $table->increments('id');

            $table->integer('user_id');
            $table->dateTime('login_date');
            $table->string('login_source');
            $table->string('user_agent');

            $table->timestamps();

            // Add indexes
            $table->index('user_id');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('seat_login_history');
	}

}
