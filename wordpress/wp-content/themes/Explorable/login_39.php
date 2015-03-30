<?php
/*

Template name: login

*/

require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
	//if($user_ID)
		//echo "<script>alert(1);</script>";
	
	if(is_user_logged_in())
	{
		if (!isset($_SESSION)) session_start();
		
		$current_user = wp_get_current_user();
		
		$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE wp_user_id = " . $current_user->ID);
		$row = $rows[0];
		
		$_SESSION['user_id'] = $row->user_id;
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
	 
	$secure_cookie = '';
	$interim_login = isset($_REQUEST['interim-login']);
	$customize_login = isset( $_REQUEST['customize-login'] );
	if ( $customize_login )
		wp_enqueue_script( 'customize-base' );
	
	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_user_by('login', $user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
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

function oninit()
{
	document.getElementById("username").focus();

}
function onLogin()
{
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
		  if(xmlhttp.responseText != "fail")
		  {
			  //alert(0);
			  //parent.document.getElementById("loginFrame").style.display = "none";
			  //parent.document.getElementById("loginFrame").src = "";
			  //parent.document.location.href = "<?php echo home_url() ?>/admin_page/";
			  location.href = "<?php echo home_url() ?>/admin_page/";
		  }
		  else
		  {
			  document.getElementById("loginResult").innerText = "Login failed!";
		  }
	    }
	  }
	xmlhttp.open("GET","<?php echo get_template_directory_uri(); ?>/ajaxLogin.php?username=" + username + "&password=" + password,true);
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
		-webkit-border-radius: 10px;
		
		border-radius: 10px;
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
		
		-webkit-border-radius: 10px;
		-moz-border-radius: 10px;
		-ms-border-radius: 10px;
		-o-border-radius: 10px;
		border-radius: 10px;
		text-align: center;
		overflow: hidden;
	}
	.join_header {
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		
		position: relative;
		z-index: 1;
		padding: 30px 0 66px;
/* 		padding: 32px 0px 124px 0px; */
		border-bottom: 1px solid #d6d7d9;
		background-color: #fafafa;
		box-shadow: 1px 0 2px rgba(0,0,0,0.15);
	}
	.content {
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		background-color: #f0f2f4;
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
							
	<div style="width: 410px; height: 900px; display: block; overflow: visible; border-radius:10px;">
		<div class="fancybox-outer" style="padding: 0px; width: auto; height: auto; border-radius:10px;">
			<div class="fancybox-inner" style="width: 410px; height: auto; overflow: auto; border-radius:10px;">
				<div class="lightbox-wrap" align="center" style="text-align: center; border-radius:10px;">
					<div class="join_header" align="center" style="text-align: center;border-radius:10px;">
						<img src="<?php echo get_template_directory_uri()?>/images/logo.png">
						
						<div class="content" style="margin-top:20px;border-radius:10px;" aign="center">
							<table border="0" cellspacing="10" cellpadding="10" style="border-collapse: collapse; width:100%;height:330px;border-radius:10px;">
								<colgroup>
									<col width="35%">
									<col width="65%">
								</colgroup>
								<tr>
									<td align="right">
										<span>User Name</span>
									</td>
									<td>
										<input type="text" id="username" name="log" style="width:90%;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
									</td>
								</tr>
								<tr>
									<td align="right" >
										<span>Password</span>
									</td>
									<td>
										<input type="password" style="width:90%;" id="password" name="pwd" onkeypress="document.getElementById('loginResult').innerHTML = '';">
									</td>
								</tr>
								 
								<tr>
									
									<td colspan="2" align="center">
										<input type="button" onclick="onLogin();" value="Login" style="color:#ffffff; letter-spacing: 3px; width:220px; height:45px; border-radius:25px;
										background: url('<?php echo get_template_directory_uri()?>/images/login1.png'); ">
									</td>
								</tr>
								<tr>
									<td colspan="2" align="center">
										<input type="button" onclick="onCreateAccount();"  value="Create Account" style="word-spacing:10px; color:#ffffff; letter-spacing: 3px; width:220px; height:45px; border-radius:25px;
										background: url('<?php echo get_template_directory_uri()?>/images/login1.png');" onclick="location.href = '<?php echo home_url() ?>/add_user/';">
									</td>
								</tr>
								
								<tr>
									<td colspan="2" align="center">
										<?php
										if ($_SESSION['facebook_login'] == '1')  {
										?>
											<img id="imgFacebookVerified" title="facebook Logined" onclick="fn_facebookLogin();" style="width:220px;height:45px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/facebook-logined.png">
										<?php
										} else { 
										?>
											<img id="imgFacebook" title="facebook Login" onclick="fn_facebookLogin();" style="width:220px;height:45px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/facebook-login.png">
										<?php
										} 
										?>
									</td>
								</tr>
								
								<tr>
									<td colspan="2" align="center">
									<?php
										if ($_SESSION['twitter_login'] == '1')  {
										?>
											<img id="imgTwitterVerified" title="twitter Logined" onclick="fn_twitterLogin();" style="width:220px;height:45px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/twitter-logined.png">
										<?php
										} else { 
										?>
											<img id="imgTwitter" title="twitter Login" onclick="fn_twitterLogin();" style="width:220px;height:45px; cursor: pointer;" src="<?php echo get_template_directory_uri()?>/images/twitter-login.png">
										<?php
										} 
										?>
									</td>
								</tr>
								 
								<tr>
									<td colspan="2" align="center" style="height:25px;margin:0px; padding:0px;">
										<img id="wait" style="width:35px;height:25px;margin:0px; padding:0px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
										<span id="loginResult" style="color:red;"></span>
									</td>
								</tr>
							</table>
						</div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</form>

<script language="javascript">
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