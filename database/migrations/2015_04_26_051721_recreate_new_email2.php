<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateNewEmail2 extends Migration {

	/**
	 * Run the migrations.
	 *j
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('yeplive_users', function($table){
			$table->string('email');
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
	}

}
