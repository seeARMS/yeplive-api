<?php
/*

Template name: forgotten-password-reset

*/

?>
<?php
	$password = $_GET['user_id']; 
	$user_id = base64_decode($password);
?>
<div style="width: 70%; height: auto; margin: 27px auto auto auto;" align="center">
	<div style="border: 1px solid; width: 50%; height: 400px;">
		<div style="font-size: 28px; font-weight: 700; margin: 23px auto;">YepLive Password Reset</div>
		<div style="width: 78% ; height: 38%; float: left; position: relative;">
			<div style="width: 76%; text-align: left; font-size: 20px; margin-top: 7px;">New Password</div>
			<div style="width:100%;">
				<input type="password" name="email" id="password" style="width: 76%; height: 21%; margin-top: 7px;" >
			</div>
			<div style="width: 76%; margin-top: 44px; text-align: left; font-size: 20px;">Verify Password</div>
			<div style="width: 100%;">
				<input type="password" name="emailverify" id="passwordVerify" style="width: 76%; height: 21%; margin-top: 7px;" >
			</div>
			<input type="button" name="" value="Confirm" onclick="passwordConfirm();" style="margin: 38px 41px; float: left; width: 33%; height: 24%; cursor: pointer;">
		</div>
	</div>
</div>

<script type="text/javascript">
	function passwordConfirm () {
		var password = document.getElementById("password").value;
		var passwordVerify = document.getElementById("passwordVerify").value;
		
		if ( password == "" ) {
			alert("Please input new password!");
			document.getElementById("password").focus();
			return;
		}
		if ( passwordVerify == "" ) {
			alert("Please verify a password");
			document.getElementById("passwordVerify").focus();
			return;
		}
		var checkStr = hasNumbers(password);
		if ( checkStr == false) {
			alert("Password must be inculde one more numeric at least! Please input password.");
			document.getElementById("password").value = "";
			document.getElementById("passwordVerify").value = "";
			document.getElementById("password").focus();
			return;
		} 
		
		if ( password.length < 6 ) {
			document.getElementById("password").value = "";
			document.getElementById("passwordVerify").value = "";
			alert("The password must be at least 6 characters and numeric ");
			document.getElementById("password").focus();
			return;
		}
		
		if ( passwordVerify != password ) {
			alert("Please verify a password again");
			document.getElementById("passwordVerify").value = "";
			document.getElementById("passwordVerify").focus();
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
			if(xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
				if(xmlhttp.responseText == "success")
				{
					alert("New Password Success!");
					window.close();
					return;
				}
				else if(xmlhttp.responseText == "mysql_query fail") {
					alert("please try it again.");
				}
			}
		}  
		var user_id = <?php echo $user_id; ?>;
		xmlhttp.open("GET","<?php echo get_template_directory_uri() ?>/ajaxPasswordReset.php/?password=" + password + "&user_id=" + user_id ,true);
		xmlhttp.send();
	}
	
	function hasNumbers(t)
	{
		return /\d/.test(t);
	}
</script>