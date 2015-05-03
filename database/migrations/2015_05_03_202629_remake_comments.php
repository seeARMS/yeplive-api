<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemakeComments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
				Schema::create('comments', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->integer('user_id')->unsigned();
			$table->integer('yep_id')->unsigned();
			$table->string('comment');
			$table->integer('created_time')->default(null);
			$table->integer('updated_time')->default(null);
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
