<?php

/*

Template name: facebook_notify

*/

require_once('fbconnect.php');
require_once('wp-content/themes/Explorable/userMgm.php');

$fbconnect = new Fbconnect;

if (!isset($_SESSION)) session_start();

$_SESSION['facebook_id'] = $fbconnect->user['id'];
$_SESSION['facebook_name'] = $fbconnect->user['name'];
$_SESSION['facebook_email'] = $fbconnect->user['email'];
$_SESSION['facebook_accessToken'] = $fbconnect->getAccessToken();

$friends = $fbconnect->api('me/friends');
$friendsCount = count($friends['data']);

$_SESSION['facebook_friends'] = $friendsCount;

global $wpdb;


$current_user = wp_get_current_user();
$wp_user_id = $current_user->ID;

if ($wp_user_id != 0)
{
	$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE facebook_id = '" . $_SESSION['facebook_id'] . "'");
	
	if (count($rows) == 1)
	{
		$row = $rows[0];
		$userMgm = new UserMgm;
		$userMgm->moveUserId($row->user_id, $_SESSION['user_id']);
	}
	
	$wpdb->query( $wpdb->prepare(
			"UPDATE wp_user_yep SET facebook_id = %s, facebook_name = %s, facebook_email = %s, facebook_access_token = %s, facebook_friends = %d WHERE wp_user_id = %d",
			$_SESSION['facebook_id'],
			$_SESSION['facebook_name'],
			$_SESSION['facebook_email'],
			$_SESSION['facebook_accessToken'],
			$_SESSION['facebook_friends'],
			$wp_user_id
	));
	
	$rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE wp_user_id = " . $wp_user_id);
	
	$row = $rows[0];

    if ($rows[0]->diff != null)
        if (strpos($rows[0]->diff, "-") !== 0)
            $banned = $rows[0]->bannedUntil;
    if($banned != "")
    {
        echo "You have been banned until " . $banned . " (server time)";
        exit;
    }
	
	$_SESSION['user_id'] = $row->user_id;
}
else
{

	if (isset($_SESSION['user_id']))
	{
		$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE facebook_id = '" . $_SESSION['facebook_id'] . "'");
		
//		if (count($rows) == 1)
//		{
//			$row = $rows[0];
//
//			$userMgm = new UserMgm;
//			if ($row->wp_user_id == null)
//			{
//				$userMgm->moveUserId($row->user_id, $_SESSION['user_id']);
//			}
//			else
//			{
//				$userMgm->moveUserId($_SESSION['user_id'], $row->user_id);
//				$_SESSION['user_id'] = $row->user_id;
//			}
//
//		}
			
		$wpdb->query( $wpdb->prepare(
				"UPDATE wp_user_yep SET facebook_id = %s, facebook_name = %s, facebook_email = %s, facebook_access_token = %s, facebook_friends = %d WHERE user_id = %d",
				$_SESSION['facebook_id'],
				$_SESSION['facebook_name'],
				$_SESSION['facebook_email'],
				$_SESSION['facebook_accessToken'],
				$_SESSION['facebook_friends'],
				$_SESSION['user_id']
		));
		
		$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE user_id = " . $_SESSION['user_id']);
		
		$row = $rows[0];
	}
	else
	{
		$rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE facebook_id = '" . $_SESSION['facebook_id'] . "' ORDER BY user_id DESC");
		$user_id = 1;
		if (count($rows) < 1)
		{
			$wpdb->query( $wpdb->prepare(
					"INSERT INTO wp_user_yep (group_id, facebook_id, facebook_name, facebook_email, facebook_access_token, facebook_friends,wp_user_id) values (%d, %s, %s, %s, %s, %d ,%d)",
					3,	// 1: admin, 2 : publisher, 3 : user
					$_SESSION['facebook_id'],
					$_SESSION['facebook_name'],
					$_SESSION['facebook_email'],
					$_SESSION['facebook_accessToken'],
					$_SESSION['facebook_friends'],
					$user_id
			));
			
			$rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE facebook_id = '" . $_SESSION['facebook_id'] . "'");
		}
			
		$row = $rows[0];

        if ($rows[0]->diff != null)
            if (strpos($rows[0]->diff, "-") !== 0)
                $banned = $rows[0]->bannedUntil;
        if($banned != "")
        {
            echo "You have been banned until " . $banned . " (server time)";
            exit;
        }

		$_SESSION['user_id'] = $row->user_id;
	}

}
	
if ($row->twitter_id != null)
{
	$_SESSION['twitter_login'] = 1;
	$_SESSION['twitter_id'] = $row->twitter_id;
	$_SESSION['twitter_name'] = $row->twitter_name;
	$_SESSION['twitter_oauthToken'] = $row->twitter_oauth_token;
	$_SESSION['twitter_oauthTokenSecret'] = $row->twitter_oauth_token_secret;
}

$_SESSION['facebook_login'] = 1;
