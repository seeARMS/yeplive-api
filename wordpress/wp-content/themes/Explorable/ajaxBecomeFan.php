<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$user_id = $_GET['user_id'];
	$loginUserId = $_GET['loginUserId'];
	$need = $_GET['Need'];
	
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
	$sql_query = "select user_id,pan_id from wp_user_pans where user_id = '" . $user_id . "' and pan_id = '" . $loginUserId . "'";
	$result = mysql_query($sql_query);
	
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		$i++;
	}
	if($i > 0)
	{
		mysql_free_result($result);
		mysql_close($link);
		echo "exist.";
		exit;
	}
	mysql_free_result($result);
	
	$sql_query = "insert into wp_user_pans(user_id,pan_id) values('" . $user_id ."' , '" . $loginUserId . "')";
	if(!mysql_select_db(DB_NAME , $link))
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

    $sql_query = "insert into wp_notifications (user_receiver_id, user_sender_id, type, action, picture_path) values ('" . $loginUserId . "' , '" . $user_id . "', '1' ,
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
    if(!mysql_select_db(DB_NAME , $link))
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

	//mysql_free_result($result);
	if($need == "")
	{
		mysql_close($link);
		echo "success";
	}
	else
	{
		
		$sql_query = "select  wu.user_nicename ,
							  lu.facebook_name,
							  lu.facebook_id,
							  lu.twitter_name,
							  lu.twitter_img, 
							wu.user_email ,
							lup.pan_id , 
							lu.picture_path 
						from 
							wp_user_yep lu 
						inner join 
							wp_user_pans lup 
						on 
							lu.user_id = lup.pan_id
						left join 
							wp_users wu 
						on 
							lu.wp_user_id = wu.ID   
					  where 
							 lup.user_id = '" . $user_id . "'
					  and
							 lup.pan_id = '" . $loginUserId . "'";
		
		if(!mysql_select_db(DB_NAME , $link))
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
		
		$user_name = $row['user_nicename'];
		$user_email = $row['user_email'];
		$pan_id = $loginUserId;
		$picture_path = $row['picture_path'];
		$facebook_name = $row['facebook_name'];
		$facebook_id = $row['facebook_id'];
		$twitter_name = $row['twitter_name'];
		$twitter_img = $row['twitter_img'];
		
		mysql_free_result($result);
		mysql_close($link);
		echo $user_name . "&&" . $user_email . "&&" . $pan_id . "&&" . $picture_path . "&&" . $facebook_name . "&&" . $twitter_name . "&&" . $facebook_id . "&&" . $twitter_img;
	}
	
?>