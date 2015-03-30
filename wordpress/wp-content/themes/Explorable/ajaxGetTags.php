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
		echo "mysql_error";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_error";
		exit;
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

    echo $tags;


?>