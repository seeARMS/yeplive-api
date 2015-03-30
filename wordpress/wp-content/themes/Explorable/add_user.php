<?php
/*

Template name: add_user

*/
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
//if($user_ID)
//{
//
//	wp_redirect(home_url()); exit;
//}

?>

<html lang="en-US" >
<head>

<script language="javascript">

function onBtnCreate()
{
	var email = document.getElementById("email").value;
	var userEmailVerify = document.getElementById("emailverify").value;
	if ( email == "") {
		document.getElementById("loginResult").innerHTML = "Input a email.";
		document.getElementById("email").focus();
		return;
	}
	if ( userEmailVerify == "") {
		document.getElementById("loginResult").innerHTML = "Verify an email.";
		document.getElementById("emailverify").focus();
		return;
	}
	if ( email != userEmailVerify ) {
		document.getElementById("loginResult").innerHTML = "Verify an email again.";
		document.getElementById("emailverify").value = "";
		document.getElementById("emailverify").focus();
		return;
	}
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;
	
	if ( username == "" ) {
		document.getElementById("loginResult").innerHTML = "Input a username";
		document.getElementById("username").focus();
		return;
	}

    if ( !username.match(/^.*[a-zA-Z]+.*$/))
    {
        document.getElementById("loginResult").innerHTML = "Username should contain at least one letter";
        document.getElementById("username").focus();
        return;
    }

    if ( !username.match(/^[a-zA-Z0-9_]+[a-zA-Z]+[a-zA-Z0-9_]*$/) ) {
        document.getElementById("loginResult").innerHTML = "You can use only letters, digits and underscore";
        document.getElementById("username").focus();
        return;
    }

    if ( username.length < 5 ) {
        document.getElementById("loginResult").innerHTML = "Username should consists of minimum 5 symbols";
        document.getElementById("username").focus();
        return;
    }

    if ( username.length > 20 ) {
        document.getElementById("loginResult").innerHTML = "Username should consists of maximum 20 symbols";
        document.getElementById("username").focus();
        return;
    }

	if ( password == "" ) {
		document.getElementById("loginResult").innerHTML = "Input a password";
		document.getElementById("password").focus();
		return;
	}
	
	document.getElementById("loginResult").style.display = "none";
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
			if(xmlhttp.responseText == "username_error")
			{
				document.getElementById("loginResult").style.display = "inline";
				document.getElementById("loginResult").innerHTML = "User name should not be empty.";
				document.getElementById("username").focus();
				return;
			}
			else if(xmlhttp.responseText == "email_error")
			{
				document.getElementById("loginResult").style.display = "inline";
				document.getElementById("loginResult").innerHTML = "Please enter a valid email.";
				document.getElementById("email").value = '';
				document.getElementById("email").focus();
				return;
			}
			else if(xmlhttp.responseText == "username_exist")
			{
				document.getElementById("loginResult").style.display = "inline";
				document.getElementById('loginResult').innerHTML = 'Username already exists. Please try another one.';
				document.getElementById('username').value = '';
				document.getElementById('username').focus();
				return;
			}
			else if(xmlhttp.responseText == "email_exist")
			{
				document.getElementById("loginResult").style.display = "inline";
				document.getElementById('loginResult').innerHTML = 'Email address already exists. Please try another one.';
				document.getElementById('email').value = '';
				document.getElementById('emailverify').value = '';
				document.getElementById('email').focus();
				return;
			}
			else if(xmlhttp.responseText == "success")
			{
				alert("Register Success!");
				document.getElementById('loginResult').innerHTML = "Register Success!";
				parent.document.location.href = "<?php echo home_url()?>";
				return;
			}
		}
	  }
	var imgurl = document.getElementById("imgUrl").value;  
	//var username = document.getElementById("username").value;
	//var email = document.getElementById("email").value;
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
	//alert(email.split("@")[0] + email.substring(eindex,dotindex+1) + email.split(".")[1]);
	
	xmlhttp.open("GET","<?php echo home_url()?>/create_account?username=" + username + "&email=" + email + "&password=" + password + "&eindex=" + eindex + "&dotindex=" + dotindex,true);
	xmlhttp.send();
	//document.aform.submit();

	
}

function oninit () {

	/*window.parent.document.getElementById("register_popup").style.width = "410px";
	window.parent.document.getElementById("register_popup").style.height = "500px";
	
	window.parent.document.getElementById("loginFrame").style.height = "500px";
	var popup_left = screen.width / 2 - (410 / 2);
	var popup_top = screen.height / 2 - (450 / 2 ) - 100;
	window.parent.document.getElementById("register_popup").style.left = popup_left + "px";
	window.parent.document.getElementById("register_popup").style.top = popup_top + "px";*/
	
}

</script>

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
		-webkit-border-radius: 4px;
		
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
		
		text-align: center;
		overflow: hidden;
	}
	.join_header {
		
		position: relative;
		background-color: #fff;
	}
	.content {
		
	}
	
.user-signin-title {
	position: relative;
    float: left;
	font-size: 24px;
	font-weight: 800;
}
.user-signin-prep {
	position: relative;
    float: left;
	left: 6px;
	font-size: 24px;
	font-weight: 500;
}
.user-signin-inform {
	position: absolute;
	bottom: 30;
	right: 32;
	font-size: 24px;
	font-weight: 800;
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
</head>

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
									padding:0px;">
<form name="aform" method="post"  style="overflow:hidden;">
	<input type="hidden" id="imgUrl" name="imgUrl" value="">									
	<div style="width: 690px; height: 420px; display: block; position: absolute; top: 0px; left: 0px; overflow: visible; background-color: white;">
<!--  		<div class="fancybox-outer" style="padding: 0px; width: auto; height: auto;">
			<div class="fancybox-inner" style="width: 690px; height: auto; overflow: auto;">
				<div class="lightbox-wrap" align="center" style="text-align: center;"> -->
				
					<div style="width: 100% ; background-color: red; height: 5px;"></div>
					<div class="join_header" align="center" style="text-align: center;">
						<?php 					
						if(get_option('users_can_register')) { //Check whether user registration is enabled by the administrator
						?>
						<div class="content" style="position: relative;" align="center">
							<div style="width: 100%; height: 65px; position: relative;">
                                <div style="position:relative; top:20px; left:40px;">
                                    <p align="left" style="font-size: 24px;"><strong>Register</strong> a new account on YEPLIVE</p>
                                </div>
							</div>
							
							<div style="width: 50% ; height: 38%; float: left; position: relative;">
								<div style="width: 76%; text-align: left;">Email</div>
								<div style="width:100%;">
									<input type="text" name="email" id="email" style="width: 76%; height: 25%; margin-top: 7px;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
								</div>
								<div style="width: 76%; margin-top: 30px; text-align: left;">Verify Email</div>
								<div style="width: 100%;">
									<input type="text" name="emailverify" id="emailverify" style="width: 76%; height: 25%; margin-top: 7px;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
								</div>
							</div>
							<div style="width: 50% ; height: 38%; float: right; position: relative;">
								<div style="width: 76%; text-align: left;">Username</div>
								<div style="width:100%;">
									<input type="text" name="username" id="username" style="width: 76%; height: 25%; margin-top: 7px;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
								</div>
								<div style="width: 76%; margin-top: 30px; text-align: left;">Password</div>
								<div style="width: 100%;">
									<input type="password" name="password" id="password" style="width: 76%; height: 25%; margin-top: 7px;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
								</div>
							</div>
							<div style="width: 100%; float: left; padding: 26px 0px;">
								<img src="<?php echo get_template_directory_uri() ?>/images/register.png" style="margin-left: 41px; cursor: pointer; float: left;" onclick="onBtnCreate();">
								<img id="wait" style="width:35px;height:25px;margin:0px;padding:0px; padding-left: 70px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
								<span id="loginResult" style="color:red; margin-left: 25px; float: left; margin-top: 32px;"></span>
							</div>
							
						</div>
						<?php }
						else{?>
						
						<?php }?>
					</div>

					<div style="width: 100% ; background-color: red; height: 5px; position: absolute; bottom: 53;"></div>
					<div style="width: 100% ; background-color: #E5E5E5; height: 53px; position: absolute; bottom: 0;">
						<div style="float: right; position: relative; width: 400px; ">
							<img src="<?php echo get_template_directory_uri()?>/images/go-back-arrow.png" style="float: right; padding-right: 160px; padding-top: 19px;">
							<a style="position: absolute; right: 84; top: 17; cursor: pointer; color: blue; cursor: poininter; font-size: 15px;" href="<?php echo home_url() ?>/login/" >GO BACK</a>
						</div>
					</div>
					
<!--			</div>
			</div>
		</div>   -->
	</div>
</form>
<?php 
//if user_ID exist then

?>

</body>
</html>