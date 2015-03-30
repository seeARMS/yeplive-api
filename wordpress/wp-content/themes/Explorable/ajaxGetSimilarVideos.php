<?php

	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$program_id = $_GET['program_id'];
    $user_id = $_GET['user_id'];
	
	
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
	$sql_query = "";
	
	//$sql_query = "SELECT program_id, title, image_path, user_id as author, description, start_time, connect_count FROM wp_program WHERE start_time >= (now() + interval -(1) week)";
    $sql_query = "SELECT fp.*, u.* FROM (SELECT p.program_id, title, image_path, user_id, description, start_time, connect_count, vote, vote_neg, vod_path FROM `wp_program` p
                    INNER JOIN (SELECT program_id, COUNT(tag_id) count FROM wp_tags_program WHERE tag_id IN
                        (SELECT tag_id FROM `wp_tags_program` WHERE `program_id` = '$program_id') GROUP BY program_id ORDER BY count DESC) t
                    ON p.program_id = t.program_id) as fp
                  INNER JOIN (SELECT uy.user_id, uy.picture_path, wu.display_name FROM wp_user_yep as uy, wp_users as wu WHERE uy.wp_user_id = wu.ID) as u ON fp.user_id = u.user_id WHERE vod_path IS NOT NULL AND program_id != '$program_id' LIMIT 100";
	
	$result = mysql_query($sql_query);

    $title = "";
    $author = "";
    $description = "";
	$start_time = "";
	$end_time = "";
    $views = "";
    $response = "";
    $image_path = "";
    $picture_path = "";
	while($row = mysql_fetch_array($result))
	{
        if ($row['image_path'] != null) $image_path = DOMAIN_ROOT . $row['image_path'];
        else $image_path = DOMAIN_ROOT . 'wp-content/themes/Explorable/images/myprofile.png';

        if ($row['picture_path'] != null) $picture_path = DOMAIN_ROOT . $row['picture_path'];
        else $picture_path = DOMAIN_ROOT . 'wp-content/themes/Explorable/images/profile-thumb.png';

        $time = nicetime($row['start_time']);
        $response = $response . $row['program_id'] . "|/|*/|/" . $row['title'] . "|/|*/|/" . $row['display_name'] . "|/|*/|/" . $row['description'] . "|/|*/|/" . $time . "|/|*/|/" . $row['connect_count'] . "|/|*/|/" . $image_path . "|/|*/|/"
            . $row['user_id'] .  "|/|*/|/" . $picture_path .  "|/|*/|/" . $row['vote'] .  "|/|*/|/" . $row['vote_neg'] .  "|/|*/|/" . $row['vod_path'] . "/****|";
	}
	
	mysql_free_result($result);
	mysql_close($link);

	echo $response;
?>