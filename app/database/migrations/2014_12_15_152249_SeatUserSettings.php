<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeatUserSettings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_user_settings', function(Blueprint $table)
        {

            $table->increments('id');
            $table->integer('user_id');
            $table->string('setting');
            $table->string('value');

            $table->timestamps();

            // Unique composite index over user_id and setting
            $table->unique(array('user_id', 'setting'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_user_settings');
    }

}
