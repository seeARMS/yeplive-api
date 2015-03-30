<?php
/*

Template name: login

*/

require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
	//if($user_ID)
		//echo "<script>alert(1);</script>";
	
	$user_name = sanitize_user($_POST['log']);
	
	$email_filt = validateEmail($user_name);
	
	
	if ( $email_filt ) {
		if ($user = get_user_by_email( $email_filt ) ) {
			$_POST['log'] = $user->data->user_login;
		}
	}

    if (isset($_COOKIE['bannedPermanently'])) $banned = -1;
    else if (isset($_COOKIE['bannedUntil'])) $banned = $_COOKIE['bannedUntil'];

	if(is_user_logged_in() )
	{
		if (!isset($_SESSION)) session_start();
		
		$current_user = wp_get_current_user();

        $rows = $wpdb->get_results("SELECT * FROM wp_warning_messages WHERE user_id IN (SELECT user_id FROM wp_user_yep WHERE wp_user_id = " . $current_user->ID . ")");
        if (count($rows) > 0)
        {
            $message = "Your video (" . $rows[0]->program_title . ") contravenes our terms and conditions. Next time you post a video of this nature, your account will be suspended";
            $wpdb->query("DELETE FROM wp_warning_messages WHERE user_id IN (SELECT user_id FROM wp_user_yep WHERE wp_user_id = " . $current_user->ID . ")");

            echo "<script>alert('" . $message . "')</script>";
        }


		$rows = $wpdb->get_results("SELECT *, TIME_TO_SEC(TIMEDIFF(bannedUntil, NOW())) as diff FROM wp_user_yep WHERE wp_user_id = " . $current_user->ID);
		$row = $rows[0];

        $banned = "";

        if ($row->bannedPermanently != 0)
        {
            $banned = -1;
            setcookie('bannedPermanently', '1', time() + 10000000000 , '/');
        }
        else if ($row->diff != "")
        {
            if (strpos($row->diff, "-") !== 0)
            {
                $banned = $row->bannedUntil;
                setcookie('bannedUntil', $banned, time() + $row->diff, '/');
            }
        }
        else if (isset($_COOKIE['bannedPermanently'])) $banned = -1;
        else if (isset($_COOKIE['bannedUntil'])) $banned = $_COOKIE['bannedUntil'];




        if($banned == "")
        {
            $_SESSION['user_id'] = $row->user_id;
            $_SESSION['user_group_id'] = $row->group_id;

            if ($row->facebook_id != null)
            {
                $_SESSION['facebook_login'] = 1;
                $_SESSION['facebook_id'] = $row->facebook_id;
                $_SESSION['facebook_name'] = $row->facebook_name;
                $_SESSION['facebook_email'] = $row->facebook_email;
                $_SESSION['facebook_accessToken'] = $row->facebook_access_token;
            }
            if ($row->twitter_id != null)
            {
                $_SESSION['twitter_login'] = 1;
                $_SESSION['twitter_id'] = $row->twitter_id;
                $_SESSION['twitter_name'] = $row->twitter_name;
                $_SESSION['twitter_oauthToken'] = $row->twitter_oauth_token;
                $_SESSION['twitter_oauthTokenSecret'] = $row->twitter_oauth_token_secret;
                $_SESSION['twitter_img'] = $row->twitter_img;
            }

            echo "<script>parent.document.location.href = '" . home_url() . "/';</script>";
        }
	}	
	 
	$secure_cookie = '';
	$interim_login = isset($_REQUEST['interim-login']);
	$customize_login = isset( $_REQUEST['customize-login'] );
	if ( $customize_login )
		wp_enqueue_script( 'customize-base' );
	
	
/*	$pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
	
	
	if (preg_match($pattern, $emailaddress) === 1) {
		// emailaddress is valid
	}*/
	
// 	$email_filt = validateEmail($_POST['log']);
	
	function validateEmail($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
// 	if ( $email_filt ) 
// 	{
// 	}
	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
 		$user_name = sanitize_user($_POST['log']);
		
// 		$email_filt = validateEmail($_POST['log']);
// 		if ( $email_filt ) {
// 			if ($user = get_user_by_email( $email_filt ) ) {
// 				if ( get_user_option('use_ssl', $user->ID) ) {
// 					$secure_cookie = true;
// 					force_ssl_admin(true);
// 				}
// 			}
// 		}
// 		else {
			if ( $user = get_user_by('login', $user_name) ) {
				if ( get_user_option('use_ssl', $user->ID) ) {
					$secure_cookie = true;
					force_ssl_admin(true);
				}
			}
//		}
	}
	
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( $secure_cookie && false !== strpos($redirect_to, '/') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		
		$redirect_to = home_url() . "/login/";
	}
	
	$reauth = empty($_REQUEST['reauth']) ? false : true;
	
	// If the user was redirected to a secure login form from a non-secure admin page, and secure login is required but secure admin is not, then don't use a secure
	// cookie and redirect back to the referring non-secure admin page. This allows logins to always be POSTed over SSL while allowing the user to choose visiting
	// the admin via http or https.
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;	

	$user = wp_signon('', $secure_cookie);

	
	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);
	
	if ( !is_wp_error($user) && !$reauth ) {
		if ( $interim_login ) {
			$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
				?>
	
				<?php if ( ! $customize_login ) : ?>
				<script type="text/javascript">setTimeout( function(){window.close()}, 8000);</script>
				<?php endif; ?>
				<?php do_action( 'login_footer' ); ?>
				<?php if ( $customize_login ) : ?>
					<script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
				<?php endif; ?>
				
	<?php		
			}
	
			if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
				// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
				if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
					$redirect_to = user_admin_url();
				elseif ( is_multisite() && !$user->has_cap('read') )
					$redirect_to = get_dashboard_url( $user->ID );
				elseif ( !$user->has_cap('edit_posts') )
					$redirect_to = admin_url('profile.php');
			}
			wp_safe_redirect($redirect_to);
			exit();
		}
	
		$errors = $user;
		// Clear errors if loggedout is set.
		if ( !empty($_GET['loggedout']) || $reauth )
			$errors = new WP_Error();
	
		// If cookies are disabled we can't log in even with a valid user+pass
		if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
			$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));
	
		// Some parts of this script use the main login form to display a message
		if		( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
			$errors->add('loggedout', __('You are now logged out.'), 'message');
		elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
			$errors->add('registerdisabled', __('User registration is currently not allowed.'));
		elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
			$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
		elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
			$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
		elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
			$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
		elseif	( $interim_login )
			$errors->add('expired', __('Your session has expired. Please log-in again.'), 'message');
		elseif ( strpos( $redirect_to, 'about.php?updated' ) )
			$errors->add('updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to experience the awesomeness.' ), 'message' );
	
		// Clear any stale cookies.
		if ( $reauth )
			wp_clear_auth_cookie();
	
		
	
		if ( isset($_POST['log']) )
			$user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(stripslashes($_POST['log'])) : '';
		$rememberme = ! empty( $_POST['rememberme'] );
?>

<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.min.js"></script>
<script language="javascript">

/*jQuery(document).ready( function () {
	document.getElementById("password").value = "";
	document.getElementById("username").value = "";
}*/

function oninit()
{
	document.getElementById("username").focus();
	document.getElementById("password").value = "";
	 
	window.parent.document.getElementById("register_popup").style.width = "690px";
	window.parent.document.getElementById("register_popup").style.height = "410px";

	window.parent.document.getElementById("loginFrame").style.height = "420px";
	window.parent.document.getElementById("loginFrame").style.width = "690px";
	var popup_left = screen.width / 2 - (690 / 2);
	var popup_top = screen.height / 2 - (420 / 2 ) - 100;
	window.parent.document.getElementById("register_popup").style.left = popup_left + "px";
	window.parent.document.getElementById("register_popup").style.top = popup_top + "px";

    if ("<?php echo $message ?>" != "")
    {
        alert("<?php echo $message ?>");
    }

    if ("<?php echo $banned ?>" != "")
    {
        if ("<?php echo $banned ?>" == "-1")
        {
            document.getElementById("loginResult").innerHTML = "You have been banned permanently";
        }
        else
        {
            document.getElementById("loginResult").innerHTML = "You have been banned until <?php echo $banned ?> (server time)";
        }
        document.getElementById("loginResult").style.display = "inline";
    }
}

function onLogin()
{
    if ('<?php echo $banned ?>' != "") return;

	var password = document.getElementById("password");
	var username = document.getElementById("username");
	
	
	if(username.value == "")
	{
		document.getElementById("loginResult").innerHTML = "Input user name.";
		username.focus();
		return;
	} 
	else if(password.value == "")
	{
		document.getElementById("loginResult").innerHTML = "Input password.";
		password.focus();
		return;
	} 
	
	document.getElementById("loginResult").style.display = "none";
	document.getElementById("wait").style.display = "inline";
	document.aform.submit();
	return;
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;
	
	if(username == "")
	{
		//document.getElementById("loginResult").innerText = "Log In failed!";
		document.getElementById("loginResult").innerText = "Input User Name!";
		document.getElementById("username").focus();
		return;
	}
	
	if(password == "")
	{
		//document.getElementById("loginResult").innerText = "Log In failed!";
		document.getElementById("loginResult").innerText = "Input Password!";
		document.getElementById("password").focus();
		return;
	}
	if(!(username.indexOf("@") > 0 && username.indexOf(".") > 0))
	{
		var email = username;
	}
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  	xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
		  
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
		  if(xmlhttp.responseText.indexOf("banned") > -1)
		  {
              document.getElementById("loginResult").innerText = "You have been banned until " + xmlhttp.responseText.split("banned ")[1] + " (server time)";
              return;
		  }
		  else if(xmlhttp.responseText == "fail")
		  {
			  document.getElementById("loginResult").innerText = "Login failed!";
		  }
          else
          {
              document.getElementById("password").value = "";
              //parent.document.getElementById("loginFrame").style.display = "none";
              //parent.document.getElementById("loginFrame").src = "";
              //parent.document.location.href = "<?php echo home_url() ?>/admin_page/";
              location.href = "<?php echo home_url() ?>/admin_page/";
          }
	    }
	  }
	
	xmlhttp.open("GET","<?php echo get_template_directory_uri(); ?>/ajaxLogin.php?username=" + username + "&password=" + password + "&email=" + email,true);
	xmlhttp.send();
}
function onCreateAccount()
{
	location.href = "<?php echo home_url() ?>/add_user/";
}
</script>

<script>
	
</script>
<html style="overflow:hidden;">
<style>
	.fancybox-outer {
		-webkit-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		-moz-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		
		margin: 0;
		background: #f9f9f9;
		color: #444;
		text-shadow: none;
		position: relative;
		/* -webkit-border-radius: 10px;*/
	}
	
	.fancybox-inner {
		width: 100%;
		height: 100%;
		padding: 0;
		margin: 0;
		position: relative;
		outline: none;
		overflow: hidden !important;
		}
	.lightbox-wrap {
		margin: 0 auto;
		background: #fff;
		
		/* -moz-border-radius: 10px;
		-ms-border-radius: 10px;
		-o-border-radius: 10px;
		border-radius: 10px; */
		text-align: center;
		overflow: hidden;
	}
	.join_header {
		/* -moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		
		position: relative;
		z-index: 1;
		padding: 30px 0 66px;
		border-bottom: 1px solid #d6d7d9;
		background-color: #fafafa; 
		box-shadow: 1px 0 2px rgba(0,0,0,0.15); */
	}
	.content {
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		/* border-radius: 10px; */
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		/* border-radius: 10px; */
	}
	input[type="text"]:hover, input[type="password"]:hover, input[type="email"]:hover,input[type="file"]:hover , textarea:hover, select:hover {
		border: 1px solid #91bde5;
}
input[type="text"], input[type="password"], input[type="email"], input[type="file"] ,textarea {
		font-weight: 300;
}
input[type="text"], input[type="password"],input[type="file"] ,input[type="email"], textarea, select {
		-webkit-background-clip: padding;
		-moz-background-clip: padding;
		background-clip: padding-box;
		-webkit-transition: box-shadow ease-out;
		-webkit-transition-delay: 0.3s;
		-moz-transition: box-shadow ease-out 0.3s;
		-o-transition: box-shadow ease-out 0.3s;
		transition: box-shadow ease-out 0.3s;
		padding: 10px;
		height: auto;
		border: 1px solid #b6b6b6;
		border-radius: 5px;
		color: #404040;
		font-size: 16px;
		line-height: 16px;
		-webkit-transition: border-color ease-out;
		-webkit-transition-delay: 0.3s;
		-moz-transition: border-color ease-out 0.3s;
		-o-transition: border-color ease-out 0.3s;
		transition: border-color ease-out 0.3s;
		}
button, input, select, textarea {
		font-size: 100%;
		vertical-align: middle;
		margin: 0;
}
body, button, input, select, textarea {
		font-family: 'Helvetica Neue',Arial,Helvetica, Verdana, sans-serif;
}
.social-login {
	width: 100%;
	height: 75px;
	position: relative;
}
.social-login-title {
	position: absolute;
	bottom: 35;
	left: 77;
	font-size: 24px;
	font-weight: 800;
}
.social-login-inform {
	position: absolute;
	bottom: 35;
	right: 93;
	font-size: 24px;
	font-weight: 500;
}
.twitter-login {

}
.facebook-login {
	height: 62px;
}
.user-signin-title {
	position: absolute;
	bottom: 30;
	left: 40;
	font-size: 24px;
	font-weight: 800;
}
.user-signin-prep {
	position: absolute;
	bottom: 30;
	left: 130;
	font-size: 24px;
	font-weight: 500;
    width: 90px;
}
.user-signin-inform {
	position: absolute;
	bottom: 30;
	right: 32;
	font-size: 24px;
	font-weight: 800;
    width: 100px;
}
.user-name-title {
	float: left;
	padding-left: 41px;
}
.password-title {
	float: left;
	padding-left: 41px;
}
.password {
	padding-top: 20px;
}
.sign-button {
	padding-top: 31px;
	width: 100%;
	float: left;
}
.forgot-password-button {
	padding-right: 20px;
	float: left;
	padding-left: 40px;
	padding-top: 16px;
	text-decoration: none;
	cursor: pointer;
}
.forgot-password-button:hover {
	text-decoration: underline;
	color: red;
}
.forgot-password:hover {
/* 	text-decoration: underline; */
/* 	color: green; */
}
.login-accurate {
	width: 100%;
	height: auto;
	display: block;
}
.forgot-password {
	display: none;
	width: 100%;
	height: auto;
/* 	margin-top: -11px; */
}
.forgot-password-title {
	width: 100%;
	height: 41px;
	font-size: 23px;
}
.forgot-password-notification {
	
}

.password-reinput {
	margin-top: 8px;
}
.notification {
	font-size: 14px;
	color: green;
	line-height: 18px;
}
.email-input {
	padding-top: 20px;
}
.password-reset {
	padding-top: 17px;
}
.forgot-password-cancel {
	padding-top: 13px;
	cursor: pointer;
}
.forgot-password-cancel:hover {
	text-decoration: underline;
	color: red;
}

input[type="text"], textarea, keygen, select, button, isindex {
		margin: 0em;
		font: -webkit-small-control;
		color: initial;
		letter-spacing: normal;
		word-spacing: normal;
		text-transform: none;
		text-indent: 0px;
		text-shadow: none;
		display: inline-block;
		text-align: start;
}
input[type="text"], textarea, keygen, select, button, isindex, meter, progress {
		-webkit-writing-mode: horizontal-tb;
}
input[type="file"] {
float: left;
margin-bottom: 5px;
width: 50%;
opacity: 0;
-webkit-box-align: baseline;
color: inherit;
text-align: start;
-webkit-appearance: initial;
padding: initial;
background-color: initial;
border: initial;
border-image: initial;
-webkit-rtl-ordering: logical;
-webkit-user-select: text;
cursor: auto;
}
</style>

<body onload="oninit();"  style="overflow:hidden; vertical-align:middle; width:100%;height:100%;background-position-x: 0px;
									background-position-y: 80px;
									background-size: initial;
									background-repeat-x: repeat;
									background-repeat-y: repeat;
									background-attachment: initial;
									background-origin: initial;
									background-clip: initial;
									background-color: initial;
									background-position: 0 80px;
									margin:0px;">
<form name="aform" method="post" style="overflow:hidden;" action="<?php echo home_url() ?>/login/">
			
	<input type="hidden" name="redirect_to" id="redirect_to" value="<?php echo home_url() ?>/admin_page">
	<input type="hidden" name="interim-login" value="1" />
	<input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
	<input type="hidden" name="customize-login" value="1" />
	<input type="hidden" name="testcookie" value="1" />
							
	<div style="width: 690px; height: 420px; display: block; overflow: visible; ">
		<div class="fancybox-outer" style="padding: 0px; width: auto; height: auto; ">
			<div class="fancybox-inner" style="width: 690px; overflow: auto;">
				<div class="lightbox-wrap" align="center" style="text-align: center;">
					<div class="join_header" align="center" style="text-align: center;">
						<div style="width: 100% ; background-color: red; height: 5px;"></div>
						<div class="content" style="margin-top:20px;float: left; width: 350px; height: 387px;" align="center">
						
							<div style="width: 100%; height: 75px; position: relative;">
                                <p style="font-size: 24px;"><strong>Sign in</strong> through <strong>YEPLIVE</strong></p>
							</div>
							<div id="login-inform" class="login-accurate">
								<div class="user-name"><span class="user-name-title">Email or Username</span></div>
								<div class="user-name-input"><input type="text" id="username" name="log" style="width:80%; height: 8%; padding-top: 0px; padding-bottom: 0px;" onkeypress="document.getElementById('loginResult').innerHTML = '';"></div>
								<div class="password"><span class="password-title">Password</span></div>
								<div class="password-input"><input type="password" id="password" name="pwd" style="width:80%; height: 8%; padding-top: 0px; padding-bottom: 0px;" onkeypress="document.getElementById('loginResult').innerHTML = '';"></div>
								<div class="sign-button">
									<img src="<?php echo get_template_directory_uri()?>/images/login-image.png" style="width:185px; height:48px; float:left; padding-left:37px; cursor: pointer;" onclick="onLogin();"></img>
								</div>
								<div class="forgot-password-button" onclick="forgotYourPassword();">Forgot your password?</div>
							</div>
<!-- forgot password  -->	<div id="forgot-password-form" class="forgot-password">
								<div class="forgot-password-title">Forgot Your Password?</div>
								<div class="forgot-password-notification">
									<span class="notification">Happens all the time. Enter the email </br> address associated with your account and </br> we will send you a new password.</span>
									
								</div>
								<div class="email-input"><input type="text" id="email" name="email-input" style="width:80%; height: 9%; padding-top: 0px; padding-bottom: 0px; line-height: 21px; border-radius: 7px; font-size: 18px;" placeholder="Email Address"></div>
								<!--  <div class="password-reinput"><input type="password" id="repassword" name="repassword" style="width:80%; height: 9%; line-height: 21px; border-radius: 7px; font-size: 18px;" placeholder="Password"></div> -->
								<div class="password-reset"><input type="button" name="password_reset" value="Reset Password" style="width: 80%; height: 10%; border-radius: 7px; cursor: pointer;" onclick="passwordReset();"></div>
								<div class="forgot-password-cancel" onclick="forgotPassCancel();">Cancel</div>
							</div>			
						</div>
						<div style="width: 340px; float: right; margin-top: 20px;">
							<div class="social-login">
                                <p style="font-size: 24px;"><strong>Sign in</strong> socially</p>
                            </div>
							<div class="facebook-login">
								<?php
									if ($_SESSION['facebook_login'] == '1')  {
									?>
										<img id="imgFacebookVerified" title="facebook Logined" onclick="fn_facebookLogin();" style="width:183px;height:46px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/facebook-logined.png">
									<?php
									} else { 
									?>
										<img id="imgFacebook" title="facebook Login" onclick="fn_facebookLogin();" style="width:183px;height:46px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/facebook-login.png">
									<?php
									} 
								?>
							</div>
							<div class="twitter-login">
								<?php
									if ($_SESSION['twitter_login'] == '1')  {
									?>
										<img id="imgTwitterVerified" title="twitter Logined" onclick="fn_twitterLogin();" style="width:183px;height:46px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/twitter-logined.png">
									<?php
									} else { 
									?>
										<img id="imgTwitter" title="twitter Login" onclick="fn_twitterLogin();" style="width:183px;height:46px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/twitter-login.png">
									<?php
									} 
								?>
							</div>
							<div style="width: 100%; height: 149px; position: relative;">
								<img id="wait" style="position: absolute; bottom: 30; left: 0; width:35px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
								<span id="loginResult" style="color:red; position: absolute; bottom: 26; left: 0;"></span>
							</div>
						</div>
					</div>
					
				</div>
				<div style="width: 100% ; background-color: red; height: 5px; position: absolute; bottom: 53;"></div>
				<div style="width: 100% ; background-color: #E5E5E5; height: 53px; position: absolute; bottom: 0;">
		        <div style="float: right; position: relative; width: 400px; <?php if($banned == "") echo "display: block;"; else echo "display: none;"; ?>">
						<span style="position: absolute; left: 85; top: 15;">Don't have an account?</span>
						<a style="position: absolute; right: 80; top: 15; cursor: pointer; color: blue;" href="<?php echo home_url() ?>/add_user/" >Register</a>
						<img src="<?php echo get_template_directory_uri()?>/images/triangle.png" style="float: right; padding-right: 67px; padding-top: 18px;">
					</div>
				</div>
			</div>
			
		</div>
	</div>
</form>

<script type="text/javascript" >
	function forgotYourPassword () {
        if ("<?php echo $banned ?>" != "") return;
		//$('body').find(".login-accurate").attr("display","none");
		document.getElementById("login-inform").style.display = "none";
		document.getElementById("forgot-password-form").style.display = "block";
	}
	
	function forgotPassCancel () {
		document.getElementById("forgot-password-form").style.display = "none";
		document.getElementById("login-inform").style.display = "block";
		document.getElementById("loginResult").innerHTML = "";
		
	}
	
	function passwordReset () {

		/*var repassword = document.getElementById("repassword").value;
		if ( repassword == "") {
			document.getElementById("loginResult").style.display = "inline";
			document.getElementById("loginResult").innerHTML = "Please enter a new password.";
			document.getElementById("repassword").focus();
			return;
		}*/
		
		document.getElementById("wait").style.display = "inline";
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		  	xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5
		  	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
		{
			if(xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
			    
			    document.getElementById("wait").style.display = "none";
				if(xmlhttp.responseText == "email_error")
				{
					document.getElementById("loginResult").style.display = "inline";
					document.getElementById("loginResult").innerHTML = "Please enter a valid email.";
					document.getElementById("email").value = '';
					document.getElementById("email").focus();
					return;
				}
				else if(xmlhttp.responseText == "email_exist")
				{
					document.getElementById("loginResult").style.display = "inline";
					document.getElementById('loginResult').innerHTML = 'Email address does not  exists.</br> Please enter a valid email.';
					document.getElementById('email').value = '';
					document.getElementById('email').focus();
					return;
				}
				else if(xmlhttp.responseText == "success")
				{
					alert("We have sent you an email with a link to reset your password.");
					//document.getElementById("repassword").value = "";
					parent.document.location.href = "<?php echo home_url()?>";
					return;
				}
				else if(xmlhttp.responseText == "unsuccess") {
					alert("please try it again.");
				}
			}
		}  

		var email = document.getElementById("email").value;
		if(!(email.indexOf("@") > 0 && email.indexOf(".") > 0))
		{
			document.getElementById("loginResult").style.display = "inline";
			document.getElementById("loginResult").innerHTML = "Please enter a valid email.";
			document.getElementById("email").value = '';
			document.getElementById("email").focus();
			document.getElementById("wait").style.display = "none";
			return;
		}
		var eindex = email.indexOf("@");
		var dotindex = email.indexOf(".") + 1;
		
		xmlhttp.open("GET","<?php echo home_url() ?>/forgot_password/?email=" + email + "&eindex=" + eindex + "&dotindex=" + dotindex,true);
		xmlhttp.send();
	} 

	function getWindowPosition(x_size,y_size)
	{	
		var xpos = ( window.screen.width -  x_size  ) / 2;
		var ypos = ( window.screen.height - y_size )  / 2;
		var props = 'width='+x_size;
		props += (',height=' + y_size);
		props += (',top='+ypos);
		props += (',left='+xpos);
		return props;
	} 
	
	var myFacebookWindow = null;
	var myFacebookInterval = null;
	
	function fn_facebookLogin()
	{
        if ("<?php echo $banned ?>" != "") return;

		if (myFacebookWindow != null)
		{
			myFacebookWindow.close();	
		}
		if (myFacebookInterval != null)
		{
			clearInterval(myFacebookInterval);
		}
		
		var URL = "<?php echo home_url()?>/facebook_login/";
		var props = getWindowPosition(450, 250);
		myFacebookWindow = window.open(URL, "", props);
	
		myFacebookInterval = setInterval("fn_checkFacebookSession()", 3000);
		
	}
	
	function fn_checkFacebookSession()
	{
		jQuery.ajax({
			type: "POST",
		  	url: "<?php echo home_url()?>/facebook_checkLogin/",
		  	success: function(msg) {
			  	if (msg != '0')
				{
					
					clearInterval(myFacebookInterval);
					myFacebookWindow.close();	
					/*
					$('#imgFacebook').css('display', 'none');
					$('#imgFacebookVerified').css('display', 'inline');
					
					if (msg == '3')
					{
						$('#imgTwitter').css('display', 'none');
						$('#imgTwitterVerified').css('display', 'inline');
					}*/
					parent.location.href = "<?php echo home_url()?>";
				}
		  	}
		});	
	}
	
	var myTwitterWindow = null;
	var myTwitterInterval = null;
	
	function fn_twitterLogin()
	{
        if ("<?php echo $banned ?>" != "") return;

		if (myTwitterWindow != null)
		{
			myTwitterWindow.close();	
		}
		if (myTwitterInterval != null)
		{
			clearInterval(myTwitterInterval);
		}
		
		var URL = "<?php echo home_url()?>/twitter_login/";
		var props = getWindowPosition(800, 500);
		myTwitterWindow = window.open(URL, "", props);
	
		myTwitterInterval = setInterval("fn_checkTwitterSession()", 3000);
		
	}
	
	function fn_checkTwitterSession()
	{
		$.ajax({
			type: "POST",
		  	url: "<?php echo home_url()?>/twitter_checkLogin/",
		  	success: function(msg) {
			  	if (msg != '0')
				{
					
					clearInterval(myTwitterInterval);
					myTwitterWindow.close();	
					/*
					$('#imgTwitter').css('display', 'none');
					$('#imgTwitterVerified').css('display', 'inline');
					
					if (msg == '3')
					{
						$('#imgFacebook').css('display', 'none');
						$('#imgFacebookVerified').css('display', 'inline');
					}*/
					parent.location.href = "<?php echo home_url()?>";
					
				}
		  	}
		});	
	}
</script>
<?php
	if(isset($_POST['log']) && isset($_POST['pwd']))
	{?>
		<script>document.getElementById("loginResult").innerHTML = "login failed!";</script>
	<?php } 
?>
</body>
</html>