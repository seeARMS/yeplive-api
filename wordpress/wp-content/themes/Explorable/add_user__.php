<?php
/*

Template name: add_user

*/
require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;
if($user_ID)
{
	
	wp_redirect(home_url()); exit;
}

?>

<html lang="en-US" >
<head>

<script language="javascript">

function onBtnCreate()
{
	if(document.getElementById("imgUrl").value == "")
	{
		alert("Please,register your profile image!");
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
				document.getElementById('email').focus();
				return;
			}
			else if(xmlhttp.responseText == "success")
			{
				alert("Please check your email for login details.");
				parent.document.location.href = "<?php echo home_url()?>";
				return;
			}
		}
	  }
	var imgurl = document.getElementById("imgUrl").value;  
	var username = document.getElementById("username").value;
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
	//alert(email.split("@")[0] + email.substring(eindex,dotindex+1) + email.split(".")[1]);
	
	xmlhttp.open("GET","<?php echo home_url()?>/create_account?username=" + username + "&email=" + email + "&imgUrl=" + imgurl + "&eindex=" + eindex + "&dotindex=" + dotindex,true);
	xmlhttp.send();
	//document.aform.submit();

	
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
		
		border-radius: 4px;
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
		-moz-border-radius-topleft: 10px;
		-webkit-border-top-left-radius: 10px;
		border-top-left-radius: 10px;
		-moz-border-radius-topright: 10px;
		-webkit-border-top-right-radius: 10px;
		border-top-right-radius: 10px;
		
		position: relative;
		z-index: 1;
		padding: 30px 0 42px;
		border-bottom: 1px solid #d6d7d9;
		background-color: #fafafa;
		box-shadow: 1px 0 2px rgba(0,0,0,0.15);
	}
	.content {
		-moz-border-radius-bottomleft: 10px;
		-webkit-border-bottom-left-radius: 10px;
		border-bottom-left-radius: 10px;
		-moz-border-radius-bottomright: 10px;
		-webkit-border-bottom-right-radius: 10px;
		border-bottom-right-radius: 10px;
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
	<div style="width: 410px; height: 450px; display: block; position: absolute; top: 0px; left: 0px; overflow: visible;border-radius:10px;">
		<div class="fancybox-outer" style="padding: 0px; width: auto; height: auto;border-radius:10px;">
			<div class="fancybox-inner" style="width: 410px; height: auto; overflow: auto;border-radius:10px;">
				<div class="lightbox-wrap" align="center" style="text-align: center;border-radius:10px;">
					<div class="join_header" align="center" style="text-align: center;border-radius:10px;">
						<img src="<?php echo get_template_directory_uri()?>/images/logo.png">
						
						<?php 					
						if(get_option('users_can_register')) { //Check whether user registration is enabled by the administrator
						?>
						<div class="content" style="margin-top:20px;border-radius:10px;" aign="center">
							<table border="0" cellspacing="10" cellpadding="10" style="border-collapse: collapse; width:100%;border-radius:10px;">
								<colgroup>
									<col width="35%">
									<col width="65%">
								</colgroup>
								<tr>
									<td align="right">
										<span>User Name</span>
									</td>
									<td>
										<input type="text" name="username" id="username" style="width:90%;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
									</td>
								</tr>
								<tr>
									<td align="right" >
										<span>Email Address</span>
									</td>
									<td>
										<input type="text" name="email" id="email" style="width:90%;" onkeypress="document.getElementById('loginResult').innerHTML = '';">
									</td>
								</tr>
								<tr >
									<td  colspan="2" align="center">
										<table border="0" cellspacing=0 cellpadding=0 style="width:100%;height:100%;">
											<tr>
												<td  rowspan="2" align="right">
													<img src="" id="charImg" style="width:120px; height:120px;">
													<div align="right" style="padding-right: 22px; margin-top:-20px;">
														<h5>User   Image</h5>
													</div>													
												</td>
												<td align="center">
													<iframe id="ifImage" src="<?php echo home_url();?>/fileUpload/" frameborder="no" marginwidth="0px" marginheight="0px"  style="margin-top:20px; width:120px;height:30px;" scrolling="no">
													</iframe>
												</td>
											</tr>
											<tr>
												<td align="center" valign="top" style="padding-top:10px;">
													<input type="button" onclick="onBtnCreate();" value="Create" style=" color:#ffffff; letter-spacing: 3px; width:120px; height:45px; border-radius:25px;
										background: url('<?php echo get_template_directory_uri()?>/images/login1.png'); ">
												</td>
											</tr>
										</table>
										
									</td>
								</tr>
								<tr >
									<td colspan="2" align="center" style="height:25px;padding:0px; margin:0px; ">
										<img id="wait" style="width:35px;height:25px;margin:0px;padding:0px; padding-left: 70px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
										<span id="loginResult" style="color:red;margin:0px; padding:0px; padding-left: 50px;"></span>
										<a style="float:right; padding-right: 5px;" href="<?php echo home_url() ?>/login/"><span>Go back</span></a>
									</td>
								</tr>
								
							</table>
						</div>
						<?php }
						else{?>
						
						<?php }?>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</form>
<?php 
//if user_ID exist then

?>

</body>
</html>