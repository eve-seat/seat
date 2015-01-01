<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationTitlemap extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporation_titlemap', function(Blueprint $table)
        {

            $table->increments('id');
            $table->integer('corporationID');
            $table->integer('titleID');
            $table->string('titleName');
            $table->binary('roles');
            $table->binary('grantableRoles');
            $table->binary('rolesAtHQ');
            $table->binary('grantableRolesAtHQ');
            $table->binary('rolesAtBase');
            $table->binary('grantableRolesAtBase');
            $table->binary('rolesAtOther');
            $table->binary('grantableRolesAtOther');
            $table->timestamps();

            // Indexes
            $table->index('corporationID');
            $table->index('titleID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporation_titlemap');
    }

}
