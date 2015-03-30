<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

    $user_id = $_GET['user_id'];
    $pan_id = $_GET['pan_id'];

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


    $sql_query = "select COUNT(*) as isFollow from wp_user_pans where user_id = '" . $user_id . "' and pan_id = '" . $pan_id . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $isFollow = $row['isFollow'];


    if ($isFollow == 0)
    {
       echo "false";
    }
    else
    {
        echo "true";
    }

?>