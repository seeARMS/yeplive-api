<?php namespace App\Services;

class Push {

	public static function sendToFollowers($user)
	{
		$followers = $user->followers;
		//	$query = \ParseInstallation::
		// Notification for Android users
		$queryAndroid = ParseInstallation::query();
		$queryAndroid->equalTo('deviceType', 'android');
		 
		ParsePush::send(array(
			"where" => $queryAndroid,
			"data" => array(
				"alert" => "Your suitcase has been filled with tiny robots!"
			)
		));
		return true;	
	}
}
