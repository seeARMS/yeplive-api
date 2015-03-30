<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$user_id = $_GET['user_id'];
	$loginUserId = $_GET['loginUserId'];
	$need = $_GET['Need'];
	
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
	
	$sql_query = "delete from wp_user_pans where user_id = '" . $user_id . "' and pan_id = '" . $loginUserId . "'";
	
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}

	echo "success";
	//mysql_free_result($result);
	
	
?>