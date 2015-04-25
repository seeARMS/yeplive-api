<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGoogleNameToId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('yeplive_users', function($table)
		{
			$table->renameColumn('google_name', 'google_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('yeplive_users', function($table)
		{
			$table->renameColumn('name', 'id');
		});
		//
	}

}
