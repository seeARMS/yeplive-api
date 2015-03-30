<?php
/*

Template name: liveplay

*/

require_once(ABSPATH . WPINC . '/registration.php');
global $wpdb, $user_ID;

get_header();
//$pid = $_GET['pid'];
//$cid = $_GET['cid'];
//if (isset($pid) && isset($cid)) {
	//$channel_id = $cid;
//} else {

	if(isset($_POST['hidChannelId']))
		$channel_id = $_POST['hidChannelId'];
	if(isset($_GET['hidChannelId']))
		$channel_id = $_GET['hidChannelId'];

	if(isset($_POST['liveVod']))
	{
		$liveVod = $_POST['liveVod'];
	}
	
	
	if(isset($_GET['liveVod']))
	{
		$liveVod = $_GET['liveVod'];
	}
	
	
	if(isset($_POST['programId']))
	{
		if($_POST['programId'] && $liveVod == "vod")
			$prog_id = $_POST['programId'];
	}
	
	if(isset($_GET['programId']))
	{
		if($_GET['programId'] && $liveVod == "vod")
			$prog_id = $_GET['programId'];
	}
	
	if($liveVod == "")
		$liveVod = "live";
	
	$otherProgramId = "";
	if($_POST['programId'])
		$otherProgramId = $_POST['programId'];
	else if($_GET['programId'])
		$otherProgramId = $_GET['programId'];

	
	
$progInfo = array("program_id" => array(),"channel_id" => array(), "user_id" => array(), "title" => array(), "image_path" => array(), "start_time" => array(),
		"end_time" => array(), "vod_enable" => array() , "vod_path" => array() , "vote" => array() , "latitude" => array() , "longitude" => array(),
		"connect_count" => array(), "facebook_id" => array() , "facebook_name" => array() , "twitter_img" => array() , "twitter_name" => array(),
		"user_login" => array() , "picture_path" => array(), "isMobile" => array());	
	//$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	/*if(!$link)
	{
		echo "mysql_connect";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}*/
	
	//echo "<script>alert('" . $channel_id . "')</script>";
	$date = date("Y-m-d H:i:s");
	$voddate = date("Y-m-d H:i:s",time() - 24 * 60 * 60);
	
	if($liveVod == "live")
	{
		if($otherProgramId == "")
		{
			$sql_query = "select
			lp.program_id pid,
			lp.channel_id cid,
			lp.user_id uid,
			lp.title title,
			lp.image_path image_path,
			lp.start_time start_time,
			lp.end_time end_time,
			lp.vod_enable vod_enable,
			lp.vod_path vod_path,
			lp.vote vote,
			lp.latitude lat,
			lp.longitude lgt,
			lp.connect_count,
			lp.isMobile
			from
					wp_program lp
			where
					lp.channel_id = '" . $channel_id . "'
			and
					lp.start_time is not null 
			and
					lp.end_time is null 
			and
					lp.connect_count = (select 
												max(connect_count) 
										from 
												wp_program 
										where 
												channel_id = '" . $channel_id . "' 
										and 
												start_time is not null
										and
												end_time is null)
			order by lp.start_time asc 
			";
		}
		else
		{
			
			$sql_query = "select
			lp.program_id pid,
			lp.channel_id cid,
			lp.user_id uid,
			lp.title title,
			lp.image_path image_path,
			lp.start_time start_time,
			lp.end_time end_time,
			lp.vod_enable vod_enable,
			lp.vod_path vod_path,
			lp.vote vote,
			lp.latitude lat,
			lp.longitude lgt,
			lp.connect_count,
			lp.isMobile
			from
					wp_program lp
			where
					lp.program_id = '" . $otherProgramId . "'
			and
					lp.channel_id = '" . $channel_id . "'
			order by lp.start_time asc
			";
		}
	}
	else
	{
		
		$sql_query = "select
		lp.program_id pid,
		lp.channel_id cid,
		lp.user_id uid,
		lp.title title,
		lp.image_path image_path,
		lp.start_time start_time,
		lp.end_time end_time,
		lp.vod_enable vod_enable,
		lp.vod_path vod_path,
		lp.vote vote,
		lp.latitude lat,
		lp.longitude lgt,
		lp.isMobile
		from
				wp_program lp
		where
				lp.channel_id = '" . $channel_id . "'
		and
				lp.program_id = '". $prog_id . "'
		order by lp.start_time asc
		";
	}
	
	
	
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	//when User click  live or vod  in emap-view, appropriate vod or live have to be displaied in appropriate channel.
	//and then appropriate picture have to be selected in thumbneil.
	//and then when click appropriate picture(vod,live), have to be displayed vod or live.
	if(count($result) > 0)
	{
		$program_id = $result[0]->pid;//["program_id"];
		$ch_id = $result[0]->cid;//["channel_id"];
		$user_id = $result[0]->uid;//["user_id"];
		$vod_path = $result[0]->vod_path;
		$start_time = $result[0]->start_time;
		$end_time = $result[0]->end_time;
		$isMobile = $result[0]->isMobile;
	}
	
	/*foreach($result as $i => $row)
	{
		$program_id = $row->pid;//["program_id"];
		$ch_id = $row->cid;//["channel_id"];
		$user_id = $row->uid;//["user_id"];
		$vod_path = $row->vod_path;
		$start_time = $row->start_time;
		$end_time = $row->end_time;
		$i++;
	}*/
	
	$sql_query = "select facebook_id,twitter_id,wp_user_id from wp_user_yep where user_id = '" . $user_id . "'";
	$result = $wpdb->get_results($sql_query);
	
	foreach($result as $i => $row)
	{
		$programmer_wp_user_id = $row->wp_user_id;
		$programmer_facebook_id = $row->facebook_id;
		$programmer_twitter_id = $row->twitter_id;
	}
	
	/*if(isset($start_time))
	{
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
	}*/
	$liveUrl = $ch_id . "_" . $program_id . "_" . $user_id;
	
	
	
	$sql_query = "select
		lp.program_id pid,
		lp.channel_id cid,
		lp.user_id uid,
		lp.title title,
		lp.image_path image_path,
		lp.start_time start_time,
		lp.end_time end_time,
		lp.vod_enable vod_enable,
		lp.vod_path vod_path,
		lp.vote vote,
		lp.latitude lat,
		lp.longitude lgt,
		lp.connect_count,
		lp.isMobile,
		lu.picture_path,
		lu.facebook_id,
		lu.facebook_name,
		lu.twitter_id,
		lu.twitter_img,
		lu.twitter_name,
		wu.user_login
		from
				wp_program lp
		inner join
				wp_user_yep lu
		on
				lp.user_id = lu.user_id
		left join
				wp_users wu
		on
				lu.wp_user_id = wu.ID
		where
				lp.channel_id = '" . $channel_id . "'
		and
			(lp.start_time is not null and lp.end_time is null
		or
			(lp.end_time
		between
				'" . $voddate . "' and '" . $date . "'
		and lp.vod_enable = '1')			
					)
		order by lp.start_time asc";
	
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	foreach($result as $i => $row)
	{
		$progInfo["program_id"][$i] = $row->pid;//["program_id"];
		$progInfo["channel_id"][$i] = $row->cid;//["channel_id"];
		$progInfo["user_id"][$i] = $row->uid;//["user_id"];
		$progInfo["title"][$i] = $row->title;//["title"];
		if($row->image_path != "")
			$progInfo["image_path"][$i] = home_url() . $row->image_path;
		else 
			$progInfo["image_path"][$i] = "";
		$progInfo["start_time"][$i] = $row->start_time;
		$progInfo["end_time"][$i] = $row->end_time;
		$progInfo["vod_enable"][$i] = $row->vod_enable;
		$progInfo["vod_path"][$i] = $row->vod_path;
		$progInfo["vote"][$i] = $row->vote;
		$progInfo["latitude"][$i] = $row->lat;
		$progInfo["longitude"][$i] = $row->lgt;
		$progInfo["connect_count"][$i] = $row->connect_count;
		$progInfo["facebook_id"][$i] = $row->facebook_id;
		$progInfo["facebook_name"][$i] = $row->facebook_name;
		$progInfo["twitter_id"][$i] = $row->twitter_id;
		$progInfo["twitter_img"][$i] = $row->twitter_img;
		$progInfo["twitter_name"][$i] = $row->twitter_name;
		$progInfo["user_login"][$i] = $row->user_login;
		$progInfo["isMobile"][$i] = $row->isMobile;
		if($row->picture_path != "")
			$progInfo["picture_path"][$i] = home_url() . $row->picture_path;
		else
			$progInfo["picture_path"][$i] = "";
		
		$i++;
	}
	
	
	$sql_query = "select 
							lu.picture_path,
							wu.user_login,
							lu.facebook_name,
							lu.twitter_name 
				  from 
							wp_user_yep lu 
				  left join 
							wp_users wu 
				  on 
							lu.wp_user_id = wu.ID 
				  where 
							lu.user_id = '" . $user_id . "'";
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	foreach($result as $i => $row)
	{
		if($row->picture_path != "")
			$picture_path = home_url() . $row->picture_path;
		$userlogin = $row->user_login;
		$facebook_name = $row->facebook_name;
		$twitter_name = $row->twitter_name; 
	}
		

	$sql_query = "select
							connect_count
				  from
							wp_channel
				  where
							channel_id = '" . $channel_id . "'";
	
	$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
	
	foreach($result as $i => $row)
	{
		$connect_count = $row->connect_count;
	}
	
	$serverCurrGmtTime = getdate(); // server gmt time
	$serverCurrLocalTime = time() * 1000; // server current time
?>
<script>
	var progInfo = new Array();
	var g_channel_id = "<?php echo $ch_id?>";
	var g_program_id = "<?php echo $program_id?>";
	var g_user_id = "<?php echo $user_id?>";
	
	<?php
			for($i = 0; $i < count($progInfo['program_id']); $i++)
			{
				/*$start_time = $progInfo['start_time'][$i];
				$end_time = $progInfo['end_time'][$i];
				
				$progStartDateTime = explode(" " , $start_time);
				$progEndDateTime = explode(" " , $end_time);
				
				$progStartDate = $progStartDateTime[0];
				$progStartTime = $progStartDateTime[1];
				
				$progStartYMD = explode("-",$progStartDate);
				$progStartHMS = explode(":",$progStartTime);
				
				$progServerStartDate = mktime($progStartHMS[0],$progStartHMS[1],$progStartHMS[2],$progStartYMD[1],$progStartYMD[2],$progStartYMD[0]);
				
				
				$progEndDate = $progEndDateTime[0];
				$progEndTime = $progEndDateTime[1];
				
				$progEndYMD = explode("-",$progEndDate);
				$progEndHMS = explode(":",$progEndTime);
				
				$progServerEndDate = mktime($progEndHMS[0],$progEndHMS[1],$progEndHMS[2],$progEndYMD[1],$progEndYMD[2],$progEndYMD[0]);
				*/
			?>
			progInfo[ progInfo.length ] = {
						program_id : "<?php echo $progInfo['program_id'][$i]?>",
						channel_id : "<?php echo $progInfo['channel_id'][$i]?>",
						user_id : "<?php echo $progInfo['user_id'][$i]?>",
						image_path : "<?php echo $progInfo['image_path'][$i]?>",
						title : "<?php echo $progInfo['title'][$i]?>",
						start_time : "<?php echo $progInfo['start_time'][$i]?>",
						end_time : "<?php echo $progInfo['end_time'][$i]?>",
						vod_enable : "<?php echo $progInfo['vod_enable'][$i]?>",
						vod_path : "<?php echo $progInfo['vod_path'][$i]?>",
						vote : "<?php echo $progInfo['vote'][$i]?>",
						latitude : "<?php echo $progInfo['latitude'][$i]?>",
						longitude : "<?php echo $progInfo['longitude'][$i]?>",
						connect_count : "<?php echo $progInfo['connect_count'][$i]?>",
						facebook_id : "<?php echo $progInfo['facebook_id'][$i]?>",
						facebook_name : "<?php echo $progInfo['facebook_name'][$i]?>",
						twitter_id : "<?php echo $progInfo['twitter_id'][$i]?>",
						twitter_img : "<?php echo $progInfo['twitter_img'][$i]?>",
						twitter_name : "<?php echo $progInfo['twitter_name'][$i]?>",
						user_login : "<?php echo $progInfo['user_login'][$i]?>",
						picture_path : "<?php echo $progInfo['picture_path'][$i]?>",
						isMobile : "<?php echo $progInfo['isMobile'][$i]?>"
					};
			<?php 
			}?>
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
<script type="text/javascript" src='<?php echo get_template_directory_uri()?>/js/swfobjectPlayer.js'></script>
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
				
				ChatServer = new FancyWebSocket('ws://<?php echo SERVER_ADDRESS?>:9300/ChatServer.php');

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
					 
					 if (g_program_id == "")
						 return;
					 log( "Chat Server Connected." );
					 <?php 
					
					if($program_id != '') {
						
						// When you open this page first.
						if(is_user_logged_in())
						{
							$username = et_get_usernamebyid($_SESSION['user_id']);//$username[0]->user_nicename
							echo "ChatServer.send('message', '".$username[0]->user_nicename.":wscommand:'+g_program_id);";
							//echo "alert('".$_SESSION['user_id'].":wscommand:'+g_program_id);";
						}
						else if($_SESSION['facebook_name'] != "")
							echo "ChatServer.send('message', '".$_SESSION['facebook_name'].":wscommand:'+g_program_id);";
						else  if($_SESSION['twitter_name'] != "") 
							echo "ChatServer.send('message', '".$_SESSION['twitter_name'].":wscommand:'+g_program_id);";
						else
							echo "ChatServer.send('message', 'anonymous:wscommand:' + g_program_id);";
						echo "bPingThread = true;";
						echo "pingThread();";
					} 
					?>
					<?php
					if(is_user_logged_in())
					{
						$username = et_get_usernamebyid($_SESSION['user_id']);//$username[0]->user_nicename
					}
					?>
					var is_user_logged_in = "<?php echo is_user_logged_in()?>".toUpperCase();
					
					if("<?php echo $program_id?>" == "" && g_program_id != "")
					{
						if(is_user_logged_in == "TRUE")
							ChatServer.send("message","<?php echo $username[0]->user_nicename?>" + ":wscommand:" + g_program_id);
						else if("<?php echo $_SESSION['facebook_name']?>" != "")
							ChatServer.send("message","<?php echo $_SESSION['facebook_name']?>" + ":wscommand:" + g_program_id);
						else if("<?php echo $_SESSION['twitter_name']?>" != "")
							ChatServer.send("message","<?php echo $_SESSION['twitter_name']?>" + ":wscommand:" + g_program_id);
						else
							ChatServer.send("message","anonymous:wscommand:" + g_program_id);
						bPingThread = true;
						pingThread();

						
					}
					
					
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

				if (g_program_id != "") {
					ChatServer.connect();
				}
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

<script>
	//var homeurl = "<?php echo home_url( '/' )?>/profile/";		
	//var headerBg = document.getElementById("et-header-bg");
	//headerBg.style.display = "none";

	var counter = 0;
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

	function onCloseChatClient() {
		ChatServer.send('message', '::close::');
	}

	function onConnectChatServer() {
		onCloseChatClient();
		if (g_program_id != "")
			ChatServer.connect();
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
function onAddImage()
{
	//if(counter < 10)
	//	return;
	//counter = 0;
	var characterTbl = document.getElementById("character");
	var td = characterTbl.rows[0].insertCell(characterTbl.rows[0].cells.length);
	td.style.textAlign = "center";
	td.style.border = "0px";
	td.style.width="100px";
	var imgIcon = document.createElement("img");
	//imgIcon.id = document.getElementById("type_TextName").value;
	imgIcon.style.width = "100px";
	imgIcon.style.height = "100px";
	imgIcon.src = "<?php echo get_template_directory_uri()?>/images/bg.jpg";
	imgIcon.attachEvent("onclick",onImageClick);
	
	td.appendChild(imgIcon);
	

	var imgTitle = document.createElement("span");
	//imgTitle.innerText = document.getElementById("type_TextName").value;
	imgTitle.innerText = "image1111111111111" + characterTbl.rows[0].cells.length;
	imgTitle.attachEvent("onclick",onImageClick);
	
	//imgTitle.style.width += imgTitle.offsetWidth + 100; 
	td.appendChild(document.createElement("br"))
	td.appendChild(document.createElement("br")) 
	td.appendChild(imgTitle);

	characterTbl.style.width = (characterTbl.offsetWidth + td.offsetWidth) + "px" ;
	characterTbl.parentNode.style.height = (td.offsetHeight + 20) + "px";
}
function onImageClick()
{
	//alert(0);
}
function getPageOffsetLeft(object) {
	  var x;
	  // Return the x coordinate of an element relative to the page.
	  x = object.offsetLeft;
	  
	  if (object.offsetParent != null)
	    x += getPageOffsetLeft(object.offsetParent);
	  return x;
	}
function getPageOffsetTop(object) {
	  var x;
	  // Return the x coordinate of an element relative to the page.
	  x = object.offsetTop;
	  
	  if (object.offsetParent != null)
	    x += getPageOffsetTop(object.offsetParent);
	  return x;
	}
/*function onflashout()
{
	if(window.event.srcElement.tagName == "IFRAME")
		return;
	
	var flash = document.getElementById("flash");
	var left = getPageOffsetLeft(flash);
	var top = getPageOffsetTop(flash);
	var iframe = document.getElementById("iframe");

	var iframeLeft = getPageOffsetLeft(iframe);
	var iframeTop = getPageOffsetTop(iframe);

	if(window.event.clientX > iframeLeft && window.event.clientX < iframeLeft + iframe.offsetWidth && 
			window.event.clientY > iframeTop && window.event.clientY < iframeTop + iframe.offsetHeight)
		return;
	if(window.event.clientX < left || window.event.clientY < top)
	{
		$("#iframe").slideUp(500);
		//iframe.style.display = "none";
	}
	else if(window.event.clientX > left + flash.offsetWidth || window.event.clientY > top + flash.offsetHeight)
	{
		//iframe.style.display = "none";
		$("#iframe").slideUp(500);
	}
	else
	{
		//iframe.style.display = "inline";
		$("#iframe").slideDown(500);
	}
}
function onflashover()
{
	$("#iframe").slideDown(500);
	//document.getElementById("iframe").style.display = "inline";
}*/
function onFullProfile()
{
	
	location.href = "<?php echo home_url( '/' )?>/profile/";
}
var emapInfo = new Array();
emapInfo[0] = {x:20 , y:20};
emapInfo[1] = {x:20 , y:25};
emapInfo[2] = {x:25 , y:25};
emapInfo[3] = {x:25 , y:30};
emapInfo[4] = {x:30 , y:30};
emapInfo[5] = {x:30 , y:35};
function onimgclick(index)
{
	
	var mapInfo = document.getElementById("emapInfo");
	mapInfo.value = "";
	
	for(i = 0; i < emapInfo.length; i++)
	{
		mapInfo.value = mapInfo.value + emapInfo[i].x + ")" + emapInfo[i].y + "/"; 
	}
	var focusValue = document.getElementById("focusValue");
	focusValue.value = "";
	focusValue.value = emapInfo[index].x + "/" + emapInfo[index].y;
	document.aform.action = "<?php echo home_url( '/' )?>/e-map/";
	document.aform.submit();
}
function onClientName()
{
	
	<?php
	if(!isset($_SESSION['user_id']))
	{ 
	?>
		alert("Please do login!");
		return;
	<?php 
	}?>
	document.getElementById("programerID").value = g_user_id;
	document.getElementById("programId").value = g_program_id;
	document.aform.action = "<?php echo home_url()?>/profile/";
	document.aform.submit();
}
function vodTitleClick(vodTitle)
{
	
	if(vodTitle.trim() != "" && vodTitle.trim() != "null")
	{
		var video_player = document.getElementById("video_player");
		if(video_player == null || video_player == undefined)
		{
			var playertd = document.getElementById("playertd");
			playertd.innerHTML = "";
			
			video_player = document.createElement("div");
			video_player.id = "video_player";
			playertd.appendChild(video_player);
		}
		
		var parameters = {
		    src: "<?php echo RTMP_WOWZA?>"+"vod/mp4:" + vodTitle
		    , autoPlay: "true"
		    , streamType: "vod"
	        , bgColor: "#000000"
		};

		swfobject.embedSWF(
	        "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf"
	        , "video_player"
	        , 650
	        , 450
	        , "10.1.0"
	        , "<?php echo get_template_directory_uri()?>/expressInstall.swf"
	        , parameters
	        , {
	            allowFullScreen: "true",            
	            wmode: "direct"
	        }
	        , {
	            name: "StrobeMediaPlayback"
	        }
	    );
	}
	else
	{
		var pleasewaitDiv = document.getElementById("pleasewaitDiv");
		
		if(pleasewaitDiv == null || pleasewaitDiv == undefined)
		{
			var playertd = document.getElementById("playertd");
			playertd.innerHTML = '<div id = "pleasewaitDiv" style="background-color: #000000; position:absolute; top:200px; left:43%; text-align: center; color:#ffffff">Please,wait...</div>';
		}
	}
}
function onVodClick(program_id,channel_id)
{
	var ch = document.getElementById(channel_id);
	var inputTag = ch.getElementsByTagName("input");
	var channelUrlStr = inputTag[0].value;
	
	document.getElementById("hidChannelId").value = channel_id;
	document.getElementById("programId").value = program_id;
	document.getElementById("liveVod").value = "vod";
	document.aform.action = channelUrlStr;
	document.aform.submit();
}
function onVoteClick(obj)
{
	
	obj.disabled = true;
	if(document.getElementById("voteimg").style.opacity == "0.6")
	{
		alert("already registered!!!!!");
		return;
	}
	var program_id = "<?php echo $program_id?>";
	<?php
	if(!isset($_SESSION['user_id']))
	{?>
		alert("Please,do login!");
		return;
	<?php } 
	?>
	<?php
	if(isset($_SESSION['program_id']) && $_SESSION['program_id'] == $program_id) 
	{?>
		alert("Already voted.");
		return;
	<?php }
	else if(!isset($program_id) || $program_id == "")
	{?>
		alert("Program isn't registered in this time.");
		return;
	<?php }?>
	//
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
			//alert(xmlhttp.responseText);
			document.getElementById("voteimg").disabled = false;
		   document.getElementById("voteimg").style.opacity = "0.6"; 
		}
	  }
	  
	
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxRegVote.php?program_id=" + program_id,true);
	xmlhttp.send();
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
		if(g_program_id == "")
		{
			alert("Registered program does not exist!");
			return;
		}
		for(i = 0; i < progInfo.length; i++)
		{
			if(progInfo[i].program_id == g_program_id)
			{
				/*title = progInfo[i].title;
				var ch = document.getElementById("<?php echo $channel_id?>");
				var inputTag = ch.getElementsByTagName("input");
				channelUrlStr = inputTag[0].value;*/
				
				title = "Such an interesting video on YepLive!";
				channelUrlStr = window.location.toString();
				
				break;
			}
		}

		var liveVod = "live";
		for(i = 0; i < progInfo.length; i++)
		{
			if(progInfo[i].program_id == g_program_id)
			{
				var clientStartTime = new Date(fn_dateConvert(progInfo[i].start_time));
				var clientEndTime = new Date(0);

				if(progInfo[i].end_time.trim() == "")
				{
				}
				else
				{
					clientEndTime.setTime(fn_dateConvert(progInfo[i].end_time));
				}

			var currentDate = new Date();
			
			if(clientEndTime.getTime() == 0 && currentDate.getTime() - clientStartTime.getTime() < 5 * 3600 * 1000)
				liveVod = "live";
			else
				liveVod = "vod";
			}
		}
		
			
		//channelUrlStr = channelUrlStr + "?hidChannelId=" + "<?php echo $channel_id?>" + "&liveVod=" + liveVod + "&programId=" + g_program_id;
		
	$.ajax({
		type: "POST",
	  	url: "<?php echo home_url()?>/facebook_sendpost/",
	  	data: { channelUrl: channelUrlStr , title: title },  
	  	success: function(result) {
			alert("Shared on Facebook!");
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
		if(g_program_id == "")
		{
			alert("Registered program does not exist!");
			return;
		}
		for(i = 0; i < progInfo.length; i++)
		{
			if(progInfo[i].program_id == g_program_id)
			{
				//title = progInfo[i].title;
				/*var ch = document.getElementById("<?php echo $channel_id?>");
				var inputTag = ch.getElementsByTagName("input");
				channelUrlStr = inputTag[0].value;*/
				
				title = "Such an interesting video on YepLive!";
				channelUrlStr = window.location.toString();
				
				break;
			}
		}

		var liveVod = "live";
		for(i = 0; i < progInfo.length; i++)
		{
			
			if(progInfo[i].program_id == g_program_id)
			{
				var clientStartTime = new Date(fn_dateConvert(progInfo[i].start_time));
				var clientEndTime = new Date(0);

				if(progInfo[i].end_time.trim() == "")
				{
				}
				else
				{
					clientEndTime.setTime(fn_dateConvert(progInfo[i].end_time));
				}

				var currentDate = new Date();
				
				if(clientEndTime.getTime() == 0 && currentDate.getTime() - clientStartTime.getTime() < 5 * 3600 * 1000)
					liveVod = "live";
				else
					liveVod = "vod";
			}
			
		}
			
			//channelUrlStr = channelUrlStr + "?hidChannelId=" + "<?php echo $channel_id?>" + "&liveVod=" + liveVod + "&programId=" + g_program_id;
			
	$.ajax({
		type: "POST",
	  	url: "<?php echo home_url()?>/twitter_sendpost/",
	  	data: { channelUrl: channelUrlStr , title: title},
	  	success: function(result) {
		alert("Shared on Twitter!");
	  		var msg = "I promoted " + title +" to " + result + " people on twitter!";
		  	
	  		send( msg );				
			log( 'You: ' + msg );
			document.getElementById("log").scrollTop = 0;
	  	}
	});	
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

function fn_addFacebookFriend()
{
	<?php 
	if(!isset($_SESSION['user_id']))
	{?>
		alert("Please,do login!");
		return;
	<?php 
	}
	if(!isset($_SESSION['facebook_id']) || $_SESSION['facebook_id'] == "")
	{?>
		alert("You have to join in facebook!");
		return;
	<?php 
	}?>
	
	if(g_program_id == "")
	{
		alert("There is no programmer to be friend!");
		return;
	}
	for(i = 0; i < progInfo.length; i++)
	{
		if(progInfo[i].program_id == g_program_id)
		{
			title = progInfo[i].title;
			var ch = document.getElementById("<?php echo $channel_id?>");
			var inputTag = ch.getElementsByTagName("input");
			channelUrlStr = inputTag[0].value;
			break;
		}
	}

	var programmer_facebook_id = "";
	
	for(i = 0; i < progInfo.length; i++)
	{
		if(progInfo[i].program_id == g_program_id)
		{
			programmer_facebook_id = progInfo[i].facebook_id;
			break; 
		}
	}
	
	if(programmer_facebook_id == "")
	{
		alert("This Programmer does not join in facebook!");
		return;
	}	
	var URL = "http://www.facebook.com/addfriend.php?id=" + programmer_facebook_id;
	var props = getWindowPosition(900, 600);
	window.open(URL, "", props);	
}

function fn_addTwitterFollowing()
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
	
	

	if(g_program_id == "")
	{
		alert("There is no programmer to be friend!");
		return;
	}
	
	var programmer_twitter_id = "";
	
	for(i = 0; i < progInfo.length; i++)
	{
		if(progInfo[i].program_id == g_program_id)
		{
			programmer_twitter_id = progInfo[i].twitter_id;
			break; 
		}
	}

	
	if(programmer_twitter_id == "")
	{
		alert("This user does not join in twitter!");
		return;
	}
	var URL = "https://twitter.com/intent/user?user_id=" + programmer_twitter_id;
	var props = getWindowPosition(900, 600);
	window.open(URL, "", props);	
}
function onThumbClick(program_id)
{
	var isExistLive = false;
	
	for(i = 0; i < progInfo.length; i++)
	{
		var thumbProgram = document.getElementById("thumbProgram"+ i.toString());
		
		if(progInfo[i].program_id == program_id)
		{
			
			document.getElementById("contentSpan").innerHTML = "Title : " + progInfo[i].title + "<br>";
			
			thumbProgram.style.marginLeft = "20px";
			
			thumbProgram.style.marginTop = "5px";
			
			thumbProgram.style.marginRight = "20px";
			
			
			thumbProgram.style.borderRadius = "10px";
			
			thumbProgram.style.backgroundColor = "#000000";
			
			var imgChild = thumbProgram.getElementsByTagName("img");
			
			imgChild[0].style.margin = "10px";
			imgChild[0].style.borderRadius= "10px";
			imgChild[0].style.marginBottom = "0px";
			
			var thumbTitle = document.getElementById("thumbTitle" + i.toString());
			thumbTitle.style.color = "silver";
			
			document.getElementById("program_title").innerHTML = progInfo[i].title;
			
			if(progInfo[i].user_login != "")
				document.getElementById("userLoginName").innerHTML = progInfo[i].user_login;
			else if(progInfo[i].facebook_name != "")
				document.getElementById("userLoginName").innerHTML = progInfo[i].facebook_name;
			else if(progInfo[i].twitter_name != "")
				document.getElementById("userLoginName").innerHTML = progInfo[i].twitter_name;
			
			if(progInfo[i].picture_path.trim() != "")
			{
				document.getElementById("programImage").src = progInfo[i].picture_path;
				
			}
			else if(progInfo[i].image_path.trim() != "")
			{
				document.getElementById("programImage").src = progInfo[i].image_path;
				
			}
			else if(progInfo[i].facebook_id != "")
			{
				document.getElementById("programImage").src = "https://graph.facebook.com/" + progInfo[i].facebook_id + "/picture";
				
			}
			else if(progInfo[i].twitter_img != "")
			{
				document.getElementById("programImage").src = progInfo[i].twitter_img;
				
			}

			var clientStartTime = new Date(fn_dateConvert(progInfo[i].start_time));
			
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

			var clientEndTime = new Date(0);

			if(progInfo[i].end_time.trim() == "")
			{
				
			}
			else
			{	
				clientEndTime.setTime(fn_dateConvert(progInfo[i].end_time));

				year = clientEndTime.getFullYear();
				month = clientEndTime.getMonth() + 1;
				intDate = clientEndTime.getDate();

				hours = clientEndTime.getHours();
				minutes = clientEndTime.getMinutes();
				seconds = clientEndTime.getSeconds();

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

				document.getElementById("contentSpan").innerHTML += "<br>End Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
			}
			
			var currentDate = new Date();

			g_channel_id = progInfo[i].channel_id;
			g_program_id = progInfo[i].program_id;
			g_user_id = progInfo[i].user_id;
			
			if(clientEndTime.getTime() == 0 && currentDate.getTime() - clientStartTime.getTime() < 5 * 3600 * 1000) //if live
			{	
					
					var video_player = document.getElementById("video_player");
					if(video_player == null || video_player == undefined)
					{
						var playertd = document.getElementById("playertd");
						playertd.innerHTML = "";
						
						video_player = document.createElement("div");
						video_player.id = "video_player";
						playertd.appendChild(video_player);
					}
					
					var parameters;
					if(progInfo[i].isMobile == "0")
					{
						parameters = {
						    src: "<?php echo RTMP_WOWZA?>livestream/" + progInfo[i].channel_id + "_" + progInfo[i].program_id + "_" + progInfo[i].user_id
						    , autoPlay: "true"
						    , streamType: "live"
					        , bgColor: "#000000"
						};
					}
					else
					{
						parameters = {
							    src: "<?php echo RTMP_WOWZA?>livemobile/" + progInfo[i].channel_id + "_" + progInfo[i].program_id + "_" + progInfo[i].user_id
							    , autoPlay: "true"
							    , streamType: "live"
						        , bgColor: "#000000"
							};
					}


					swfobject.embedSWF(
				        "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf"
				        , "video_player"
				        , 580
				        , 420
				        , "10.1.0"
				        , "<?php echo get_template_directory_uri()?>/expressInstall.swf"
				        , parameters
				        , {
				            allowFullScreen: "true",            
				            wmode: "direct"
				        }
				        , {
				            name: "StrobeMediaPlayback"
				        }
				    );
					
					if(SetIntervalId != null && SetIntervalId != undefined)
						clearInterval(SetIntervalId);

					startDate = new Date(clientStartTime.getTime());
					endDate = new Date(clientEndTime.getTime());
					
					timeCount();
					SetIntervalId = setInterval("timeCount();",1000);
					
					var countimg = document.getElementById("countimg");
					var connectcount = document.getElementById("connectcount");
					var timedurationimg = document.getElementById("timedurationimg");
					var timeduration = document.getElementById("timeduration");

					countimg.style.opacity = "1";
					connectcount.style.opacity = "1";
					timedurationimg.style.opacity = "1";
					timeduration.style.opacity = "1";
					connectcount.innerHTML = progInfo[i].connect_count;
			    
					var chatRange = document.getElementById("chatRange");

					
				    var chat = document.getElementById("chat");
				    	chat.style.display = "inline";
				    	
				    if("<?php echo $_SESSION['user_id']?>" != "")
				    {	
				    	var messageTr = document.getElementById("messageTr");
				    		messageTr.style.display = "inline";
				    }
				    var vodlinks = document.getElementsByName("vodlinks");

					if(vodlinks != null && vodlinks != undefined)
					{
						
						while(vodlinks.length > 0)
						{
							chatRange.removeChild(vodlinks[0]);
							vodlinks = document.getElementsByName("vodlinks");
							
						}

					}
					
					var brTags = chatRange.getElementsByTagName("br");
					
					
					if(brTags != null && brTags != undefined)
					{
						while(brTags.length > 0)
						{
							brTags[0].parentNode.removeChild(brTags[0]);
							brTags = chatRange.getElementsByTagName("br");
						}
					}
					isExistLive = true;
					
					//alert(2);
			}	
			else		//if vod
			{		
				/*var video_player = document.getElementById("video_player");
				if(video_player == null || video_player == undefined)
				{
					var playertd = document.getElementById("playertd");
					playertd.innerHTML = "";
					
					video_player = document.createElement("div");
					video_player.id = "video_player";
					playertd.appendChild(video_player);
				}*/
				if(progInfo[i].end_time.trim() != "" && (progInfo[i].vod_path.trim() == "" || progInfo[i].vod_path.trim() == "null")) //if temp
				{
					var pleasewaitDiv = document.getElementById("pleasewaitDiv");
					
					if(pleasewaitDiv == null || pleasewaitDiv == undefined)
					{
						var playertd = document.getElementById("playertd");
						playertd.innerHTML = '<div id = "pleasewaitDiv" style="background-color: #000000; position:absolute; top:200px; left:43%; text-align: center; color:#ffffff">Please,wait...</div>';
					}
				}
				else // if real vod
				{
					var video_player = document.getElementById("video_player");
					if(video_player == null || video_player == undefined)
					{
						var playertd = document.getElementById("playertd");
						playertd.innerHTML = "";
						
						video_player = document.createElement("div");
						video_player.id = "video_player";
						playertd.appendChild(video_player);
					}
					
					var parameters = {
						    src: "<?php echo RTMP_WOWZA?>vod/mp4:" + progInfo[i].vod_path.split(",")[0]
						    , autoPlay: "true"
						    , streamType: "vod"
					        , bgColor: "#000000"
						};
						swfobject.embedSWF(
					        "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf"
					        , "video_player"
					        , 580
					        , 420
					        , "10.1.0"
					        , "<?php echo get_template_directory_uri()?>/expressInstall.swf"
					        , parameters
					        , {
					            allowFullScreen: "true",            
					            wmode: "direct"
					        }
					        , {
					            name: "StrobeMediaPlayback"
					        }
					    );
					    
					    if(SetIntervalId != null && SetIntervalId != undefined)
							clearInterval(SetIntervalId);
						
						var countimg = document.getElementById("countimg");
						var connectcount = document.getElementById("connectcount");
						var timedurationimg = document.getElementById("timedurationimg");
						var timeduration = document.getElementById("timeduration");

						countimg.style.opacity = "0";
						connectcount.style.opacity = "0";
						timedurationimg.style.opacity = "0";
						timeduration.style.opacity = "0";
						
						
						var chatRange = document.getElementById("chatRange");
						
					    var chat = document.getElementById("chat");
					    	chat.style.display = "none";
					    var messageTr = document.getElementById("messageTr");
					    	messageTr.style.display = "none";
					    
						var vodlinks = document.getElementsByName("vodlinks");

						if(vodlinks != null && vodlinks != undefined)
						{
							
							while(vodlinks.length > 0)
							{
								chatRange.removeChild(vodlinks[0]);
								vodlinks = document.getElementsByName("vodlinks");
								
							}

						}
						
						var brTags = chatRange.getElementsByTagName("br");
						
						
						if(brTags != null && brTags != undefined)
						{
							while(brTags.length > 0)
							{
								brTags[0].parentNode.removeChild(brTags[0]);
								brTags = chatRange.getElementsByTagName("br");
							}
						}
						var voditems = progInfo[i].vod_path.split(",");

						if(voditems[0] != "")
						{
							for(m = 0; m < voditems.length; m++)
							{
								//<a name="vodlinks" href="javascript:void(0);" style="padding-left:10px; line-height: 25px;" onclick="vodTitleClick('<?php echo $vodpaths[$i]?>');"><?php echo $vodpaths[$i]?></a>
				        		//<br>
				        		
				        		var aTag = document.createElement("a");
				        		aTag.style.display = "none";
				        		aTag.name = "vodlinks";
				        		aTag.href = "javascript:vodTitleClick('" + voditems[m]  + "')";
				        		aTag.style.paddingLeft = "10px";
				        		aTag.style.lineHeight = "25px";
				        		aTag.innerHTML = voditems[m];
								
				        		var brTag = document.createElement("br");
				        		
				        		chatRange.appendChild(aTag);
				        		chatRange.appendChild(brTag);
				        		
							}
						}
				}	
			}
			
		}
		else
		{
			thumbProgram.style.marginLeft = "20px";
			thumbProgram.style.marginTop = "15px";
			thumbProgram.style.marginRight = "20px";
			thumbProgram.style.backgroundColor = "#dddddd";

			var imgChild = thumbProgram.getElementsByTagName("img");
			imgChild[0].style.margin = "0px";
			imgChild[0].style.borderRadius = "10px";
			var thumbTitle = document.getElementById("thumbTitle" + i.toString());
			thumbTitle.style.color = "black";
			
		}
	}
	if(isExistLive == true)
		onConnectChatServer();
}
function fn_dateConvert(dateStr)
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
function polling()
{

//	alert(0);	

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
		    if(xmlhttp.responseText.trim() != "")
		    {
			    delete progInfo;
			    progInfo = null;
			    progInfo = new Array();
				var result = xmlhttp.responseText.split("~");
				
				for(i = 0; i < result.length - 1; i++)
				{
					var returnValue = result[i].split("&");
						
						
						progInfo[ progInfo.length ] = {
							program_id : returnValue[0],
							channel_id :returnValue[1],
							user_id : returnValue[2],
							title : returnValue[3],
							image_path : returnValue[4],
							start_time : returnValue[5],
							end_time : returnValue[6],
							vod_enable : returnValue[7],
							vod_path : returnValue[8],
							vote : returnValue[9],
							latitude : returnValue[10],
							longitude : returnValue[11],
							connect_count : returnValue[12],
							facebook_id : returnValue[13],
							facebook_name : returnValue[14],
							twitter_id : returnValue[15],
							twitter_img : returnValue[16],
							twitter_name : returnValue[17],
							user_login :returnValue[18],
							isMobile : returnValue[19],
							picture_path : returnValue[20]
					};
				}
				
				var thumbDiv = document.getElementById("thumbDiv");

				thumbDiv.innerHTML = "";
				thumbDiv.style.width = progInfo.length * 200 + "px";

				
				var isFlag = "";
				
				var currTime = new Date();
				
				for(k = 0; k < progInfo.length; k++)
				{
					isFlag = "";
					var tempStartTime = fn_dateConvert(progInfo[k].start_time);
					//var tempEndTime = fn_dateConvert(progInfo[k].end_time);
					
					if(progInfo[k].end_time.trim() == "" && (currTime.getTime() - tempStartTime < 5 * 3600 * 1000))
						isFlag = "live";
					else
						isFlag = "vod";	
					
					if(progInfo[k].program_id == g_program_id)
					{
						if(progInfo[k].end_time.trim() == "")
							endDate.setTime(0);
						else
							endDate.setTime(fn_dateConvert(progInfo[k].end_time));
						
						document.getElementById("connectcount").innerHTML = progInfo[k].connect_count;
						
						var aTag = document.createElement("a");

						thumbDiv.appendChild(aTag);
						aTag.id = "thumbProgram" + k;
						aTag.href = "javascript:onThumbClick('" + progInfo[k].program_id + "')";
						
						aTag.style.cssFloat = "left";
						aTag.style.color = "silver";
						aTag.style.marginTop = "5px";
						aTag.style.marginLeft = "20px";
						aTag.style.marginRight = "20px";
						aTag.style.borderRadius = "10px";
						aTag.style.backgroundColor = "#000000";

						

						var imgTag = document.createElement("img");
						aTag.appendChild(imgTag);
						imgTag.style.width = "130px";
						imgTag.style.height = "130px";
						imgTag.style.margin = "10px";
						imgTag.style.borderRadius = "10px";
						imgTag.style.marginBottom = "0px";
						
						if(progInfo[k].image_path.trim() != "")
							imgTag.src = progInfo[k].image_path;
						else if(progInfo[k].picture_path.trim() != "")
							imgTag.src = progInfo[k].picture_path;
						else if(progInfo[k].facebook_id != "")
							imgTag.src = "https://graph.facebook.com/" + progInfo[k].facebook_id + "/picture";
						else if(progInfo[k].twitter_img != "")
							imgTag.src = progInfo[k].twitter_img;

						

						var divTag = document.createElement("div");
						aTag.appendChild(divTag);
						divTag.id = "thumbTitle" + k;
						divTag.style.paddingTop = "5px";
						divTag.style.paddingBottom = "5px";
						divTag.style.whiteSpace = "nowrap";
						divTag.style.width = "130px";
						divTag.style.overflow = "hidden";
						divTag.style.color = "silver";
						divTag.style.textOverflow = "ellipsis";
						divTag.innerHTML = progInfo[k].title;
						
						if(isFlag == "live")
							divTag.innerHTML = "(LIVE)" + progInfo[k].title;
						else if(isFlag == "vod")
							divTag.innerHTML = "(VOD)" + progInfo[k].title;

						var video_player = document.getElementById("video_player");
						if((video_player == null || video_player == undefined) &&  //if video_player not exist and real vod exist
								(progInfo[k].end_time.trim() != "" && (progInfo[k].vod_path.trim() != "" && progInfo[k].vod_path.trim() != "null")))
						{
							//alert(0);
							var pleaseWaitDiv = document.getElementById("pleasewaitDiv");
							pleaseWaitDiv.parentNode.innerHTML = "";
							
							onThumbClick(progInfo[k].program_id);
						}
						else if((video_player != null && video_player != undefined) &&  //if video_player exist and temp  exist
								(progInfo[k].end_time.trim() != "" && (progInfo[k].vod_path.trim() == "" || progInfo[k].vod_path.trim() == "null")))
						{
							//alert(progInfo[k].end_time + "," + progInfo[k].vod_path);
							var videoPlayer = document.getElementById("video_player");
							videoPlayer.parentNode.innerHTML = "";
							
							onThumbClick(progInfo[k].program_id);
						}
					}
					else
					{
						
						var aTag = document.createElement("a");

						thumbDiv.appendChild(aTag);
						aTag.id = "thumbProgram" + k;
						aTag.href = "javascript:onThumbClick('" + progInfo[k].program_id + "')";
						aTag.style.cssFloat = "left";
						aTag.style.color = "black";
						aTag.style.marginTop = "15px";
						aTag.style.marginLeft = "20px";
						aTag.style.marginRight = "20px";

						

						var imgTag = document.createElement("img");
						aTag.appendChild(imgTag);
						
						imgTag.style.width = "130px";
						imgTag.style.height = "130px";
						imgTag.style.borderRadius = "10px";
						
						if(progInfo[k].image_path.trim() != "")
							imgTag.src = progInfo[k].image_path;
						else if(progInfo[k].picture_path.trim() != "")
							imgTag.src = progInfo[k].picture_path;
						else if(progInfo[k].facebook_id != "")
							imgTag.src = "https://graph.facebook.com/" + progInfo[k].facebook_id + "/picture";
						else if(progInfo[k].twitter_img != "")
							imgTag.src = progInfo[k].twitter_img;

						

						var divTag = document.createElement("div");
						aTag.appendChild(divTag);
						
						divTag.id = "thumbTitle" + k;
						divTag.style.paddingTop = "5px";
						divTag.style.paddingBottom = "5px";
						divTag.style.whiteSpace = "nowrap";
						divTag.style.width = "130px";
						divTag.style.overflow = "hidden";
						divTag.style.color = "black";
						divTag.style.textOverflow = "ellipsis";
						divTag.innerHTML = progInfo[k].title;
						
						if(isFlag == "live")
							divTag.innerHTML = "(LIVE)" + progInfo[k].title;
						else if(isFlag == "vod")
							divTag.innerHTML = "(VOD)" + progInfo[k].title;
							
						
					}
					

					
					/*aTag.onclick = function(){
							var progindex = this.id.substr("thumbProgram".length , this.id.length);
							onThumbClick(progInfo[ parseInt(progindex)].program_id );
						}
					*/
				}
				
				if(g_program_id == "")
				{
					g_program_id = progInfo[0].program_id;
					ChatServer.connect();
					//g_program_id = progInfo[0].program_id;
					onThumbClick(progInfo[0].program_id);
				}
				//if(isFlag == true)
					//onThumbClick(g_program_id);	
		    }
			 
		}
	  }
	  
	
	xmlhttp.open("GET","<?php echo home_url()?>/program_polling?channel_id=" + "<?php echo $channel_id?>",true);
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
<input type="hidden" id="programerID" name="programerID" value="">
<input type="hidden" id="programId" name="programId" value="">
<input type="hidden" id="liveVod" name="liveVod" value="">
<div align="center" id="rootDiv">
	<table id="rootTbl" style="width:1000px; background-color: #000000; border-radius:10px; margin-bottom: 50px;" cellpadding="0" cellspacing="0" border="0" border-color="silver" >
	  <tr>
	    <td width="63%">
	      <table>
	        <tr>
	          <td style="padding-left: 20px; padding-top: 10px;" align="left">
	          	
	          	<?php
	          			
				        	$flag = false;
							for($i = 0; $i < count($progInfo["user_id"]); $i++)
							{
								if($progInfo["program_id"][$i] == $program_id)
								{
									$flag = true;
									?>
									<h1 id="program_title" style="color:silver;"><?php echo $progInfo['title'][$i]?></h1>
									<?php 
								}
							}
							if($flag == false)
							{
								?>
								<h1 id="program_title" style="color:silver;">No Event</h1>
								<?php 
							}
					?>
	          </td>
	        </tr>
	        
	        
	        <tr>
	          <td style="text-align: center; width:580px; height:420px; position: relative;" id="playertd">
	          <?php if($liveVod == "vod" && (trim($vod_path) == "" || trim($vod_path) == "null"))
	          {?>
	          	<div id = "pleasewaitDiv" style="background-color: #000000; position:absolute; top:200px; left:43%; text-align: center; color:#ffffff;">Please,wait...</div>
	          <?php 
	          }else{?>
				<div id="video_player" >
  				</div>
				<script type="text/javascript">
					<?php
					if($liveVod == "live")
					{ 
					?>
					var parameters;
					if("<?php echo $isMobile?>" == "0")
					{
						parameters = {
						    src: "<?php echo RTMP_WOWZA?>livestream/<?php echo $liveUrl?>"
						    , autoPlay: "true"
						    , streamType: "live"
					        , bgColor: "#000000"
							, minContinuousPlayback : 5
						};
					}
					else
					{
						parameters = {
							    src: "<?php echo RTMP_WOWZA?>livemobile/<?php echo $liveUrl?>"
							    , autoPlay: "true"
							    , streamType: "live"
						        , bgColor: "#000000"
								, minContinuousPlayback : 5
							};
					}
					swfobject.embedSWF(
				        "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf"
				        , "video_player"
				        , 580
				        , 420
				        , "10.1.0"
				        , "<?php echo get_template_directory_uri()?>/expressInstall.swf"
				        , parameters
				        , {
				            allowFullScreen: "true",            
				            wmode: "direct"
				        }
				        , {
				            name: "StrobeMediaPlayback"
				        }
				    );
					<?php }
					else {
					
?>
					
					var parameters = {
						    src: "<?php echo RTMP_WOWZA?>vod/mp4:" + "<?php echo $vod_path?>".split(",")[0]
						    , autoPlay: "true"
						    , streamType: "vod"
					        , bgColor: "#000000"
						};
						swfobject.embedSWF(
					        "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf"
					        , "video_player"
					        , 580
					        , 420
					        , "10.1.0"
					        , "<?php echo get_template_directory_uri()?>/expressInstall.swf"
					        , parameters
					        , {
					            allowFullScreen: "true",            
					            wmode: "direct"
					        }
					        , {
					            name: "StrobeMediaPlayback"
					        }
					    );
					<?php }?>
					
				</script> 
				<?php }?>
			  </td>
			</tr>
			<tr>
			  <td>
				<table width="100%" border="0">
				  <tr>
				    <td width="30%" rowspan="4" style="padding-left: 3px;">
				    	
				      <img id="programImage" src="<?php
				      if($picture_path != "") 
				      	echo $picture_path;
				      else{
							for($i = 0; $i < count($progInfo["user_id"]); $i++)
							{
								if($progInfo["program_id"][$i] == $program_id)
								{
									if($progInfo["image_path"][$i] != "")
										echo $progInfo["image_path"][$i];
									else if($progInfo["facebook_id"][$i] != "")
										echo "https://graph.facebook.com/" . $progInfo["facebook_id"][$i] . "/picture";
									else if($progInfo["twitter_img"][$i] != "")
										echo $progInfo["twitter_img"][$i];
									break;
								}
							}
						}
				      ?>" style="width:200px; height:200px; border-radius:10px;" >
				    </td>
				    <td colspan="2" align="center">
				      <table>
				        <tr>
				          <td>
				          	<img id="timedurationimg" src="<?php echo get_template_directory_uri(); ?>/images/time_temp.png" style="<?php if($liveVod == "vod"){?> opacity:0; <?php }?> height:28px;" />
				          </td>
				          <td>
				          
				            <span id="timeduration" style="color:green; <?php if($liveVod == "vod"){?> opacity:0; <?php }?> font-size: 20px; font-weight: bold;">00:00:00</span>
				            
				          </td>
				          <td style="padding-left: 30px;">
				          
				            <img id="countimg" src="<?php echo get_template_directory_uri(); ?>/images/count_temp.png" style="<?php if($liveVod == "vod"){?> opacity:0; <?php }?>height:24px;" />
				            
				          </td>
				          <td>
				          
				            <span id="connectcount" style="color:green; font-size: 20px; <?php if($liveVod == "vod"){?> opacity:0; <?php }?> font-weight: bold;">
				            
				            <?php
				            if($liveVod == "live")
							{ 
					            for($i = 0; $i < count($progInfo["user_id"]); $i++)
								{
									if($progInfo["program_id"][$i] == $program_id)
									{
										echo $progInfo["connect_count"][$i];
										break;
									}
								}
							}?>
				            </span>
				            
				          </td>
				        </tr>
				      </table>
				    </td>
				  </tr>
				  <tr>
				    <td width="50%" height="70px;" style="padding-left: 20px;" align="left">
				      <a href="javascript:void(0);" id="clientName" onclick="onClientName();">
				        <h1 id="userLoginName" style="color:silver;"><?php
				        if($userlogin != "") 
				        	echo $userlogin;
				        else if($facebook_name != "")
				        	echo $facebook_name;
				        else if($twitter_name != "")
				        	echo $twitter_name;
				        ?>
				        </h1>
				      </a>
				    </td>
				    <td  align="center" >
				      <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
					  <g:plusone annotation="none" href="<?php echo $_SERVER['REQUEST_URI'] ?>"></g:plusone>
				      <img src="<?php echo get_template_directory_uri(); ?>/images/tweet_logo.jpg" onclick="fn_twitterPost();"   style="cursor:pointer; float:right;width:25px; height:25px;">
				      <img src="<?php echo get_template_directory_uri(); ?>/images/facebook_logo.jpg"  onclick="fn_facebookPromote();"   style="cursor:pointer;float: right;width:25px; height:25px;">
				      <img id="voteimg" src="<?php echo get_template_directory_uri(); ?>/images/vote_up.png" style="cursor:pointer;width:25px; height:25px; float: right; <?php if(isset($_SESSION['program_id']) && $_SESSION['program_id'] == $program_id){?> opacity:0.6; <?php }?>" onclick="if(this.style.opacity != '0.6' ) onVoteClick(this);" onmousedown="if(this.style.opacity == '0.6' || this.disabled == true) return false; this.style.opacity='0.6' this.src='<?php echo get_template_directory_uri(); ?>/images/vote_down.png'" onmouseup="if(this.style.opacity == '0.6' || this.disabled == true) return false; this.style.opacity='0.6'; this.src='<?php echo get_template_directory_uri(); ?>/images/vote_up.png'" onmouseout="if(this.style.opacity == '0.6' || this.disabled == true) return false;this.src='<?php echo get_template_directory_uri(); ?>/images/vote_up.png'">
						
					</td>
				  </tr>
				  <tr>
				    <td colspan="2" style="padding-left: 20px;" align="left">
				      <span style="color:silver; line-height: 20px; " id="contentSpan" name="contentSpan">
				        <?php
				        	
							for($i = 0; $i < count($progInfo["user_id"]); $i++)
							{
								if($progInfo["program_id"][$i] == $program_id)
								{
									echo "Title : " . $progInfo['title'][$i];
									echo "<br>";
								?>
									<script>
										var clientStartTime = new Date(fn_dateConvert("<?php echo $progInfo["start_time"][$i]?>"));
										var clientEndTime = new Date(0);
										 
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

										
										if("<?php echo $progInfo["end_time"][$i]?>" != "")
										{
											clientEndTime.setTime(fn_dateConvert("<?php echo $progInfo["end_time"][$i]?>"));
											
											year = clientEndTime.getFullYear();
											month = clientEndTime.getMonth() + 1;
											intDate = clientEndTime.getDate();
	
											hours = clientEndTime.getHours();
											minutes = clientEndTime.getMinutes();
											seconds = clientEndTime.getSeconds();
	
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
	
											document.getElementById("contentSpan").innerHTML += "<br>" + "End Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
										} 
				      				</script>
								<?php }
							}?>
				      </span>
				      
				    </td>
				  </tr>
				  <tr>
				    <td colspan="2" valign="bottom" align="center">
				      <img  onclick="fn_addFacebookFriend();"  src="<?php echo get_template_directory_uri(); ?>/images/facebook_friends.png" style="cursor:pointer;height:34px; cursor: pointer;">
				      <img  onclick="fn_addTwitterFollowing();"   src="<?php echo get_template_directory_uri(); ?>/images/twitter_follow.png" style="cursor:pointer;height:34px; cursor: pointer;">
				    </td>
				  </tr>
				</table>
			  </td>
	        </tr>
	      </table>	
	    </td>
	    <td style="width:1px; background-color: silver;">
	    </td>
	    <td width="*">
            <div style="width: 100%; height: 680px;">
                  <iframe style="width: 100%; height: 100%;" src="<?php echo DOMAIN_ROOT
                  ?>textchat/chat.html?username=<?php
                            if(is_user_logged_in())
                            {
                                $username = et_get_usernamebyid($_SESSION['user_id']);
                                $user_nicename = $username[0]->user_nicename;
                            }
                            else if($_SESSION['facebook_login'] == 1)
                                $user_nicename = $_SESSION['facebook_name'];
                            else if($_SESSION['twitter_login'] == 1)
                                $user_nicename = $_SESSION['twitter_name'];

                            echo $user_nicename
                  ?>&roomId=<?php echo $prog_id ?>"/>
            </div>
	    </td>
	  </tr>
		<tr>
			
			
		</tr>
	</table>

	
	<div style="overflow-x:auto; overflow-y:hidden; width:1100px; border:5px solid #bbbbbb;border-radius:10px; height:190px; vertical-align: middle; background-color: #dddddd; " valign="middle" align="left">
	<div id="thumbDiv" style="width:<?php echo count($progInfo['user_id']) * 200?>px; height:100%; " align="center">
	
	<?php
	 
	for($i = 0; $i < count($progInfo["user_id"]); $i++)
	{
		if($progInfo["program_id"][$i] != $program_id)
		{
	?>
		
		<a href="javascript:void(0);" id="thumbProgram<?php echo $i?>" onclick="onThumbClick('<?php echo $progInfo['program_id'][$i]?>');"  style="float: left; color:black; margin-top:15px; margin-left:20px; margin-right:20px">
		  <img style="width:130px;height:130px; border-radius:10px;" src = "<?php
		  if($progInfo["image_path"][$i] != "") 
		  	echo $progInfo["image_path"][$i];
		  else if($progInfo["picture_path"][$i] != "")
		  	echo $progInfo["picture_path"][$i];
		  else if($progInfo["facebook_id"][$i] != "")
		  	echo "https://graph.facebook.com/" . $progInfo["facebook_id"][$i] . "/picture";
		  else if($progInfo["twitter_img"][$i] != "")
		  	echo $progInfo["twitter_img"][$i];
		  ?>">
		  </img>
		  <div id="thumbTitle<?php echo $i?>" style="padding-top:5px;padding-bottom:5px;
		  white-space:nowrap; 
			width:130px; 
			overflow:hidden; 
			color:black;
			text-overflow:ellipsis;
		  "></div>
		  		<script>

				var currentTime = new Date();
				var clientStartTime = new Date(fn_dateConvert("<?php echo $progInfo["start_time"][$i]?>"));
				if("<?php echo $progInfo["end_time"][$i]?>" == "" && currentTime.getTime() - clientStartTime.getTime() < 5 * 3600 * 1000)
				{
					document.getElementById("thumbTitle" + "<?php echo $i?>").innerHTML = "(LIVE)" + "<?php echo $progInfo["title"][$i]?>";
				}
				else
				{
					document.getElementById("thumbTitle" + "<?php echo $i?>").innerHTML = "(VOD)" + "<?php echo $progInfo["title"][$i]?>";
				}
				
				
				</script>
		  
		</a>
	<?php }
	else{?>
		
		<a href="javascript:void(0);" id="thumbProgram<?php echo $i?>" onclick="onThumbClick('<?php echo $progInfo['program_id'][$i]?>');" style="float: left; margin-left:20px; margin-top:5px; margin-right:20px; color:silver;  border-radius:10px; background-color: #000000;  " title="Start Time:<?php echo $progInfo["start_time"][$i]; ?>    End Time:<?php echo $progInfo["end_time"][$i];?>">
		  <img style="width:130px;height:130px; margin: 10px;border-radius:10px; margin-bottom: 0px;" src = "<?php 
		  if($progInfo["image_path"][$i] != "") 
		  	echo $progInfo["image_path"][$i];
		  else if($progInfo["picture_path"][$i] != "")
		  	echo $progInfo["picture_path"][$i];
		  else if($progInfo["facebook_id"][$i] != "")
		  	echo "https://graph.facebook.com/" . $progInfo["facebook_id"][$i] . "/picture";
		  else if($progInfo["twitter_img"][$i] != "")
		  	echo $progInfo["twitter_img"][$i];
		  ?>"></img>
		  <div id="thumbTitle<?php echo $i?>" style="padding-top:5px;padding-bottom:5px;
		  white-space:nowrap; 
			width:130px; 
			overflow:hidden; 
			color:silver;
			text-overflow:ellipsis;
			"></div>
			<script>

			var currentTime = new Date();
			var clientStartTime = new Date(fn_dateConvert("<?php echo $progInfo["start_time"][$i]?>"));
			if("<?php echo $progInfo["end_time"][$i]?>" == "" && currentTime.getTime() - clientStartTime.getTime() < 5 * 3600 * 1000)
			{
				document.getElementById("thumbTitle" + "<?php echo $i?>").innerHTML = "(LIVE)" + "<?php echo $progInfo["title"][$i]?>";
			}
			else
			{
				document.getElementById("thumbTitle" + "<?php echo $i?>").innerHTML = "(VOD)" + "<?php echo $progInfo["title"][$i]?>";
			}
			
			
			</script>
			<?php
		  		/*if($progInfo["end_time"][$i] == "" && )
		  		{
		  			echo "(LIVE)" . $progInfo["title"][$i];
		  		}
		  		else
		  			echo "(VOD)" . $progInfo["title"][$i];
		  			*/
		  		?> 
			
		</a>
		<?php }
	}?>	
	</div>
	</div>
	
	
</div>
<br>
</form>
<script>
		var SetIntervalId;

		var startDate = new Date(0);
		var endDate = new Date(0);

		<?php if($start_time != "" && $end_time == "")
		{?>
			startDate.setTime(fn_dateConvert("<?php echo $start_time ?>"));
			timeCount();
			SetIntervalId = setInterval("timeCount();",1000);
		<?php }
		else if((!isset($program_id) || $program_id == "") && count($progInfo["user_id"]) > 0)
		{?>
			onThumbClick("<?php echo $progInfo["program_id"][0]?>");
		<?php }?>
		function timeCount()
		{
			var nowTime = new Date();
			var startTime = startDate.getTime();
			var endTime = endDate.getTime();
			
			if(endTime != 0  || (nowTime.getTime() - startTime >= 5 * 3600 * 1000)) // 
			{
				
				var max_connect_count = -1;
				var program_index = -1;
				for(i = 0; i < progInfo.length; i++)
				{
					var clientStartTime =  fn_dateConvert(progInfo[i].start_time);
					//var clientEndTime = fn_dateConvert(progInfo[i].end_time);
					
					//var currentDate = new Date();
					
					if(progInfo[i].end_time.trim() == "" && (nowTime.getTime() - clientStartTime < 5 * 3600 * 1000)) //if live then
					{
						if(max_connect_count < parseInt(progInfo[i].connect_count))
						{
							max_connect_count = parseInt(progInfo[i].connect_count);
							program_index = progInfo[i].program_id;
						}
					}
				}
				if(program_index > -1) // if live then
				{
					if(SetIntervalId != null && SetIntervalId != undefined)
						clearInterval(SetIntervalId);
					
					onThumbClick(program_index);
					return;
				}
				else //if vod then
				{
					
					for(i = 0; i < progInfo.length; i++)
					{
						if(max_connect_count < parseInt(progInfo[i].connect_count))
						{
							max_connect_count = parseInt(progInfo[i].connect_count);
							program_index = progInfo[i].program_id;
						}
					}
					
					if(program_index > -1)
					{
						
						if(SetIntervalId != null && SetIntervalId != undefined)
							clearInterval(SetIntervalId);
						onThumbClick(program_index.toString());
						return;
					}
					else // if Both of live and vod is nothing then.
					{
						
						if(SetIntervalId != null && SetIntervalId != undefined)
						{
							clearInterval(SetIntervalId);
							return;
						}
					}	
				}
			}
			

			var period = (nowTime.getTime() - startTime) / 1000;
			
			var hours = parseInt(period  /  3600);
			var minutes = period  % 3600;
				seconds = parseInt(minutes % 60);
				minutes = parseInt(minutes / 60);
			
			//var hours = nowTime.getHours();
			//var minutes = nowTime.getMinutes();
			//var seconds = nowTime.getSeconds();
			
			if(hours.toString().length == 1)
				hours = "0" + hours;
			if(minutes.toString().length == 1)
				minutes = "0" + minutes;
			if(seconds.toString().length == 1)
				seconds = "0" + seconds; 	
			document.getElementById("timeduration").innerHTML = hours + ":" + minutes + ":" + seconds;
			
			delete nowTime;
			nowTime = null;
			
		}
		 setInterval("polling();",5000);
</script>
<?php get_footer(); ?>