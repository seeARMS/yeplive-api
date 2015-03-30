<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$program_id = $_GET['program_id'];
    $user_id = $_GET['user_id'];
    $voteDiff = $_GET['vote'];


//	if((isset($_SESSION['program_id']) && $_SESSION['program_id'] == $program_id) || !isset($program_id) || $program_id == "")
//	{
//		echo "Already voted!!!!!!";
//		exit;
//	}
	
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
        mysql_free_result($result);

        if ($vote > 0)
        {
            $sql_query = "select vote from wp_program where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "mysql_query error";
                exit;
            }
            $row = mysql_fetch_array($result);
            $votes = $row["vote"];
            mysql_free_result($result);

            $votes = $votes - $vote;

            $sql_query = "update wp_program set vote = '" . $votes . "' where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "update fail!";
                exit;
            }
            mysql_free_result($result);


            $sql_query = "delete from wp_votes where program_id = '" . $program_id . "' and user_id = '" . $user_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "delete fail!";
                exit;
            }


            mysql_close($link);

            echo "delete";
        }
        else if ($vote < 0)
        {
            $sql_query = "select vote_neg from wp_program where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "mysql_query error";
                exit;
            }
            $row = mysql_fetch_array($result);
            $votes = $row["vote_neg"];
            mysql_free_result($result);

            $votes = $votes + $vote;

            $sql_query = "update wp_program set vote_neg = '" . $votes . "' where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "update fail!";
                exit;
            }
            mysql_free_result($result);


            $sql_query = "delete from wp_votes where program_id = '" . $program_id . "' and user_id = '" . $user_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "delete fail!";
                exit;
            }

            $sql_query = "delete from wp_notifications where program_id = '" . $program_id . "' and user_sender_id = '" . $user_id . "' and (type = 3 or type = 4)";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "delete fail!";
                exit;
            }


            mysql_close($link);

            echo "delete";
        }

    }
    else
    {
        if ($voteDiff > 0)
        {
            $sql_query = "select vote, user_id from wp_program where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "mysql_query error";
                exit;
            }
            $row = mysql_fetch_array($result);
            $votes = $row["vote"];
            $user_receiver_id = $row["user_id"];
            //mysql_free_result($result);

            $voteDiff = 1;
            $votes = $votes + $voteDiff;


            $sql_query = "update wp_program set vote = '" . $votes . "' where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "update fail!";
                exit;
            }
            //mysql_free_result($result);


            $sql_query = "insert into wp_votes (program_id, user_id, vote) values ('" . $program_id . "' , '" . $user_id . "', '" . $voteDiff . "')";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "insert fail!";
                exit;
            }

            if ($user_receiver_id != $user_id)
            {
                $sql_query = "insert into wp_notifications (program_id, user_receiver_id, user_sender_id, type, action, picture_path) values ('" . $program_id . "' , '" . $user_receiver_id . "' , '" . $user_id . "', '3' ,
                                                (
                                                    SELECT CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
                                                    WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
                                                    ELSE b.twitter_name END user_login
                                                    FROM wp_user_yep b WHERE user_id = '". $user_id ."'
                                                )
                                                ,
                                                (
                                                    SELECT CASE WHEN picture_path IS NOT NULL THEN picture_path
                                                                        WHEN facebook_name IS NOT NULL THEN facebook_picture
                                                                        WHEN twitter_name IS NOT NULL THEN twitter_img
                                                                        ELSE NULL
                                                                        END picture_path FROM wp_user_yep WHERE user_id = '". $user_id ."'
                                                )
                                            )";
                $result = mysql_query($sql_query);
                if(!$result)
                {
                    echo "notification insert fail!";
                    exit;
                }
            }

            mysql_close($link);

            echo "insert";
        }
        else if ($voteDiff < 0)
        {
            $sql_query = "select vote_neg, user_id from wp_program where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "mysql_query error";
                exit;
            }
            $row = mysql_fetch_array($result);
            $votes = $row["vote_neg"];
            $user_receiver_id = $row["user_id"];
            //mysql_free_result($result);

            $voteDiff = -1;
            $votes = $votes - $voteDiff;


            $sql_query = "update wp_program set vote_neg = '" . $votes . "' where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "update fail!";
                exit;
            }
            //mysql_free_result($result);


            $sql_query = "insert into wp_votes (program_id, user_id, vote) values ('" . $program_id . "' , '" . $user_id . "', '" . $voteDiff . "')";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "insert fail!";
                exit;
            }

            if ($user_receiver_id != $user_id)
            {
                $sql_query = "insert into wp_notifications (program_id, user_receiver_id, user_sender_id, type, action, picture_path) values ('" . $program_id . "' , '" . $user_receiver_id . "' , '" . $user_id . "', '4' ,
                                                (
                                                    SELECT CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
                                                    WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
                                                    ELSE b.twitter_name END user_login
                                                    FROM wp_user_yep b WHERE user_id = '". $user_id ."'
                                                )
                                                ,
                                                (
                                                    SELECT CASE WHEN picture_path IS NOT NULL THEN picture_path
                                                                        WHEN facebook_name IS NOT NULL THEN facebook_picture
                                                                        WHEN twitter_name IS NOT NULL THEN twitter_img
                                                                        ELSE NULL
                                                                        END picture_path FROM wp_user_yep WHERE user_id = '". $user_id ."'
                                                )
                                            )";
                $result = mysql_query($sql_query);
                if(!$result)
                {
                    echo "notification insert fail!";
                    exit;
                }
            }

            mysql_close($link);

            echo "insert";
        }

    }




//
//	if(!isset($_SESSION['program_id']) || $_SESSION['program_id'] != $program_id || $_SESSION['program_id'] == "")
//	{
//
//		session_start();
//		$_SESSION['program_id'] = $program_id;
//		echo "success";
//	}
//	else
//	{
//		echo "already voted!";
//	}
	
?>