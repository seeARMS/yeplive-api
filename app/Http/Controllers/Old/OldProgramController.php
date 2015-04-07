<?php
//Mobile Methods 
	/const DB_HOST = "yeplive-dev.czc6detrzhw4.us-west-2.rds.amazonaws.com:3306";
const DOMAIN_ROOT = "example.com";
const DB_USER = "root";
const DB_PASSWORD = "rootroot";
const DB_NAME = "test";
*
	public function get_similar_videos(Request $request)
	{
		$program_id = $request->input("program_id");
		$user_id = $request->input("user_id");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
			{
				return "error";
			exit;
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "error";
			exit;
		}
		$sql_query = "";
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

		return $response;
	}

	public function get_tags(Request $request)
	{
		$program_id = $request->input("program_id");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_error";
		}

    $sql_query = "SELECT `name` FROM `wp_tags` WHERE `id` IN (SELECT tag_id FROM wp_tags_program WHERE program_id = '" . $program_id . "')";
    $result = mysql_query($sql_query);

    $tags = "#";
    while ($row = mysql_fetch_array($result))
    {
        $tags = $tags . $row["name"] . ' #';
    }

    $tags = substr($tags, 0, strlen($tags) - 1);
    $tags = trim($tags);

		return $tags;
	} 

	public function get_video_view_count(Request $request)
	{
		$programId = $request->input("program_id");	
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME , $link))
		{
			return "mysql_select_db error";
		}
			$result = mysql_query("select connect_count from wp_program where program_id = '" . $programId . "'");
		$row= mysql_fetch_array($result);
	
		$count = $row['connect_count'];
		$count += 1;
		
		$sql_query = "UPDATE wp_program SET connect_count = '" . $count . "' where program_id = '" . $programId . "'";
		$result = mysql_query($sql_query);
		return $count;
	}

	public function set_vote(Request $request)
	{
		$program_id = $request->input("program_id");
		$user_id = $request->input("user_id");
		$voteDiff = $request->input("vote");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		return "mysql_connect error";
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		return "mysql_select_db error";
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
                return "mysql_query error";
            }
            $row = mysql_fetch_array($result);
            $votes = $row["vote"];
            mysql_free_result($result);

            $votes = $votes - $vote;

            $sql_query = "update wp_program set vote = '" . $votes . "' where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                return "update fail!";
            }
            mysql_free_result($result);


            $sql_query = "delete from wp_votes where program_id = '" . $program_id . "' and user_id = '" . $user_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                return "delete fail!";
            }
        }
        else if ($vote < 0)
        {
            $sql_query = "select vote_neg from wp_program where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                return "mysql_query error";
            }
            $row = mysql_fetch_array($result);
            $votes = $row["vote_neg"];
            mysql_free_result($result);

            $votes = $votes + $vote;

            $sql_query = "update wp_program set vote_neg = '" . $votes . "' where program_id = '" . $program_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                return "update fail!";
            }
            mysql_free_result($result);


            $sql_query = "delete from wp_votes where program_id = '" . $program_id . "' and user_id = '" . $user_id . "'";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                return "delete fail!";
            }

        }

        $sql_query = "delete from wp_notifications where program_id = '" . $program_id . "' and user_sender_id = '" . $user_id . "' and (type = 3 or type = 4)";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            return "delete fail!";
        }
    }

    if ($vote == $voteDiff)
    {
        return "delete";
    }

    if ($voteDiff > 0)
    {
        $sql_query = "select vote, user_id from wp_program where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            return "mysql_query error";
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
            return "update fail!";
        }
        //mysql_free_result($result);


        $sql_query = "insert into wp_votes (program_id, user_id, vote) values ('" . $program_id . "' , '" . $user_id . "', '" . $voteDiff . "')";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            return "insert fail!";
        }

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
            return "notification insert fail!";
        }


        mysql_close($link);

        return"insert";
    }
    else if ($voteDiff < 0)
    {
        $sql_query = "select vote_neg, user_id from wp_program where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            return "mysql_query error";
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
            return "update fail!";
        }
        //mysql_free_result($result);


        $sql_query = "insert into wp_votes (program_id, user_id, vote) values ('" . $program_id . "' , '" . $user_id . "', '" . $voteDiff . "')";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            return "insert fail!";
        }


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
            return "notification insert fail!";
        }

        mysql_close($link);
        return "insert";
    }

	}

	public function get_votes(Request $request)
	{
		$program_id = $request->input("program_id");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}

    $sql_query = "select vote from wp_program where program_id = '" . $program_id . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $votes = $row["vote"];

    return $votes;
	}

	public function get_neg_votes(Request $request)
	{
		$program_id = $request->input("program_id");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}

    $sql_query = "select vote_neg from wp_program where program_id = '" . $program_id . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $votes = $row["vote_neg"];

    return $votes;
	}
	
	public function get_user_vote(Request $request)
	{
		$program_id = $request->input("program_id");
		$user_id = $request->input("user_id");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}


    $sql_query = "select vote from wp_votes where program_id = '" . $program_id . "' and user_id = '" . $user_id . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $vote = $row["vote"];

    if ($vote)
    {
       return $vote;
    }
    else
    {
        return "failed";
    }
	}

	public function report_video(Request $request)
	{
		$program_id = $request->input("program_id");
		$reporter = $request->input("reporter");
		$reported = $request->input("reported");
		$videoURL = $request->input("videoURL");
		$reason = $request->input("reason");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}


    $sql_query = "select report_id from wp_reports where program_id = '" . $program_id . "' and reporter_id = '" . $reporter . "' and reported_id = '" . $reported . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $report = $row["report_id"];

    if ($report)
    {
       return "exists";
    }
    else
    {
        $sql_query = "insert into wp_reports (program_id, reporter_id, reported_id, reason) values ('" . $program_id . "' , '" . $reporter . "', '" . $reported . "', '" . $reason . "')";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            return "insert fail!";
        }

        mysql_close($link);

        if (!mail("yeplivetest123@gmail.com", "YepLive Report", "Video: " . $videoURL . "\r\nReporter id: " . $reporter . "\r\nReported user id: " . $reported. "\r\nReason: " . $reason. ""))
        {
            return "failed";
        }
        else return "reported";
    }
	}

	public function add_program(Request $request)
	{
		$channel_id = $request->input("channel_id");
		$user_id = $request->input("userid");
		$title = $request->input("title");
		$description = $request->input("description");
		$latitude = $request->input("latitude");
		$longitude = $request->input("longitude");
		$location = $request->input("location");
		$tags = $request->input("tags");
		$image_path = $request->input("imgurl");
		$vod_enable = $request->input("vod_enable");
		$start_time = date("Y-m-d H:i:s");
		//$end_time = date("Y-m-d H:i:s" , time() + $duration * 60);
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect err";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}
		$sql_query = "";
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
        return "banned -1";
    }
    else if($banned != "")
    {
        return "banned " . $row["bannedUntil"];
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
		return "mysql_query fail";	
	}

    $sql_query = "select max(program_id) program_id, channel_id from wp_program";
    if(!mysql_select_db(DB_NAME,$link))
    {
        return "mysql_select_db error";
    }


    $result = mysql_query($sql_query);

    if(!$result)
    {
        return "mysql_query error";
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
                return "mysql_query insert into wp_tags fail";
            }
        }

        $sql_query = "insert into wp_tags_program (tag_id, program_id) values ((select id from wp_tags where name='" . $tag . "'), '" . $program_id . "')";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            //"mysql_query insert into wp_tags_program fail";
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
            return "mysql_query error";
        }
    }

	
	mysql_free_result($result);
	
	mysql_close($link);
	
	return $program_id . "," . $channel_id;


	}

	public function update_program_info(Request $request)
	{
		$program_id = $request->input("program_id");
		$title = $request->input("title");
		$description = $request->input("description");
		$latitude = $request->input("latitude");
		$longitude = $request->input("longitude");
		$location = $request->input("location");
		$thumbnail_path = $request->input("thumbnail_path");
		$tags = $request->input("tags");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}


    if (!empty($title))
    {
        $sql_query = "update wp_program set title = '" . $title . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            return "title error!";
        }
    }

    if (!empty($description))
    {
        $sql_query = "update wp_program set description = '" . $description . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            return "description error!";
        }
    }

    if (!empty($latitude) && !empty($longitude))
    {
        $sql_query = "update wp_program set latitude = '" . $latitude . "', longitude = '" . $longitude . "', location = '" . $location . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            return "location error!";
        }
    }

    if (!empty($thumbnail_path))
    {
        $sql_query = "update wp_program set image_path = '" . $thumbnail_path . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            return "thumbnail error!";
        }
    }

    if (!empty($tags))
    {
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
                    return "mysql_query insert into wp_tags fail";
                }
            }

            $sql_query = "insert into wp_tags_program (tag_id, program_id) values ((select id from wp_tags where name='" . $tag . "'), '" . $program_id . "')";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                return "mysql_query insert into wp_tags_program fail";
            }

        }
    }
	}
	*/

