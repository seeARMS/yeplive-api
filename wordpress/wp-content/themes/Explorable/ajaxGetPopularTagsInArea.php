<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

global $wpdb;

    $user_id = $_GET['user_id'];

	$NElng = $_GET['NElng'];
    $NElat = $_GET['NElat'];
    $SWlng = $_GET['SWlng'];
    $SWlat = $_GET['SWlat'];

    $pattern = $_GET['pattern'];

    $filter = $_GET['filter'];

    $currentTime = date('Y-m-d H:i:s');

    $not = "";
    $area_clause = "";
    $time_clause = "";


    if ($pattern == "")
    {
        $like = "%";
        $tags_limit = 8;

        if ($NElat < $SWlat)
        {
            $lat1 = $NElat;
            $lat2 = $SWlat;
        }
        else
        {
            $lat1 = $SWlat;
            $lat2 = $NElat;
        }

        if ($NElng < $SWlng)
        {
            $lng1 = $NElng;
            $lng2 = $SWlng;
            $not = " NOT ";
        }
        else
        {
            $lng1 = $SWlng;
            $lng2 = $NElng;
        }

        $area_clause = "(latitude BETWEEN " . $lat1 . " AND " . $lat2 . ")
                        AND
                        (longitude " . $not . " BETWEEN " . $lng1 . " AND " . $lng2 . ")
                        AND";
    }
    else
    {
        $like = $pattern . "%";
        $tags_limit = 4;
    }


    $beforeDate = time() - (1 * 24 * 60 * 60);
    $strBefore = date('Y-m-d H:i:s', $beforeDate);
    //$time_clause = "(start_time IS NOT NULL AND end_time IS NULL) OR (end_time BETWEEN '".$strBefore."' AND '".$currentTime."' AND vod_enable='1')";

    $curTime = date('Y-m-d H:i:s', time());
    $time_clause = "((start_time >= DATE_SUB('$curTime', INTERVAL 1 DAY))
			    AND ((end_time IS NULL AND (start_time >= DATE_SUB('$curTime', INTERVAL 5 MINUTE))) OR end_time <= '$curTime' AND vod_enable = 1))";

    if ($filter == "live")
    {
        $time_clause = "((start_time >= DATE_SUB('$curTime', INTERVAL 5 MINUTE)) AND start_time IS NOT NULL AND end_time IS NULL)";
    }
    else if ($filter == "5min")
    {
        $beforeDate = time() - (5 * 60);
        $strBefore = date('Y-m-d H:i:s', $beforeDate);
        $time_clause = "(end_time BETWEEN '".$strBefore."' AND '".$currentTime."' AND vod_enable='1')";
    }
    else if ($filter == "1hour")
    {
        $beforeDate = time() - (60 * 60);
        $strBefore = date('Y-m-d H:i:s', $beforeDate);
        $time_clause = "(end_time BETWEEN '".$strBefore."' AND '".$currentTime."' AND vod_enable='1')";
    }
    else if ($filter == "archived")
    {
        $time_clause = "(vod_enable='1')";
    }


    $sql_query = "SELECT name, count(*) FROM
                                (SELECT tags.name name FROM wp_tags tags INNER JOIN wp_tags_program tp ON tp.tag_id = tags.id WHERE tp.program_id IN
                                            (SELECT program_id FROM wp_program WHERE "
                                                                    . $area_clause .
                                                                    "
                                                                    (
                                                                       " . $time_clause . "
											                        )
                                            )
                                ) t WHERE name LIKE '" . $like . "' GROUP BY name ORDER BY count(*) DESC LIMIT " . $tags_limit;
    $tags_list = $wpdb->get_results($sql_query);

    $result = "";
    if ( count($tags_list) != 0 )
    {
        foreach ($tags_list as $row => $values)
        {
            $result = $result . $values->name . ",";
        }
    }

    $result = $result . "/|/|";

    if ($user_id != "")
    {
        $sql_query = "SELECT users.display_name display_name, users_yep.user_id user_id, users_yep.picture_path picture_path FROM wp_users users INNER JOIN
                                (SELECT wp_user_id,user_id,picture_path FROM wp_user_yep WHERE user_id IN
                                                (SELECT DISTINCT user_id FROM wp_program WHERE "
                                                        . $area_clause .
                                                        "
                                                        (
                                                            " . $time_clause . "
                                                        )
                                                        AND
                                                        (user_id IN (SELECT user_id FROM wp_user_pans WHERE pan_id = ". $user_id . "))
                                                )
                                )
                                users_yep ON users_yep.wp_user_id = users.id WHERE users.display_name LIKE '" . $like . "' LIMIT 4";

        $followed_users_list = $wpdb->get_results($sql_query);


        if ( count($followed_users_list) != 0 )
        {
            foreach ($followed_users_list as $row => $values)
            {
                $result = $result . $values->user_id . ",";
                $result = $result . $values->display_name . ",";
                $result = $result . $values->picture_path . "||";
            }
        }
    }

    $result = $result . "/|/|";

    $sql_query = "SELECT ids.user_id user_id, ids.picture_path, users.display_name display_name FROM wp_users users INNER JOIN
                        (SELECT user_yep.wp_user_id id, user_yep.user_id user_id, user_yep.picture_path picture_path FROM wp_user_yep user_yep INNER JOIN
                                (SELECT user_id, count(*) FROM wp_user_pans WHERE user_id IN
                                        (SELECT DISTINCT user_id FROM wp_program WHERE "
                                                        . $area_clause .
                                                        "
                                                        (
                                                            " . $time_clause . "
                                                        )
                                        )GROUP BY user_id ORDER BY count(*) DESC ) pans ON user_yep.user_id = pans.user_id) ids
                                ON users.id = ids.id WHERE users.display_name LIKE '" . $like . "' LIMIT 4";

    $max_users_list = $wpdb->get_results($sql_query);


    if ( count($max_users_list) != 0 )
    {
        foreach ($max_users_list as $row => $values)
        {
            $result = $result . $values->user_id . ",";
            $result = $result . $values->display_name . ",";
            $result = $result . $values->picture_path . "||";
        }
    }

    echo $result;

?>