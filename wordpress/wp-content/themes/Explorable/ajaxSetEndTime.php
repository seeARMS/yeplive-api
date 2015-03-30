<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$prog_id = $_GET['prog_id'];
	
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	
	if (!mysql_select_db(DB_NAME, $link)) {
		echo 'Could not select database';
		exit;
	}
	
	
	$curr_time = date("Y-m-d H:i:s");
	
	$sql_query = "update wp_program set end_time = '" . $curr_time . "' , vod_enable = '1' where program_id = '" . $prog_id . "'";
	$result = mysql_query($sql_query);
	
	mysql_close($link);
	
	echo "success";
?>