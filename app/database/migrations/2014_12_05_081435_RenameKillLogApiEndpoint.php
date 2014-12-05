<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameKillLogApiEndpoint extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\DB::statement("UPDATE `api_calllist` SET `name` = 'KillMails' WHERE `name` = 'KillLog'");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\DB::statement("UPDATE `api_calllist` SET `name` = 'KillLog' WHERE `name` = 'KillMails'");
	}

}
