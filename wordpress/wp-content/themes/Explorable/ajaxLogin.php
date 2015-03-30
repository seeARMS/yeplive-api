<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
	include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
	
	$posted_username = $_GET['username'];
	$posted_password = $_GET['password'];
	
// 	if ( isset($email) )
// 	{
// 		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
// 		if(!$link)
// 		{
// 			echo "mysql_connect err";
// 			exit;
// 		}
// 		if(!mysql_select_db(DB_NAME,$link))
// 		{
// 			echo "mysql_select_db error";
// 			exit;
// 		}
// 		$sql_query = "";
// 		$sql_query = "select user_login from wp_users where user_email = '" . $email . "'";
// 		$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
// 		if ( $result > 0 )
// 		{
// 			$posted_username = $result->user_login;
// 		}
// 	}

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

    $sql_query = "select bannedUntil from wp_user_yep where wp_user_id IN (SELECT ID from wp_users where user_login=" . $posted_username . ")";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    if(!is_null($row["bannedUntil"]))
    {
        echo "banned " . $row["bannedUntil"];
        exit;
    }
	
	$user = get_user_by( 'login', $posted_username );
	if ( $user && wp_check_password( $posted_password, $user->data->user_pass, $user->ID) )
		echo $user->ID;
	else
		echo "fail";
	
?>