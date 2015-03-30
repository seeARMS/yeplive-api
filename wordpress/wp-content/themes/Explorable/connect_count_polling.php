<?php
/*

Template name: connect_count_polling

*/
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$program_id = $_GET['program_id'];
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	
	if(!$link)
	{
		echo "mysql_connect error";
		exit;
	}
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	
	$sql_query = "select connect_count from wp_program where program_id = '" . $program_id . "'";
	
	
	$result = mysql_query($sql_query);
	
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}
	
	$row = mysql_fetch_array($result);
	
	$connect_count = $row['connect_count'];
	
	mysql_free_result($result);
	mysql_close($link);
	
	echo $connect_count;
?>