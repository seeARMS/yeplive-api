
<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

    $user_id = $_GET['user_id'];
	$userpic = $_GET['userpic'];
 	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		echo "mysql_connect error";
		exit;
	}
	if(!mysql_select_db(DB_NAME , $link))
	{
		echo "mysql_select_db error";
		exit;
	}

	$sql_query = "UPDATE wp_user_yep SET picture_path = '" . $userpic . "' where user_id = '" . $user_id . "'";
	$result = mysql_query($sql_query);
	echo $result;
	