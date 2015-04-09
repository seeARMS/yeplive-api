<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yeplive_users', function(Blueprint $table)
		{
			$table->increments('user_id');
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->string('activation_key');
			$table->string('url');
			$table->string('display_name');
			$table->string('picture_path');
			$table->string('facebook_id');
			$table->string('facebook_email');
			$table->string('facebook_access_token');
			$table->integer('facebook_friends');
			$table->string('facebook_picture');
			$table->string('twitter_id');
			$table->string('twitter_name');
			$table->string('twitter_oauth_token');
			$table->string('twitter_oauth_token_secret');
			$table->string('google_name');
			$table->string('google_access_token');
			$table->string('google_email');
			$table->string('google_picture');
			$table->dateTime('bannedUntil');
			$table->tinyInteger('bannedPermanently');
			$table->string('status');
			$table->rememberToken();
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
		Schema::drop('yeplive_users');
	}

}
