<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$program_id = $_GET['program_id'];
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		
	}
	$sql_query = "SELECT
											lp.vote vote,
											lp.start_time start_time,
											lp.end_time end_time,
											lp.vod_path vod_path,
											lp.vod_enable vod_enable,
											lu.user_id userid,
											wu.user_nicename username,
											lu.facebook_name facebook_name,
											lu.twitter_name twitter_name,
											lp.latitude lat,
											lp.longitude lgt,
											lp.title title,
											lp.description description,
											lp.isMobile isMobile,
											lp.connect_count connect_count,
											lp.image_path image_path,
											lp.location location,
											lp.vote_neg vote_neg,
											lu.facebook_id facebook_id,
											lu.picture_path userpic
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

									(lp.program_id = '" . $program_id . "')";

	$result = mysql_query($sql_query);
	if(!result)
	{
		echo "error";
	}

	$row = mysql_fetch_array($result);

    $program_info = $row['title'] . "/|/|/" . $row['image_path'] . "/|/|/" . $row['vod_enable'] . "/|/|/" . $row['vod_path'] . "/|/|/" . $row['vote'] . "/|/|/"
        . $row['vote_neg'] . "/|/|/" . $row['lat'] . "/|/|/" . $row['lgt'] . "/|/|/" . $row['location'] . "/|/|/" . $row['userid'] . "/|/|/"
        . $row['start_time'] . "/|/|/" . $row['end_time'] . "/|/|/" . $row['description'] . "/|/|/" . $row['connect_count'] . "/|/|/" . $row['isMobile']  . "/|/|/"
        . $row['username'] . "/|/|/" . $row['userpic'] . "/|/|/" . $row['facebook_id'] . "/|/|/" . $row['twitter_name'] . "/|/|/" . $row['facebook_name'];


    $sql_query = "SELECT `name` FROM `wp_tags` WHERE `id` IN (SELECT tag_id FROM wp_tags_program WHERE program_id = '" . $program_id . "')";
    $result = mysql_query($sql_query);

    $tags = "#";
    while ($row = mysql_fetch_array($result))
    {
        $tags = $tags . $row["name"] . ' #';
    }

    $tags = substr($tags, 0, strlen($tags) - 1);
    $tags = trim($tags);

    $program_info = $program_info . "/|/|/" . $tags;

	echo $program_info;
?>