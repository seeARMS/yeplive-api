<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$program_id = $_GET['program_id'];
    $user_id = $_GET['user_id'];
    $period = $_GET['period'];


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

    if ($period == 0)
    {
        $sql_query = "INSERT INTO wp_warning_messages (user_id, program_title) VALUES ('" . $user_id . "', (SELECT title FROM wp_program WHERE program_id = " . $program_id . "))";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            echo "message failed!";
            exit;
        }
        else echo "success";
    }

    else if ($period == -1)
    {
        $sql_query = "UPDATE wp_user_yep SET bannedPermanently = 1 WHERE (user_id=" . $user_id . ")";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            echo "perm ban failed!";
            exit;
        }
        else echo "success";
    }
    else
    {
        $sql_query = "UPDATE wp_user_yep SET bannedUntil = TIMESTAMPADD(SECOND, " . $period . ", NOW()) WHERE (user_id=" . $user_id . ")";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            echo "ban failed!";
            exit;
        }
        else echo "success";
    }

    $sql_query = "DELETE FROM wp_program WHERE program_id = '" . $program_id . "'";
    $result = mysql_query($sql_query);
    if(!$result)
    {
        echo "delete failed!";
        exit;
    }

    mysql_close($link);


?>