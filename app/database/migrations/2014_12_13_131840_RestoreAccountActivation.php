<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestoreAccountActivation extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the new columns
        Schema::table('seat_users', function(Blueprint $table)
        {
            $table->string('activation_code')->nullable();
            $table->boolean('activated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the new columns
        Schema::table('seat_users', function(Blueprint $table)
        {
            $table->dropColumn('activation_code');
            $table->dropColumn('activated');
        });
    }

}
