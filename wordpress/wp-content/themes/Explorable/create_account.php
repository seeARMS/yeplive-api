<?php
/*

Template name: create_account

*/
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
	
 $emailAddr = substr($_GET['email'],0, $_GET['eindex']) . "@" . substr($_GET['email'] , $_GET['eindex'] , strlen($_GET['email']));



	$username = $wpdb->escape($_GET['username']);
	if(empty($username)) {
		//echo "User name should not be empty.";
		echo "username_empty";
		exit();
		
	}
	
	$email = $wpdb->escape($emailAddr);
	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {
		//echo "Please enter a valid email.";
		echo "email_error";
		
		exit();
	}
	
	$sql_query = "select user_login from wp_users where user_login = '" . $username . "'";
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	if(count($result) > 0)
	{
		echo "username_exist";
		exit();
	}
	
	$sql_query = "select user_email from wp_users where user_email = '" . $email . "'";
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	if(count($result) > 0)
	{
		echo "email_exist";
		exit();
	}
	
	//$random_password = wp_generate_password( 12, false );
	
	$random_password = $_GET['password'];
	$status = wp_create_user( $username, $random_password, $email );
	if(is_wp_error($status))
	{
		echo "username_exist";
		exit();		
	}
	
	
	
		
		$from = get_option('admin_email');
		$headers = 'From: '.$from . "\r\n";
		$subject = "Registration successful";
		$msg = "Registration successful.\nYour login details\nUsername: $username\nPassword: $random_password";
		

		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			echo "mysql_connect error";
			exit();
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			echo "mysql_select_db error";
			exit();
		}
		$sql_query = "select ID from wp_users where user_login = '" . $_GET['username'] . "' and user_email = '" . $emailAddr . "'";
		$result = mysql_query($sql_query);
		if(!$result)
		{
			echo "mysql_query error";
			exit();
		}
		$row = mysql_fetch_array($result);
		mysql_free_result($result);

		$user_id = $row['ID'];

		//$teststr = $_GET['imgUrl'];
		//$substr = home_url();
		//$resultStr = substr($teststr , strlen($substr), strlen($teststr));

		if (!isset($_SESSION)) session_start();

//		if (isset($_SESSION['user_id']))
//		{
//			//$sql_query = "UPDATE wp_user_yep SET group_id = 1, picture_path = '" . $resultStr . "', wp_user_id = '" . $user_id . "' WHERE user_id = " . $_SESSION['user_id'];
//			$sql_query = "UPDATE wp_user_yep SET group_id = 3, wp_user_id = '" . $user_id . "' WHERE user_id = " . $_SESSION['user_id'];
//			$result = mysql_query($sql_query);
//			if(!$result)
//			{
//				echo "mysql_insert error";
//				exit();
//			}
//		}
//		else
		{
			//$sql_query = "insert into wp_user_yep(group_id,picture_path,wp_user_id) values('1' , '" . $resultStr . "' , '" . $user_id . "')";
			$sql_query = "insert into wp_user_yep(group_id,wp_user_id) values('3' , '" . $user_id . "')";
			$result = mysql_query($sql_query);
			if(!$result)
			{
				echo "mysql_insert error";
				exit();
			}
		}
		mysql_close($link);
		echo "success";
		//wp_mail( $email, $subject, $msg, $headers );
		//exit;
	

?>