<?php
/*

Template name: broadcast

*/
if(!isset($_SESSION)) session_start();

if($_SESSION['user_id'] == "")
{
	echo "please login!";
	exit();
}
get_header();
require_once(ABSPATH . WPINC . '/registration.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
//include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
global $wpdb, $user_ID;

$channel_id = $_POST['broadCh_id'];
$program_id = $_POST['broadProg_id'];

	$liveUrl = $channel_id . "_" . $program_id . "_" . $_SESSION['user_id'];
	
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
	$sql_query = "select start_time,
						 end_time,
						 image_path ,
						 title
				  from 
						 wp_program 
				  where 
						 program_id = '" . $program_id . "'";
	$result = mysql_query($sql_query);
	
	$row = mysql_fetch_array($result);
	$start_time = $row['start_time'];
	$end_time = $row['end_time'];
	if($row['image_path'] != "")
		$image_path = home_url() . $row['image_path'];
		
	$title = $row['title'];
	
	
	/*
	$startDateTime = explode(" " , $start_time);
	$endDateTime = explode(" " , $end_time);
	
	$startDate = $startDateTime[0];
	$startTime = $startDateTime[1];
	
	$startYMD = explode("-",$startDate);
	$startHMS = explode(":",$startTime);
	
	$serverStartDate = mktime($startHMS[0],$startHMS[1],$startHMS[2],$startYMD[1],$startYMD[2],$startYMD[0]);
	
	
	$endDate = $endDateTime[0];
	$endTime = $endDateTime[1];
	
	$endYMD = explode("-",$endDate);
	$endHMS = explode(":",$endTime);
	
	$serverEndDate = mktime($endHMS[0],$endHMS[1],$endHMS[2],$endYMD[1],$endYMD[2],$endYMD[0]);
	*/
	mysql_select_db(DB_NAME,$link);
	
	$sql_query = "select
							lu.picture_path,
							lu.facebook_id,
							lu.twitter_img,
							lu.facebook_name,
							lu.twitter_name,
							wu.user_login
				  from
							wp_user_yep lu
				  left join
							wp_users wu
				  on
							lu.wp_user_id = wu.ID
				  where
							lu.user_id = '" . $_SESSION['user_id'] . "'";
	
	$result = mysql_query($sql_query);
	$row = mysql_fetch_array($result);
	
	$user_login = $row['user_login'];
	$facebook_name = $row['facebook_name'];
	$twitter_name = $row['twitter_name'];
	$picture_path = $row['picture_path'];
	$facebook_id = $row['facebook_id'];
	$twitter_img = $row['twitter_img'];
	
	mysql_free_result($result);
	
	
	mysql_close($link);
	
	$sql_query = "select
							connect_count
				  from
							wp_program
				  where
							program_id = '" . $program_id . "'";
	
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	foreach($result as $i => $row)
	{
		$connect_count = $row->connect_count;
	}
	
	$serverCurrGmtTime = getdate(); // server gmt time
	$serverCurrLocalTime = time() * 1000; // server current time
	//$tc = 0;
/*function timeCount()
{
	global $serverEndDate;
	global $serverStartDate;
	global $tc;
	sleep(5);
	
	
	$period = $serverEndDate - $serverStartDate - $tc;
	
	if($period == 0)
		return;
	
	$hours = round($period / 3600);
	
	$minutes = $period % 3600;
	$minutes = round($minutes / 60);
	
	$seconds = $period % 60;
	
	if(strlen($hours) == 1)
		$hours = "0" . $hours;
	if(strlen($minutes) == 1)
		$minutes = "0" . $minutes;
	if(strlen($seconds) == 1)
		$seconds = "0" . $seconds;
	
	echo "
			<script>document.getElementById('timeduration').innerHTML = '" . $hours . ":" . $minutes . ":" . $seconds . "'
			</script>		
		";
	$tc++;
	timeCount();
}*/	
	
?>
<script>
	var dateTemp = new Date();
	
	var localTime = Date.UTC(dateTemp.getFullYear()  , dateTemp.getMonth() , dateTemp.getDate() , dateTemp.getHours() , dateTemp.getMinutes() , dateTemp.getSeconds());
	
	var periodClientLocalGmt = localTime - (dateTemp.getTime() - dateTemp.getTime() % 1000);
	var  clientCurrGmtTime = (dateTemp.getTime() - dateTemp.getTime() % 1000) - periodClientLocalGmt; // getting client gmttime
	//periodLocalGmt = localTime - gmtTime;
	//gmtTime = localTime - periodLocalGmt;
	//locaTime = periodLocalGmt + localTime; 
	
	var serverYear = "<?php echo $serverCurrGmtTime['year']?>";
	var serverMonth = "<?php echo $serverCurrGmtTime['mon']?>";
	var serverDate = "<?php echo $serverCurrGmtTime['mday']?>";
	var serverHours = "<?php echo $serverCurrGmtTime['hours']?>";
	var serverMinutes = "<?php echo $serverCurrGmtTime['minutes']?>";
	var serverSeconds = "<?php echo $serverCurrGmtTime['seconds']?>";

	var serverCurrGmtTime = new Date(0);
	serverCurrGmtTime.setFullYear(serverYear);
	serverCurrGmtTime.setMonth(serverMonth - 1);
	serverCurrGmtTime.setMonth(serverMonth - 1);
	serverCurrGmtTime.setDate(serverDate);
	serverCurrGmtTime.setHours(serverHours);
	serverCurrGmtTime.setMinutes(serverMinutes);
	serverCurrGmtTime.setSeconds(serverSeconds);
	
	var periodClientServerGmtTime = clientCurrGmtTime - serverCurrGmtTime.getTime();
	
	
</script>
	
	
	
		
        <!-- END Browser History required section -->  
            
        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/swfobject.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/fancywebsocket.js"></script>
        <style type="text/css">
			#othercontainer {
				width: 1150px;
				height: 205px;
				position: relative;
			}
			
			#othercarousel {
				margin: 0 auto;
			}

			#othercarousel img {
				border: 0;
			}
		</style>
		<script type="text/javascript">
		jQuery($.ajax({
			 type: "POST",
			 url: "<?php echo get_template_directory_uri(); ?>/ChatServer.php",
			 data: {}
			 }));

			var ChatServer;			
			var bPingThread = true;		
			<?php if(isset($_SESSION['user_id'])) {
			
				echo "var bLoggedIn = true;";
			} else {
				echo "var bLoggedIn = false;";
			}?>
			$(document).ready(function() {
				 log('Connecting with chat server ...');
				
				ChatServer = new FancyWebSocket('ws://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:9300/ChatServer.php');

				$('#message').keypress(function(e) {
					if (e.keyCode == 13 && this.value ) {
						if (bLoggedIn == false)
							return false;
						send( this.value );				
						log( 'You: ' + this.value );
						document.getElementById("log").scrollTop = 0;
						$(this).val('');
						return false;
					} else if (e.keyCode == 13 && this.value == '') {
						return false;
					}
				});

				//Let the user know we're connected
				ChatServer.bind('open', function() {



					 log( "Chat Server Connected." );
					 <?php 



					if($program_id != '') {
						
						// When you open this page first.
						if(is_user_logged_in())
						{
							$username = et_get_usernamebyid($_SESSION['user_id']);//$username[0]->user_nicename
							echo "ChatServer.send('message', '".$username[0]->user_nicename.":wscommand:". $program_id ."');";
							//echo "alert('".$_SESSION['user_id'].":wscommand:'+g_program_id);";
						}
						else if($_SESSION['facebook_name'] != "")
							echo "ChatServer.send('message', '".$_SESSION['facebook_name'].":wscommand:". $program_id ."');";
						else  if($_SESSION['twitter_name'] != "") 
							echo "ChatServer.send('message', '".$_SESSION['twitter_name'].":wscommand:". $program_id ."');";
						else
							echo "ChatServer.send('message', 'anonymous:wscommand:". $program_id ."');";
						echo "bPingThread = true;";
						echo "pingThread();";
					} 
					?>
				});

				//OH NOES! Disconnection occurred.
				ChatServer.bind('close', function( data ) {
					 //log( "Disconnected." );
						bPingThread = false;
				});

				//Log any messages sent from server
				ChatServer.bind('message', function( payload ) {
				if (payload == "pingthread")
						return;
					log( payload );
				});


				ChatServer.connect();

			});
			function pingThread() {
				if (bPingThread == true) {
					send("pingthread");
					window.setTimeout("pingThread()", 2000);
				}
			}

			jQuery(function( $ ) {
				$( "#othercarousel" ).rcarousel({
					visible: 5,
					step: 5,
					margin:20,
					width:195,
					height:195,
					speed: 300
				});
				
			});
			</script>
<script type="text/javascript">
            // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
            
            var swfVersionStr = "11.1.0";
            // To use express install, set to playerProductInstall.swf, otherwise the empty string. 
            var xiSwfUrlStr = "<?php echo get_template_directory_uri()?>/playerProductInstall.swf";
            var flashvars = {"URL": "rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream","CHNL": "mp4:<?php echo $liveUrl?>","WDTH": 320,"HGHT": 240,"FPS": 15,"BW":90000,"QLT":90,"INV":30};
            var params = {};
            params.quality = "high";
            params.bgcolor = "#000000";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            var attributes = {};
            attributes.id = "H264_Encoder";
            attributes.name = "H264_Encoder";
            attributes.align = "middle";
            swfobject.embedSWF(
                "<?php echo get_template_directory_uri()?>/H264_Encoder.swf", "flashContent", 
                "580", "420", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes);
            // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
            swfobject.createCSS("#flashContent", "display:block;text-align:left;");
        </script>
        <script type="text/javascript"> 
		function onInfo(v){
			H264_Encoder.doPublish(v);
		}
		</script>

<script>
//var headerBg = document.getElementById("et-header-bg");
//headerBg.style.display = "none";
function log( text ) {
	$log = $('#log');
	//Add text to log
	$log.html(text + "\n" + ($log.val()));
	//Autoscroll
	
	$log[0].scrollTop = $log[0].scrollHeight - $log[0].clientHeight;
}

function send( text ) {
	ChatServer.send( 'message', text );
}

function onSendMessage() {
	var obj = document.getElementById('message');
	if (bLoggedIn == false)
		return;
	if (obj.value == '' || obj.value == ' ')
		return;
	
	log( 'You : ' + obj.value );
	send( obj.value );

	$('#message').val('');
}
function fn_facebookPromote()
{
	<?php
		if(!isset($_SESSION['user_id']))
		{?>
			alert("Please,do login!");
			return;
		<?php }
		if(!isset($_SESSION['facebook_id']) || $_SESSION['facebook_id'] == "")
		{?>
			alert("You have to join in facebook!");
			return;
		<?php 
		}?>
		var channelUrlStr = "";
		var title = "";
		<?php
		if($program_id == "")
		{?>
			alert("Registered program does not exist!");
			return;
		<?php 
		}
		?>
			title = "<?php echo $title?>";
			var ch = document.getElementById("<?php echo $channel_id?>");
			var inputTag = ch.getElementsByTagName("input");
			channelUrlStr = inputTag[0].value;
			
		channelUrlStr = channelUrlStr + "?hidChannelId=" + "<?php echo $channel_id?>" + "&liveVod=live&programId=" + "<?php echo $program_id?>";
	$.ajax({
		type: "POST",
	  	url: "<?php echo home_url()?>/facebook_sendpost/",
	  	data: { channelUrl: channelUrlStr , title: title },  
	  	success: function(result) {
		  	var msg = "I promoted " + title +" to " + result + " people on Facebook !";
		  	send( msg );				
			log( 'You: ' + msg );
			document.getElementById("log").scrollTop = 0;
	  	}
	});	
}

function fn_twitterPost()
{
	<?php
			if(!isset($_SESSION['user_id']))
			{?>
				alert("Please,do login!");
				return;
			<?php }
			if(!isset($_SESSION['twitter_id']) || $_SESSION['twitter_id'] == "")
			{?>
				alert("You have to join in twitter!");
				return;
			<?php 
			}?>
			
			var channelUrlStr = "";
			var title = "";
			<?php
			if($program_id == "")
			{?>
				alert("Registered program does not exist!");
				return;
			<?php 
			}?>
			title = "<?php echo $title?>";
			var ch = document.getElementById("<?php echo $channel_id?>");
			var inputTag = ch.getElementsByTagName("input");
			channelUrlStr = inputTag[0].value;
			
			channelUrlStr = channelUrlStr + "?hidChannelId=" + "<?php echo $channel_id?>" + "&liveVod=live&programId=" + "<?php echo $program_id?>";
			
	$.ajax({
		type: "POST",
	  	url: "<?php echo home_url()?>/twitter_sendpost/",
	  	data: { channelUrl: channelUrlStr, title: title},
	  	success: function(result) {
	  		var msg = "I promoted " + title +" to " + result + " people on twitter!";
	  		send( msg );				
			log( 'You: ' + msg );
			document.getElementById("log").scrollTop = 0;
	  	}
	});	
}
function polling()
{
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
		    document.getElementById("connect_count").innerHTML = xmlhttp.responseText; 
		   
		}
	  }
	
	
	
	xmlhttp.open("GET","<?php echo get_template_directory_uri() ?>/connect_count_polling.php?program_id=" + "<?php echo $program_id ?>",true);
	xmlhttp.send();
}
function fn_dateConvert(dateStr)
{
	try
	{
	var start_date = dateStr.split(" ")[0];
	var start_time = dateStr.split(" ")[1];
	
	var year = start_date.split("-")[0];
	var month = start_date.split("-")[1];
	var intDate = start_date.split("-")[2];

	var hours = start_time.split(":")[0];
	var minutes = start_time.split(":")[1];
	var seconds = start_time.split(":")[2];
		
	if(month.substring(0,1) == "0")
		month = month.substring(1,2);
	
	if(intDate.substring(0,1) == "0")
		intDate = intDate.substring(1,2);
	
	if(hours.substring(0,1) == "0")
		hours = hours.substring(1,2);
	
	if(minutes.substring(0,1) == "0")
		minutes = minutes.substring(1,2);
	
	if(seconds.substring(0,1) == "0")
		seconds = seconds.substring(1,2);
		
	var tempDate = new Date(0);
	
	
	tempDate.setFullYear(parseInt(year));
	tempDate.setMonth(parseInt(month) - 1);
	tempDate.setMonth(parseInt(month) - 1);
	tempDate.setDate(parseInt(intDate));
	tempDate.setHours(parseInt(hours));
	tempDate.setMinutes(parseInt(minutes));
	tempDate.setSeconds(parseInt(seconds));
	
	var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
	var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

	var tempTime = ctLocalTime.getTime();

	

	return tempTime;
	}
	catch(e)
	{
		return "date_convert_error";
	}
	
	
}
function get_start_time()
{
	
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
		 //   document.getElementById("connect_count").innerHTML = xmlhttp.responseText;
		  
		   if(xmlhttp.responseText != "")
		   {
			   var rResult = fn_dateConvert(xmlhttp.responseText);
			   if(rResult == "date_convert_error")
				   return;
			   clearInterval(getStartTime_Thread);

			   org_start_date = xmlhttp.responseText;
			   startDate.setTime(fn_dateConvert(xmlhttp.responseText));

			   year = startDate.getFullYear();
				month = startDate.getMonth() + 1;
				intDate = startDate.getDate();

				hours = startDate.getHours();
				minutes = startDate.getMinutes();
				seconds = startDate.getSeconds();

				if(month.toString().length == 1)
					month = "0" + month;
				if(intDate.toString().length == 1)
					intDate = "0" + intDate;
				if(hours.toString().length == 1)
					hours = "0" + hours;

				if(minutes.toString().length == 1)
					minutes = "0" + minutes;
				if(seconds.toString().length == 1)
					seconds = "0" + seconds;

				document.getElementById("contentSpan").innerHTML = "Title : <?php echo $title ?>";
				document.getElementById("contentSpan").innerHTML += "<br>Start Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
				
			   timeCount();
		   }
		   else
		   {
			   document.getElementById("timeduration").innerHTML = "00:00:00";
			   document.getElementById("connect_count").innerHTML = "0";
		   }
		}
	  }
	
	
	
	xmlhttp.open("GET","<?php echo get_template_directory_uri() ?>/get_start_time.php?program_id=" + "<?php echo $program_id ?>",true);
	xmlhttp.send();
}
</script>

<br>
<form name="aform" method="post" >
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="programIdArr" name="programIdArr" value="">
<div align="center" id="rootDiv">
	<table id="rootTbl" style="width:1000px; background-color: #000000; border-radius:10px; margin-bottom: 50px;" cellpadding="0" cellspacing="0" border="0" border-color="silver" >
	  <tr>
	    <td width="63%">
	      <table>
	        <tr>
	          <td style="padding-left: 20px; padding-top: 10px;">
	          	<table style="width: 90%;">
	          		<tr>
	          			<td style="width:39%;">
	          				<div style="color:silver; font-size: 32px; font-weight:700;"><?php echo $title?></div>
	          			</td>
	          			<td style="width:30%;">
	          				<div style="font-size: 15px; color: #ED1C24; font-weight:700; float:left; line-height: 28px;">RECORDING LIVE</div>
	          				<img style="float:right; margin-top: 3px; width: 23px; margin-right: 14px;" src="<?php echo get_template_directory_uri()?>/images/red-circle.png"></img>
	          					          				
	          			</td>
	          			<td style="width: 30%;">
	          				<div style="font-size: 14px; color: #FFAEC9; float: left; line-height: 26px; font-weight: 700;" >STOP</div>
	          				<img class="record_stop" src="<?php echo get_template_directory_uri()?>/images/record-stop.png" onclick="streamingStop();">
	          			</td>
	          		</tr>
	          	</table>
	          	
	          </td>
	        </tr>
	        <tr>
	          <td style="text-align: center; width:640px; height:480px;">
				<div id="flashContent" >
		            <p>
		                To view this page ensure that Adobe Flash Player version 
		                11.1.0 or greater is installed. 
		            </p>
		            <script type="text/javascript"> 
		                var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
		                document.write("<a href='http://wws rw.adobe.com/go/getflashplayer'><img src='" 
		                                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
		            </script> 
	        	</div>	
				 
			  </td>
			</tr>
			<tr>
			  <td>
				<table width="100%" border="0">
				  <tr>
				    <td width="30%" rowspan="4" style="padding-left: 3px;">
				      <img src="<?php
				      if($image_path != "") 
				      	echo $image_path;
				      else if($picture_path != "")
				      	echo home_url() . $picture_path;
				      else if($facebook_id != "")
				      	echo "https://graph.facebook.com/" . $facebook_id . "/picture";
				      else if($twitter_name != "")
				      	echo $twitter_img;
				      ?>" style="width:200px; height:200px; border-radius:10px;" >
				    </td>
				    <td colspan="2" align="center">
				      <table>
				        <tr>
				          <td>
				            <img src="<?php echo get_template_directory_uri(); ?>/images/time_temp.png" style="height:28px;" />
				          </td>
				          <td>
				            <span id="timeduration" style="color:green; font-size: 20px; font-weight: bold;">00:00:00</span>
				          </td>
				          <td style="padding-left: 30px;">
				            <img src="<?php echo get_template_directory_uri(); ?>/images/count_temp.png" style="height:24px;" />
				          </td>
				          <td>
				            <span id="connect_count" style="color:green; font-size: 20px; font-weight: bold;"><?php echo $connect_count?></span>
				          </td>
				        </tr>
				      </table>
				    </td>
				  </tr>
				  <tr>
				    <td width="50%" height="70px;" style="padding-left: 20px;">
				      <a href="#">
				        <h1 style="color:silver;">
				        <?php if($user_login != "") 
				        	echo $user_login;
				        else if($facebook_name != "")
				        	echo $facebook_name;
				        else if($twitter_name != "")
				        	echo $twitter_name;
				        ?></h1>
				      </a>
				    </td>
				    <td width="*" align="center">
				      <img src="<?php echo get_template_directory_uri(); ?>/images/tweet_logo.jpg" onclick="fn_twitterPost();" style="cursor:pointer;float:right;width:34px; height:34px;">
				      <img src="<?php echo get_template_directory_uri(); ?>/images/facebook_logo.jpg" onclick="fn_facebookPromote();" style="cursor:pointer;float: right;">
				      <!-- <img src="<?php echo get_template_directory_uri(); ?>/images/vote_up.png" style="float: right;" onmousedown="this.src='<?php echo get_template_directory_uri(); ?>/images/vote_down.png'" onmouseup="this.src='<?php echo get_template_directory_uri(); ?>/images/vote_up.png'" onmouseout="this.src='<?php echo get_template_directory_uri(); ?>/images/vote_up.png'">
				       -->
				    </td>
				  </tr>
				  <tr>
				    <td colspan="2" style="padding-left: 20px;">
				      <span style="color:silver; line-height: 20px; " id="contentSpan" name="contentSpan">
				      <?php 
		      				echo "Title : " . $title;
							echo "<br>";
					  ?>
					  <script>
					  
					  if("<?php echo $start_time?>" != "")
					  {
						  
						var clientStartTime = new Date(fn_dateConvert("<?php echo $start_time?>"));
						
						year = clientStartTime.getFullYear();
						month = clientStartTime.getMonth() + 1;
						intDate = clientStartTime.getDate();

						hours = clientStartTime.getHours();
						minutes = clientStartTime.getMinutes();
						seconds = clientStartTime.getSeconds();

						if(month.toString().length == 1)
							month = "0" + month;
						if(intDate.toString().length == 1)
							intDate = "0" + intDate;
						if(hours.toString().length == 1)
							hours = "0" + hours;

						if(minutes.toString().length == 1)
							minutes = "0" + minutes;
						if(seconds.toString().length == 1)
							seconds = "0" + seconds;

						document.getElementById("contentSpan").innerHTML += "Start Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
					  }

					  </script>
				      </span>
				    </td>
				  </tr>
				  <tr>
				    <td colspan="2" valign="bottom">
				      <!-- <img src="<?php echo get_template_directory_uri(); ?>/images/link_bar.jpg" style="width:100%; height:34px;"> -->
				    </td>
				  </tr>
				</table>
			  </td>
	        </tr>
	      </table>	
	    </td>
	    <td style="width:1px; background-color: silver;">
	    </td>
	    <td width="*" >
	      <table border="0" border-color="silver">
	        <tr>
	          <td width="15%" style="padding-left: 15px; padding-top: 5px;">
				<img src="<?php echo get_template_directory_uri(); ?>/images/profile-thumb.png" style="width:24px; height:24px; border-radius:3px;">
			  </td>
			  <td width="*">
			    <input type="text" style="width:280px;z-index: 20000; padding:5px;"  id='message' name='message'>
			  </td>
	        </tr>
	        <tr>
	          <td colspan="2">
	            <div id="chat" style="height: 650px; padding-left: 10px;">
	              <textarea id='log' name='log' readonly='readonly' style="overflow:hidden; border-color:#000000; background-color:#000000; color:silver; resize:none; width: 320px; height: 620px;"></textarea><br/>
		        </div>
	          </td>
	        </tr>
	      </table>
	    </td>
	  </tr>
	</table>

	
<script type="text/javascript">
	function streamingStop () {
		alert("You have finished your live stream. You may view it below");	
		var channelId = "<?php echo $channel_id ?>";
		var programId = "<?php echo $program_id ?>";
		
		//paramids = progInfo[ i ].pid + "," + progInfo[ i ].cid + "," + progInfo[ i ].vod_enable + ":";
		paramids = programId + "," + channelId + "," + "0" + ":";
		
		document.getElementById("programIdArr").value = paramids.substring(0,paramids.length - 1);
		document.aform.action = "<?php echo home_url()?>/program_list";
		document.aform.submit();
		return;	
	}
	
</script>
	
	
	
</div>
<br>

</form>
<script>
		//
		
		var startDate = new Date(0);
		var org_start_date = "<?php echo $start_time?>";
		
		if("<?php echo $start_time?>" != "")
		{
			startDate.setTime(fn_dateConvert("<?php echo $start_time?>"));
			document.getElementById("timeduration").innerHTML = "00:00:00";
			timeCount();
		}
		function timeCount()
		{
			
			var nowTime = new Date();
			var startTime = startDate.getTime();
			
			if(nowTime.getTime() - startTime >= 5 * 3600 * 1000)
			{
				var obj = document.getElementById("H264_Encoder");
				
				if(obj)
				{
					document.getElementById("timeduration").innerHTML = "00:00:00";
					document.getElementById("connect_count").innerHTML = "0";
					obj.parentNode.removeChild(obj);
					clearInterval(polling_thread);
				}
				return;
			}

			/*
			var hours = nowTime.getHours();
			var minutes = nowTime.getMinutes();
			var seconds = nowTime.getSeconds();
			*/
			var period = (nowTime.getTime() - startTime) / 1000;
			
			var hours = parseInt(period  /  3600);
			var minutes = period  % 3600;
				seconds = parseInt(minutes % 60);
				minutes = parseInt(minutes / 60);
				
			if(hours.toString().length == 1)
				hours = "0" + hours;
			if(minutes.toString().length == 1)
				minutes = "0" + minutes;
			if(seconds.toString().length == 1)
				seconds = "0" + seconds; 	
			document.getElementById("timeduration").innerHTML = hours + ":" + minutes + ":" + seconds;
			
			delete nowTime;
			nowTime = null;
			setTimeout("timeCount();",1000);
		}

		var menuLoad_thread = setInterval("is_menu_load_func();",100);
		var polling_thread;
		var getStartTime_Thread;
		/*if("<?php // echo $start_time?>" == "")
		//{
			getStartTime_Thread = setInterval("get_start_time();",1000);
			
		}*/
		function is_menu_load_func()
		{
			if(menuLoadFlag == true)
			{
				clearInterval(menuLoad_thread);
				polling_thread = setInterval("polling();",10000);
				
				if("<?php echo $start_time?>" == "")
				{
					getStartTime_Thread = setInterval("get_start_time();",3000);
					
				}
			}	
		}
		
			 	 	
</script>
<?php
 
get_footer();?>