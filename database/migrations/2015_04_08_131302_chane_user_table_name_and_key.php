<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChaneUserTableNameAndKey extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		if (! Schema::hasTable('yeplive_users'))
		{
			Schema::rename('users', 'yeplive_users');
		}
		Schema::table('yeplive_users', function($table)
		{
			$table->renameColumn('id', 'user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::rename('yeplive_users', 'users');
		Schema::table('yeplive_users', function($table)
		{
			$table->renameColumn('user_id', 'id');
		});
	}

}
