<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$program_id = $_GET['program_id'];
    $title = $_GET['title'];
    $description = $_GET['description'];
    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];
    $location = $_GET['location'];
    $thumbnail_path = $_GET['thumbnail_path'];
    $tags = $_GET['tags'];

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


    if (!empty($title))
    {
        $sql_query = "update wp_program set title = '" . $title . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            echo "title error!";
            exit;
        }
    }

    if (!empty($description))
    {
        $sql_query = "update wp_program set description = '" . $description . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            echo "description error!";
            exit;
        }
    }

    if (!empty($latitude) && !empty($longitude))
    {
        $sql_query = "update wp_program set latitude = '" . $latitude . "', longitude = '" . $longitude . "', location = '" . $location . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            echo "location error!";
            exit;
        }
    }

    if (!empty($thumbnail_path))
    {
        $sql_query = "update wp_program set image_path = '" . $thumbnail_path . "' where program_id = '" . $program_id . "'";
        $result = mysql_query($sql_query);

        if(!$result)
        {
            echo "thumbnail error!";
            exit;
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
                    echo "mysql_query insert into wp_tags fail";
                    exit;
                }
            }

            $sql_query = "insert into wp_tags_program (tag_id, program_id) values ((select id from wp_tags where name='" . $tag . "'), '" . $program_id . "')";
            $result = mysql_query($sql_query);
            if(!$result)
            {
                echo "mysql_query insert into wp_tags_program fail";
            }

        }
    }



?>