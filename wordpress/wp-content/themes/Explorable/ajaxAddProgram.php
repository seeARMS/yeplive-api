<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$channel_id = $_GET['channel_id'];
	$user_id = $_GET['userid'];
	$title = $_GET['title'];
	//$duration = $_GET['duration'];
	$image_path = $_GET['imgurl'];
	$vod_enable = $_GET['vod_enable'];
	$latitude = $_GET['latitude'];
	$longitude = $_GET['longitude'];
    $location = $_GET['location'];
	//$vod_path = $_GET['vodpath'];
	$description = $_GET['description'];

    $tags = $_GET['tags'];
	
	$start_time = date("Y-m-d H:i:s");
	//$end_time = date("Y-m-d H:i:s" , time() + $duration * 60);
	
	
	
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		echo "mysql_connect err";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	$sql_query = "";

//    $sql_query = "SELECT * FROM wp_warning_messages WHERE user_id = " . $user_id;
//    $result = mysql_query($sql_query);
//    $row = mysql_fetch_array($result);
//
//    if ($row != "")
//    {
//        $message = "Your video (" . $row['program_title'] . ") contravenes our terms and conditions. Next time you post a video of this nature, your account will be suspended";
//        $sql_query = "DELETE FROM wp_warning_messages WHERE user_id  = " . $user_id;
//
//        echo "$message";
//    }


    $sql_query = "select bannedUntil, TIME_TO_SEC(TIMEDIFF(bannedUntil, NOW())) as diff, bannedPermanently  from wp_user_yep where user_id=" . $user_id . "";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);

    $banned = "";

    if ($row['bannedPermanently'] == "1")
    {
        $banned = -1;
        setcookie('bannedPermanently', '1', time() + 10000000000, '/');
    }
    else if ($row['diff'] != "")
    {
        if (strpos($row['diff'], "-") !== 0)
        {
            $banned = $row['bannedUntil'];
            setcookie('bannedUntil', $banned, time() + $row['diff'], '/');
        }
    }
    else if (isset($_COOKIE['bannedUntil'])) $banned = $_COOKIE['bannedUntil'];
    else if (isset($_COOKIE['bannedPermanently'])) $banned = -1;

    if ($banned == -1)
    {
        echo "banned -1";
        exit;
    }
    else if($banned != "")
    {
        echo "banned " . $row["bannedUntil"];
        exit;
    }
	
	$sql_query = "insert into  wp_program(channel_id,
										user_id,
										title,
										image_path,
										vod_enable,
										vote,
										vote_neg,
										latitude,
										longitude,
										location,
										description,
										connect_count)
				values('" . $channel_id ."' , 
						'" . $user_id ."' ,
						'" . $title ."' ,
						'" . $image_path ."' ,		
						'0' ,
						'0' ,
						'0' ,
						'" . $latitude ."' ,
						'" . $longitude ."' ,
						'" . $location ."' ,
						'" . $description ."',
						'0')";

	
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query fail";	
		exit;
	}

    $sql_query = "select max(program_id) program_id, channel_id from wp_program";
    if(!mysql_select_db(DB_NAME,$link))
    {
        echo "mysql_select_db error";
        exit;
    }


    $result = mysql_query($sql_query);

    if(!$result)
    {
        echo "mysql_query error";
        exit;
    }
    $row = mysql_fetch_array($result);

    $program_id = $row['program_id'];
    $channel_id = $row['channel_id'];



    $tags_array = explode(",", $tags);

    foreach ($tags_array as $tag)
    {
        if ($tag == '') continue;
        $sql_query = "select * from wp_tags where name = '" . $tag . "'";
        $result = mysql_query($sql_query);
        if(mysql_num_rows($result) == 0)
        {
            $sql_query = "insert into wp_tags (name) values ('" . $tag . "')";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "mysql_query insert into wp_tags fail";
                exit;
            }
        }

        $sql_query = "insert into wp_tags_program (tag_id, program_id) values ((select id from wp_tags where name='" . $tag . "'), '" . $program_id . "')";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            //echo "mysql_query insert into wp_tags_program fail";
            //exit;
        }

    }


    $sql_query = "select pan_id from wp_user_pans where user_id = " . $user_id;
    $result = mysql_query($sql_query);

    while($row = mysql_fetch_array($result))
    {
        $sql_query = "insert into wp_notifications (program_id, user_receiver_id, user_sender_id, type, action, picture_path) values ('" . $program_id . "' , '" . $row['pan_id'] . "' , '" . $user_id . "', '2' ,
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
        $result2 = mysql_query($sql_query);

        if(!$result2)
        {
            echo "mysql_query error";
            exit;
        }
    }

	
	mysql_free_result($result);
	
	mysql_close($link);
	
	
	echo $program_id . "," . $channel_id;
?>