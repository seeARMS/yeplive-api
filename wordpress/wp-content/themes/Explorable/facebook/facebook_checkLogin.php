<?php

/*

Template name: facebook_checkLogin

*/

if (!isset($_SESSION)) session_start();

if ($_SESSION['facebook_login'] == 1)
{
	if ($_SESSION['twitter_login'] == 1)
	{
		echo '3';
	}
	else
	{
		echo '1';
	}
	
}
else
{
	echo '0';
}