<?php
	
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$program_id = $_GET['program_id'];
    $reporter = $_GET['reporter'];
    $reported = $_GET['reported'];
    $videoURL = $_GET['videoURL'];
    $reason = $_GET['reason'];


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


    $sql_query = "select report_id from wp_reports where program_id = '" . $program_id . "' and reporter_id = '" . $reporter . "' and reported_id = '" . $reported . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $report = $row["report_id"];

    if ($report)
    {
       echo "exists";
    }
    else
    {
        $sql_query = "insert into wp_reports (program_id, reporter_id, reported_id, reason) values ('" . $program_id . "' , '" . $reporter . "', '" . $reported . "', '" . $reason . "')";
        $result = mysql_query($sql_query);
        if(!$result)
        {
            echo "insert fail!";
            exit;
        }

        mysql_close($link);

        if (!mail("yeplivetest123@gmail.com", "YepLive Report", "Video: " . $videoURL . "\r\nReporter id: " . $reporter . "\r\nReported user id: " . $reported. "\r\nReason: " . $reason. ""))
        {
            echo "failed";
        }
        else echo "reported";
    }

?>