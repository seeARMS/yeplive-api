<?php
/*

Template name: twitter_sendPost

*/
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

	$channelUrl = $_POST['channelUrl'];
	$title = $_POST['title'];

if (isset($_SESSION['twitter_id']))
{
	global $wpdb;
	
	$members = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE twitter_id = '" . $_SESSION['twitter_id'] . "'");
	
	$member = $members[0];
	
	$comment = $title;
	$short_url = $channelUrl;
	
	$msg = $comment . " " . $short_url;
	
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $member->twitter_oauth_token, $member->twitter_oauth_token_secret);
	
	$connection->post('statuses/update', array('status' => $msg));
	
	echo 'Successfully promoted on Twitter !';	
}
else
{
	echo 'Please login to Twitter.';
}
