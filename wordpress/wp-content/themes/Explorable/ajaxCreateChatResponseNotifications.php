
<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

    $user_sender_id = $_GET['user_sender_id'];
    $program_id = $_GET['program_id'];
    $action = $_GET['action'];
 	
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

    $sql_query = "SELECT DISTINCT user_id FROM wp_chat_messages WHERE room_id = '" . $program_id . "' AND user_id NOT IN (SELECT user_id FROM wp_user_settings WHERE chat_response_notifications = 0) AND user_id != '" . $user_sender_id . "' AND isUploader = 0
                    UNION SELECT user_id FROM wp_program WHERE program_id = '" . $program_id . "' AND user_id NOT IN (SELECT user_id FROM wp_user_settings WHERE chat_response_notifications = 0) AND user_id != '" . $user_sender_id . "'";
    $result = mysql_query($sql_query);

    while($row = mysql_fetch_array($result))
    {
        $sql_query = "insert into wp_notifications (program_id, user_receiver_id, user_sender_id, type, action, picture_path) values ('" . $program_id . "' , '" . $row['user_id'] . "' , '" . $user_sender_id . "', '6' , '" . $action . "',
                                                        (
                                                            SELECT CASE WHEN picture_path IS NOT NULL THEN picture_path
                                                                    WHEN facebook_name IS NOT NULL THEN facebook_picture
                                                                    WHEN twitter_name IS NOT NULL THEN twitter_img
                                                                    ELSE NULL
                                                                    END picture_path FROM wp_user_yep WHERE user_id = '". $user_sender_id ."'
                                                        )
                                                    )";
        $result2 = mysql_query($sql_query);

        if(!$result2)
        {
            echo "mysql_query error";
        }
        else
        {
            echo "success";
        }
    }



	