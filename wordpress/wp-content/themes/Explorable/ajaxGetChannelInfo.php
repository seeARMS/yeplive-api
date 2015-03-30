<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$category_id = $_GET['category_id'];
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		
	}
	$sql_query = "select channel_id,channel_name,category_id from wp_channel where category_id = '" . $category_id . "'";
	$result = mysql_query($sql_query);
	if(!result)
	{
		
	}
	$channel_info = "";
	while($row = mysql_fetch_array($result))
	{
		$channel_info = $channel_info . $row['channel_id'] . "," . $row['channel_name'] . "/";
	}
	echo $channel_info;
?>