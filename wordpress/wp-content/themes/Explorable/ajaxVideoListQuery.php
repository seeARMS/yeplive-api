<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$channel_id = $_POST['title'];
 	
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
	
	$sql_query = "select 
						lp.program_id pid,
						lp.channel_id cid,
						lp.title title,
						lp.image_path,
						lp.vod_enable vod_enable,
						lp.vod_path vod_path,
						lp.vote vote,
						lp.latitude lat,
						lp.longitude lgt,
						lu.user_id as userid,
						lp.start_time start_time,
						lp.end_time end_time,
						lp.description description,
						lp.connect_count,
						lp.isMobile,
						wu.user_nicename as username,
						lu.facebook_name,
						lu.twitter_name
					FROM 
						wp_program lp
					inner join
						wp_user_yep lu
					on
						lp.user_id = lu.user_id
					left join
						wp_users wu
					on
						lu.wp_user_id = wu.ID
					WHERE
						 channel_id = '" . $channel_id . "' ";	
	
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}

	$count = 0;
	$tempStr = array();
	while ($row[$count] = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$tempStr[] = implode(",,", $row[$count]);
		$count++;
	}
	echo implode("---",$tempStr);

