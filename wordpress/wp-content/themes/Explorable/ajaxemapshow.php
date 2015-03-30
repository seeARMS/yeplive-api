<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	global $wpdb;
	
 	$program_id = $_GET["program_id"];
 	$currentTime = date('Y-m-d H:i:s');
//
// 	$beforeDate = time() - (1 * 24 * 60 * 60);
// 	$strBefore = date('Y-m-d H:i:s', $beforeDate);

    $tags = $_GET["tags"];
    $user_id = $_GET["user_id"];
    $filter = $_GET["filter"];

    $beforeDate = time() - (1 * 24 * 60 * 60);
    $strBefore = date('Y-m-d H:i:s', $beforeDate);
    //$time_clause = "(lp.start_time IS NOT NULL AND lp.end_time IS NULL) OR (lp.end_time BETWEEN '".$strBefore."' AND '".$currentTime."' AND lp.vod_enable='1')";

    $curTime = date('Y-m-d H:i:s', time());
    $time_clause = "((lp.start_time >= DATE_SUB('$curTime', INTERVAL 1 DAY))
			AND ((lp.end_time IS NULL AND lp.start_time >= DATE_SUB('$curTime', INTERVAL 5 MINUTE)) OR lp.end_time <= '$curTime' AND lp.vod_enable = 1))";

    if ($filter == "live")
    {
        $time_clause = "((lp.start_time >= DATE_SUB('$curTime', INTERVAL 5 MINUTE)) AND lp.start_time IS NOT NULL AND lp.end_time IS NULL)";
    }
    else if ($filter == "5min")
    {
        $beforeDate = time() - (5 * 60);
        $strBefore = date('Y-m-d H:i:s', $beforeDate);
        $time_clause = "(lp.end_time BETWEEN '".$strBefore."' AND '".$currentTime."' AND lp.vod_enable='1')";
    }
    else if ($filter == "1hour")
    {
        $beforeDate = time() - (60 * 60);
        $strBefore = date('Y-m-d H:i:s', $beforeDate);
        $time_clause = "(lp.end_time BETWEEN '".$strBefore."' AND '".$currentTime."' AND lp.vod_enable='1')";
    }
    else if ($filter == "archived")
    {
        $time_clause = "(lp.vod_enable='1')";
    }
	
//	$currentTime = "2013-12-27 04:43:45";
//	$strBefore = "2013-12-26 04:43:45";

    if ($tags != "")
    {
        $tags_array = explode(",", $tags);

        $sql_query = "SELECT * FROM
                                 (SELECT
											lp.program_id 	pid,
											lp.vote vote,
											lp.channel_id cid,
											lp.start_time start_time,
											lp.end_time end_time,
											lp.vod_path vod_path,
											lp.vod_enable vod_enable,
											lu.user_id as userid,
											wu.user_nicename as username,
											lu.facebook_name,
											lu.twitter_name,
											lp.latitude lat,
											lp.longitude lgt,
											lp.title title,
											lp.description description,
											lp.isMobile,
											lp.connect_count connect_count,
											lp.image_path image_path,
											lp.location location,
											lp.vote_neg vote_neg,
											lu.facebook_id facebook_id,
											lu.twitter_id twitter_id,
											lu.picture_path userpic,
											lp.duration duration
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
											( " . $time_clause . " )
								 ) tt where pid in
								            (select program_id from wp_tags_program tp where tp.tag_id in
								                    (select id from wp_tags where ";

        //SELECT * FROM `wp_program` program where program.program_id in (select program_id from wp_tags_program tp where tp.tag_id in (select id from wp_tags where name = 'tag2'))

        $first_tag = key($tags_array);

        foreach ($tags_array as $key => $tag)
        {
            if ($tag == '') continue;
            if ($key == $first_tag) $sql_query = $sql_query . " name = '" . $tag . "'";
            else $sql_query = $sql_query . " or name = '" . $tag . "'";

        }
        $sql_query = $sql_query . ")) ORDER BY pid DESC";

        $new_emap_list = $wpdb->get_results($sql_query);
    }
    else if ($user_id != "")
    {
        $new_emap_list = $wpdb->get_results("SELECT
											lp.program_id 	pid,
											lp.vote vote,
											lp.channel_id cid,
											lp.start_time start_time,
											lp.end_time end_time,
											lp.vod_path vod_path,
											lp.vod_enable vod_enable,
											lu.user_id as userid,
											wu.user_nicename as username,
											lu.facebook_name,
											lu.twitter_name,
											lp.latitude lat,
											lp.longitude lgt,
											lp.title title,
											lp.description description,
											lp.isMobile,
											lp.connect_count connect_count,
											lp.image_path image_path,
											lp.location location,
											lp.vote_neg vote_neg,
											lu.facebook_id facebook_id,
											lu.twitter_id twitter_id,
											lu.picture_path userpic,
											lp.duration duration
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
											( " . $time_clause . " )
									AND
									(lp.user_id = " . $user_id . ") ORDER BY pid DESC");
    }
    else
    {
        $new_emap_list = $wpdb->get_results("SELECT
											lp.program_id 	pid,
											lp.vote vote,
											lp.channel_id cid,
											lp.start_time start_time,
											lp.end_time end_time,
											lp.vod_path vod_path,
											lp.vod_enable vod_enable,
											lu.user_id as userid,
											wu.user_nicename as username,
											lu.facebook_name,
											lu.twitter_name,
											lp.latitude lat,
											lp.longitude lgt,
											lp.title title,
											lp.description description,
											lp.isMobile,
											lp.connect_count connect_count,
											lp.image_path image_path,
											lp.location location,
											lp.vote_neg vote_neg,
											lu.facebook_id facebook_id,
											lu.twitter_id twitter_id,
											lu.picture_path userpic,
											lp.duration duration
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
											( " . $time_clause . " ) ORDER BY pid DESC");
    }

	
	$pids = "";
	if ( count($new_emap_list) != 0 ) {
		foreach ($new_emap_list as $row => $values)     // and lp.end_time is null
		{
			$pids = $pids . $values->pid . "---";
			$pids = $pids . $values->vote . "---";
			$pids = $pids . $values->cid . "---";
			$pids = $pids . $values->start_time . "---";
			$pids = $pids . $values->end_time . "---";
			$pids = $pids . $values->vod_path . "---";
			$pids = $pids . $values->vod_enable . "---";
			$pids = $pids . $values->userid . "---";
			$pids = $pids . $values->username . "---";
			$pids = $pids . $values->facebook_name . "---";
			$pids = $pids . $values->twitter_name . "---";
			$pids = $pids . $values->lat . "---";
			$pids = $pids . $values->lgt . "---";
			$pids = $pids . $values->title . "---";
			$pids = $pids . $values->description . "---";
			$pids = $pids . $values->isMobile . "---";
			$pids = $pids . $values->connect_count . "---";
            $pids = $pids . $values->image_path . "---";
            $pids = $pids . $values->location . "---";
            $pids = $pids . $values->vote_neg . "---";
            $pids = $pids . $values->facebook_id . "---";
            $pids = $pids . $values->userpic . "---";
            $pids = $pids . $values->twitter_id . "---";
            $pids = $pids . $values->duration . "///";
		}
	}
	echo $pids;
?>


