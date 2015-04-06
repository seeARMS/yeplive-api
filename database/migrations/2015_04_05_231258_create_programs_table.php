<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('programs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('channel_id');
			$table->string('title');
			$table->string('image_path');
			$table->boolean('vod_enable');
			$table->string('vod_path');
			$table->double('latitude');
			$table->double('longitude');
			$table->string('location');
			$table->integer('user_id');
			//$table->foreign('user_id')->references('id')->on('users');
			$table->string('start_time');
			$table->string('end_time');
			$table->mediumText('description');
			$table->integer('connect_count');
			$table->boolean('isMobile');
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
		Schema::drop('programs');
	}

}
