<?php
/*

Template name: facebook_sendPost

*/

require_once('fbconnect.php');

if (!isset($_SESSION)) session_start();

$channelUrl = $_POST['channelUrl'];
$title = $_POST['title'];

$fbconnect = new Fbconnect;

$fbconnect->setAccessToken($_SESSION['facebook_accessToken']);

$fbconnect->api('/me/feed', 'POST',
		array(
			'link' => $channelUrl,
			'message' => $title)
);

$friends = $fbconnect->api('me/friends');
$friendsCount = count($friends['data']);

$_SESSION['facebook_friends'] = $friendsCount;

echo $_SESSION['facebook_friends']; 