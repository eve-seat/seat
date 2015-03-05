<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Class SeatUsersNullableColumns
 *
 * Sets last_login and last_login_source to be nullable.
 */
class SeatUsersNullableColumns extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Possibly @TODO: create conditionals to write raw queries for all supported storage types
        DB::statement('ALTER TABLE `seat_users` MODIFY `last_login` datetime NULL;');
        DB::statement('ALTER TABLE `seat_users` MODIFY `last_login_source` varchar(255) COLLATE utf8_unicode_ci NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Possibly @TODO: create conditionals to write raw queries for all supported storage types
        DB::statement('ALTER TABLE `seat_users` MODIFY `last_login` datetime NOT NULL;');

        DB::statement('ALTER TABLE `seat_users` MODIFY `last_login_source`'
            . ' varchar(255) COLLATE utf8_unicode_ci NOT NULL;');
    }

}