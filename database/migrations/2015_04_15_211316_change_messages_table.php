<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('messages');
		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sender_id');
			$table->dateTime('timestamp');
			$table->integer('channel_id')->references('channel_id')->on('yeps');
			$table->string('display_name');
			$table->string('message');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
		Schema::create('messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('channel_id');
			$table->string('display_name');
			$table->string('message');
			$table->timestamps();
		});
	}

}