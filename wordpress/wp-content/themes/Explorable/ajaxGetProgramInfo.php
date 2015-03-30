<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$channel_id = $_GET['channel_id'];
	$programIndex = "";
	
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		echo "mysql_connect error!";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	$sql_query = "";
	
	$sql_query = "select
								program_id,
								user_id,
								title,
								image_path,
								start_time,
								end_time
						  from
								wp_program
						  where
								channel_id = '" . $channel_id . "'";
	
	$result = mysql_query($sql_query);
	
	$start_time = "";
	$end_time = "";
	while($row = mysql_fetch_array($result))
	{
		$start_time = $start_time . $row['start_time'] . ",";
		$end_time = $end_time . $row['end_time'] . ",";
	}
	
	mysql_free_result($result);
	mysql_close($link);
	
	$response = "";
	if($start_time != "")
		$response = $start_time . "/" . $end_time; 
	echo $response;
?>