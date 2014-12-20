<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCorporationCustomsOffices extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporation_customsoffices', function(Blueprint $table)
        {

            $table->increments('id');
            $table->integer('corporationID');
            $table->bigInteger('itemID');
            $table->bigInteger('solarSystemID');
            $table->string('solarSystemName');
            $table->integer('reinforceHour');
            $table->boolean('allowAlliance');
            $table->boolean('allowStandings');
            $table->double('standingLevel');
            $table->double('taxRateAlliance');
            $table->double('taxRateCorp');
            $table->double('taxRateStandingHigh');
            $table->double('taxRateStandingGood');
            $table->double('taxRateStandingNeutral');
            $table->double('taxRateStandingBad');
            $table->double('taxRateStandingHorrible');
            $table->timestamps();

            // Indexes
            $table->index('corporationID');
            $table->index('solarSystemID');
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
        Schema::dropIfExists('corporation_customsoffices');
    }

}
