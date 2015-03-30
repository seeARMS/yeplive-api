<?php
/*

Template name: forgot_password

*/
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
	
	//$emailAddr = substr($_GET['email'],0, $_GET['eindex']) . "@" . substr($_GET['email'] , $_GET['eindex'] , strlen($_GET['email']));
	$emailAddr = $_GET['email'];


	$email = $wpdb->escape($emailAddr);
	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {
		//echo "Please enter a valid email.";
		echo "email_error";
		
		exit();
	}
	
	$sql_query = "select user_email from wp_users where user_email = '" . $email . "'";
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	if(count($result) == 0)
	{
		echo "email_exist";
		exit();
	}
	
	$sql_query = "select user_login , ID from wp_users where user_email = '" . $email . "'";
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	$username = $result[0]->user_login;
	$ID = $result[0]->ID;
	//$random_password = wp_generate_password( 12, false );
	//$random_password = $_GET['repassword'];
	//$password = wp_hash_password($random_password);
	
	/*$sql_query = "UPDATE wp_users SET user_pass = '" . $password . "' WHERE user_email = '" . $email . "'";
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_insert error";
		exit();
	}*/
	
	$sendURL = base64_encode($ID);
	
	$from = get_option('admin_email');
	$headers = 'From: '. $from . "\r\n";
	$subject = "Forgot your Yeplive password?";
	
	$msg = "Yeplive received a request to reset the password for your \"$username\" account" . "\r\n";
	$msg .= "To reset your password, click on the url below:" . "\r\n";
	$msg .= "Or copy and paste the URL into your browser" . "\r\n";
	$msg .= "\r\n";
	$msg .= DOMAIN_ROOT."forgotten-password-reset/?user_id=$sendURL";
//	$msg .= "http://localhost/wordpress/forgotten-password-reset/?user_id=$sendURL";
	//$msg .= "http://localhost/wordpress/forgotten-password-reset/?user_id=$sendURL";

    $return = mail($email, $subject, $msg, $headers);

    if ( $return ) {
		echo "success";
	}
	else {
		echo "unsuccess";
	}
	
	exit();		

?>
