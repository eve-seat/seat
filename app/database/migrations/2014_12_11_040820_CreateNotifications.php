<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_notifications', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('type')->default('system');
            $table->string('title');
            $table->string('text');
            $table->integer('read')->default(0);
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
        Schema::drop('seat_notifications');
    }

}