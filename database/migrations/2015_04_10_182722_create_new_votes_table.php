<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
//		Schema::drop('votes');
		Schema::create('votes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('yep_id');
			$table->boolean('vote');
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
