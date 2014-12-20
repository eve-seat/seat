<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCorporationCustomsOfficesLocations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporation_customsoffices_locations', function(Blueprint $table)
        {

            $table->increments('id');
            $table->integer('corporationID');
            $table->bigInteger('itemID');
            $table->string('itemName');
            $table->double('x');
            $table->double('y');
            $table->double('z');
            $table->integer('mapID')->nullable()->default(null);
            $table->string('mapName')->nullable()->default(null);
            $table->timestamps();

            // Indexes
            $table->index('corporationID');
            $table->index('itemID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporation_customsoffices_locations');
    }

}
