
<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	//$count = $_POST['video_viewer_count'];
	$programId = $_GET['programId'];
 	
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
    $result = mysql_query("select connect_count from wp_program where program_id = '" . $programId . "'");
	$row= mysql_fetch_array($result);

	$count = $row['connect_count'];
	$count += 1;
	
	$sql_query = "UPDATE wp_program SET connect_count = '" . $count . "' where program_id = '" . $programId . "'";
	$result = mysql_query($sql_query);
	echo $count;
	