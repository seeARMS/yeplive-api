<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPushNotificationSettingsToUsers extends Migration {

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
			$table -> boolean('push_notifications');
			$table -> string('device_token');
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
		Schema::table('yeplive_users', function($table)
		{
			$table->dropColumn('push_notifications');
			$table->dropColumn('device_token');

		});
	}

}
