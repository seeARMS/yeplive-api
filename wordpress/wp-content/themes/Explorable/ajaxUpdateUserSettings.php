
<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

    $user_id = $_GET['user_id'];
    $chat_response_notifications = $_GET['chat_response_notifications'];
 	
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

    $sql_query = "SELECT * FROM wp_user_settings WHERE user_id = '" . $user_id . "'";
    $result = mysql_query($sql_query);
    if (mysql_fetch_array($result))
    {
        $sql_query = "UPDATE wp_user_settings SET chat_response_notifications = '" . $chat_response_notifications. "' where user_id = '" . $user_id . "'";
        $result = mysql_query($sql_query);
        if ($result)
        {
            echo "success";
            exit;
        }
        else
        {
            echo "failed";
            exit;
        }
    }
    else
    {
        $sql_query = "INSERT INTO wp_user_settings (user_id, chat_response_notifications) VALUES ('" . $user_id. "', '" . $chat_response_notifications . "');";
        $result = mysql_query($sql_query);
        if ($result)
        {
            echo "success";
            exit;
        }
        else
        {
            echo "failed";
            exit;
        }
    }


	