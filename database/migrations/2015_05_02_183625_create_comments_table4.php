<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable4 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->integer('user_id')->unsigned();
			$table->integer('yep_id')->unsigned();
			$table->foreign('user_id')->references('user_id')->on('yeplive_users');
			$table->foreign('yep_id')->references('id')->on('yeps');
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
		Schema::drop('comments');
	}

}