<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$program_id = $_GET['program_id'];
    $user_id = $_GET['user_id'];


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


    $sql_query = "select vote from wp_votes where program_id = '" . $program_id . "' and user_id = '" . $user_id . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $vote = $row["vote"];

    if ($vote)
    {
       echo $vote;
    }
    else
    {
        echo "failed";
    }

?>