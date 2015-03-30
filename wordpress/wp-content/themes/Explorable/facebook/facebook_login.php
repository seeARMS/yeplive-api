<?php
/*

Template name: facebook_login

*/

require_once('fbconnect.php');

$fbconnect = new Fbconnect;

$data = array(
		'redirect_uri' => home_url() . '/facebook_notify/',
		'scope' => 'publish_actions email'
);

wp_redirect($fbconnect->getLoginUrl($data));
