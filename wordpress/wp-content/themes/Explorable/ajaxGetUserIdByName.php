<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

    $username = $_GET['username'];


	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		echo "error";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "error";
		exit;
	}


    $sql_query = "select user_id from wp_user_yep where wp_user_id in (select id from wp_users where display_name = '" . $username . "')";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $user_id = $row["user_id"];

    if ($user_id)
    {
       echo $user_id;
    }
    else
    {
        echo "error";
    }

?>