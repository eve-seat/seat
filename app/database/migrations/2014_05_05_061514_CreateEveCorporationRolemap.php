<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeatCorporationRoleMap extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_corporation_rolemap', function(Blueprint $table)
        {
          $table->increments('id');
          
          $table->bigInteger('roleID');
          $table->string('roleName');

          // Indexes
          $table->index('roleID');

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
        Schema::drop('seat_corporation_rolemap');
    }

}
