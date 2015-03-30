<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

	$menuNames = $_GET['menuNames'];
	$flags = $_GET['isCateOrSub'];
	
	$menuNames = str_replace("^^^", "&", $menuNames);
	$mns = explode(",^/,", $menuNames);
	$fgs = explode(",", $flags);
	
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if (!$link) {
		echo 'Could not connect: ' . mysql_error();
		return;
	}
	
	for($i = 0; $i < count($mns) - 1; $i++)
	{
		$m = $i - 1;
		if($fgs[$i] == "1")
		{
			$sql_query = "select category_id from wp_category where category_name = '" . $mns[$m] . "'";
			if (!mysql_select_db(DB_NAME, $link)) {
				echo 'Could not select database';
				exit;
			}
			$result = mysql_query($sql_query);
			
			if(!$result)
			{
				//echo 'Could not connect: ' . mysql_error();
				//return;
			}
			
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			
			$category_id = $row['category_id'];
			$enteredInForLoop = false;
			while($fgs[$i] == "1")
			{
				$sql_query = "select category_id,channel_name from wp_channel where category_id = '" . $category_id . "' and channel_name = '" . $mns[$i] . "'";
				if (!mysql_select_db(DB_NAME, $link)) {
					echo 'Could not select database';
					exit;
				}
				$result = mysql_query($sql_query);
				if(!$result)
				{
					//echo 'Could not connect: ' . mysql_error();
					//return;
				}
				$row = mysql_fetch_array($result);
				mysql_free_result($result);
				
				if(!$row)
				{
					$sql_query = "insert into wp_channel(category_id,channel_name) values('" . $category_id . "' , '" . $mns[$i] ."')";
					if (!mysql_select_db(DB_NAME, $link)) {
						echo 'Could not select database';
						exit;
					}
					$result = mysql_query($sql_query);
					if(!$result)
					{
						//echo 'Could not connect: ' . mysql_error();
						//return;
					}
				}
				$i++;
				$enteredInForLoop = true;
			}	
			if($enteredInForLoop == true)
				$i--;
		}
		else
		{
			$sql_query = "select category_id from wp_category where category_name = '" . $mns[$i] . "'";
			if (!mysql_select_db(DB_NAME, $link)) {
				echo 'Could not select database';
				exit;
			}
			$result = mysql_query($sql_query);
			if(!$result)
			{
				//echo 'Could not connect: ' . mysql_error();
				//return;
			}
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			
			if(!$row)
			{
				$sql_query = "insert into wp_category(category_name) values('" . $mns[$i] ."')";
				if (!mysql_select_db(DB_NAME, $link)) {
					echo 'Could not select database';
					exit;
				}
				$result = mysql_query($sql_query);
				if(!$result)
				{
					//echo 'Could not connect: ' . mysql_error();
					//return;
				}
				if($mns[$i] == "Home")
					continue;
				$sql_query = "select max(category_id) category_id from wp_category ";
				
				if (!mysql_select_db(DB_NAME, $link)) {
					echo 'Could not select database';
					exit;
				}
				
				$result = mysql_query($sql_query);
				if(!$result)
				{
					//echo 'Could not connect: ' . mysql_error();
					//return;
				}
				$cate_id = mysql_fetch_array($result);
				mysql_free_result($result);
				
				$sql_query = "insert into wp_channel(category_id , channel_name) values('" . $cate_id['category_id'] ."','" . $mns[$i] ."')";
				if (!mysql_select_db(DB_NAME, $link)) {
					echo 'Could not select database';
					exit;
				}
				$result = mysql_query($sql_query);
				if(!$result)
				{
					//echo 'Could not connect: ' . mysql_error();
					//return;
				}
				
			}
			
		}
			
	}
	
	$sql_query = "select channel_id from wp_channel";
	if (!mysql_select_db(DB_NAME, $link)) {
		echo 'Could not select database';
		exit;
	}
	$result = mysql_query($sql_query);
	
	
	$channel_ids = "\\//\\//";
	while($row = mysql_fetch_array($result))
	{
		$channel_ids = $channel_ids . $row['channel_id'] . ",";
	}
	
	mysql_free_result($result);
	mysql_close($link);
	$response = "success" . $channel_ids;
	
	//$response = "success";
	echo $response;
?>