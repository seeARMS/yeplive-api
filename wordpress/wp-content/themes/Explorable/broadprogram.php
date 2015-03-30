<?php
/*

Template name: broadprogram

*/
get_header();

require_once(ABSPATH . WPINC . '/registration.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

global $wpdb, $user_ID;
	
	//$serverGMTTime = time() * 1000;
	/*$date = date('Y-m-d H-i-s', time() - 3600);
	echo "<script>alert('" . $date . "');</script>";
	$date = getdate(time() - 3600);
	//$date = getdate();
	echo "<script>alert('" . $date['year'] . "');</script>";
	echo "<script>alert('" . $date['mon'] . "');</script>";
	echo "<script>alert('" . $date['mday'] . "');</script>";
	echo "<script>alert('" . $date['hours'] . "');</script>";
	echo "<script>alert('" . $date['minutes'] . "');</script>";
	echo "<script>alert('" . $date['seconds'] . "');</script>";*/
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!link)
	{
		echo "mysql_connect error";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	
		/*$sql_query = "select 
							lct.category_name,
							lch.channel_name,
							lpr.channel_id,
							lpr.program_id,
							lpr.title,
							lpr.image_path,
							lpr.start_time,
							lpr.end_time,
							lpr.vod_enable,
							lpr.vod_path,
							lpr.vote,
							lpr.latitude,
							lpr.longitude
				  from 
							wp_program lpr 
				  inner join 
							wp_channel lch 
				  on  
							lpr.channel_id = lch.channel_id 
				  inner join 
							wp_category lct
				  on 
							lch.category_id = lct.category_id  
				  where 
							user_id = '" . $user_ID . "'
					order by lpr.end_time desc";
					*/
	$sql_query = "select
							lct.category_name,
							lch.channel_name,
							lpr.channel_id,
							lpr.program_id,
							lpr.title,
							lpr.image_path,
							lpr.start_time,
							lpr.end_time,
							lpr.vod_enable,
							lpr.vod_path,
							lpr.vote,
							lpr.latitude,
							lpr.longitude
				  from
							wp_program lpr
				  inner join
							wp_channel lch
				  on
							lpr.channel_id = lch.channel_id
				  inner join
							wp_category lct
				  on
							lch.category_id = lct.category_id
				  where
							user_id = '" . $_SESSION['user_id'] . "'
					order by lpr.end_time desc";
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}
	$userProgInfo = array("program_id" => array(), "category_name" => array(), "channel_name" => array(), "title" => array(), "image_path" => array(),"start_time" => array(),
					"end_time" => array() , "vod_enable" => array() , "vote" => array() , "latitude" => array(),"longitude" => array());
	
	$i = 0;
	
	while($row = mysql_fetch_array($result))
	{
		$userProgInfo["program_id"][$i] = $row['program_id'];
		$userProgInfo["category_name"][$i] = $row['category_name'];
		$userProgInfo["channel_name"][$i] = $row['channel_name'];
		$userProgInfo["channel_id"][$i] = $row['channel_id'];
		$userProgInfo["title"][$i] = $row['title'];
		$userProgInfo["image_path"][$i] = home_url() . $row['image_path'];
		$userProgInfo["start_time"][$i] = $row['start_time'];
		$userProgInfo["end_time"][$i] = $row['end_time'];
		$userProgInfo["vod_enable"][$i] = $row['vod_enable'];
		$userProgInfo["vod_path"][$i] = $row['vod_path'];
		$userProgInfo["vote"][$i] = $row['vote'];
		$userProgInfo["latitude"][$i] = $row['latitude'];
		$userProgInfo["longitude"][$i] = $row['longitude'];
		$i++;
	}
	mysql_free_result($result);
	mysql_close($link);
	
	$serverCurrGmtTime = getdate(); // server gmt time
	$serverCurrLocalTime = time() * 1000; // server current time
	
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
<script>
	
	var userProgInfo = new Array();
	
</script>
<style type="text/css">
table, td, th
{
	border:1px solid #d0d0d0;
}
.tblClass > tbody > tr > td:first-child{
	border-left:0px;
}

.tblClass > tbody > tr > td:last-child{
	border-right:0px;
}
.divClass {
	height:100px; 
	background-color: #dae1e7; 
	margin:20px; 
	border-radius:10px; 
	border:0px; 
}
.divClass > table{
	width:100%;
	height:100%; 
	margin:0px; 
	padding:0px;
	border-radius:10px; 
	border:0px;
}
.divClass > table  input[type="button"] {
	letter-spacing:4px;
	color:#ffffff;
	width:110px; 
	height:50px;
	border-radius:10px; 
	background: url('<?php echo get_template_directory_uri()?>/images/broadcast1.png');
}
</style>
<script>
var count = 0;
function onBtnAddProgram()
{
	document.aform.action = "<?php echo home_url( '/' )?>/add-program/";
	document.aform.submit();
}
function onUpdateProgram(obj)
{
	var index = -1;
	var updateButtons = document.getElementsByName("updateButton");
	for(i = 0; i < updateButtons.length; i++)
	{
		if(obj == updateButtons[i])
		{
			index = i;
			break;
		}
	}
	if(index == -1)
		return;
	document.getElementById("programIndex").value = userProgInfo[index].program_id;
	document.aform.action = "<?php echo home_url()?>/updateprogram/";
	document.aform.submit();
}
function onLive(broad_channel_id,broad_program_id)
{
	/*
	var ch = document.getElementById(ch_id);
	var inputTag = ch.getElementsByTagName("input");
	var channelUrlStr = inputTag[0].value;
	document.getElementById("hidChannelId").value = ch_id;
	document.aform.action = channelUrlStr;
	*/
	document.getElementById("broadCh_id").value = broad_channel_id;
	document.getElementById("broadProg_id").value = broad_program_id;
	document.aform.action = "<?php echo home_url()?>/broadcast/";
	document.aform.submit();
}
</script>
<form name="aform" method="post" style="width:100%;">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
<input type="hidden" value="" name="programIndex" id="programIndex">
<input type="hidden" value="" name="broadCh_id" id="broadCh_id">
<input type="hidden" value="" name="broadProg_id" id="broadProg_id">
	<div style="width:100%;" align="center">
		<table class="tblClass" style="border-collapse:collapse; -webkit-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		-moz-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		box-shadow: 0 10px 25px rgba(0,0,0,0.5);  width:80%; height:90%; margin-top:30px; margin-bottom:30px; border-color:red; border-radius:10px; background-color: #fff;display: block;
		width: 960px; left: 50%; border:1px solid #d0d0d0;"
		 border="0" cellspacing="0" cellpadding="10" >
			<tr style="height:50px;">
				<td style=" width:170px; border-right:0px; border-top:0px; border-top-left-radius:10px;">
					
				</td>
				<td valign="middle" style="width:750px;border-top:0px; border-left:0px;">
					<div style="width:100%;height:auto; position:relative;  height:50px;" align="right" valign="middle">
						
						<input type="button" value="Add Program" onclick="onBtnAddProgram();" style="position:absolute; right:20px; bottom:0px;  border-radius:25px; color:#ffffff;  font-size:large; font-family:serif; font-weight:500; word-spacing:10px;letter-spacing:2px; width:180px; height:50px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');">
					</div>
					
				</td>
			</tr>
			<tr style="background-color: #eceff1; ">
				<td colspan="2" style="height:400px; width:750px;">
					<div id="mainDiv" style="height:400px; overflow: auto;">
						<?php
						$date = date("Y-m-d H:i:s"); 
						$otherDate = date("Y-m-d H:i:s" , time() - 24 * 60 * 60);
						$count = 0;
						for($i = 0; $i < count($userProgInfo["channel_name"]); $i++)
						{
							
							if(!($userProgInfo["vod_enable"][$i] == "0" && $userProgInfo["end_time"][$i] < $date) && 
								!($userProgInfo["vod_enable"][$i] == "1" && $userProgInfo["end_time"][$i] < $otherDate))
							{
								$serverGmtEndTime = $userProgInfo["end_time"][$i];
								$serverGmtStartTime = $userProgInfo["start_time"][$i];
								
								
						?>
						<script>
								userProgInfo[ userProgInfo.length ] = {
								program_id : "<?php echo $userProgInfo['program_id'][$i]?>",
								category_name : "<?php echo $userProgInfo['category_name'][$i]?>",
								channel_name : "<?php echo $userProgInfo['channel_name'][$i]?>",
								title : "<?php echo $userProgInfo['title'][$i]?>",
								image_path : "<?php echo $userProgInfo['image_path'][$i]?>",
								start_time : "<?php echo $userProgInfo['start_time'][$i]?>",
								end_time : "<?php echo $userProgInfo['end_time'][$i]?>",
								vod_enable : "<?php echo $userProgInfo['vod_enable'][$i]?>",
								vod_path : "<?php echo $userProgInfo['vod_path'][$i]?>",
								vote : "<?php echo $userProgInfo['vote'][$i]?>",
								latitude : "<?php echo $userProgInfo['latitude'][$i]?>",
								longitude : "<?php echo $userProgInfo['longitude'][$i]?>"  
								};
						</script>
						<input type="hidden" id="hidProgramInfo" name="hidProgramInfo" value="<?php echo $userProgInfo['program_id'][$i] ?>">
						<div class="divClass" name="divClass">
							<table  >
								<colgroup>
									<col width="100px">
									<col width="*">
									<col width="120px">
									<col width="120px">
								</colgroup>
								<tr style="border:0px;">
									<td style="border:0px;">
										<img style="border-radius:10px; width:100px;height:100px;border:0px;" src="<?php echo $userProgInfo["image_path"][$i]?>">
									</td align="center">
									<td style="border:0px;" align="left">
										<div style="width:100%; min-height: 25px; line-height: 25px; padding-left: 15px;">
											<b>Title :</b><?php echo $userProgInfo["title"][$i]?>
										</div>
										<div  style="width:100%; min-height: 25px; line-height: 25px; padding-left: 15px;">
											Conference on <b><?php echo $userProgInfo["channel_name"][$i]?></b> in <b><?php echo $userProgInfo["category_name"][$i]?></b>
										</div>
										<div name="startTime"  style="width:100%; min-height: 25px; line-height: 25px; padding-left: 15px;">
											<b>Start Time :</b><span id="spanStartTime<?php echo $count?>" name="spanStartTime"><?php echo $userProgInfo["start_time"][$i]?></span>
											
										</div>
										<div name="endTime" style="width:100%; min-height: 25px; line-height: 25px; padding-left: 15px;">
											<b>End Time :</b><span id="spanEndTime<?php echo $count?>" name="spanEndTime"><?php echo $userProgInfo["end_time"][$i]?></span>
										</div>
									</td>
									<td align="center" style="border:0px;">
										<input type="button" name="updateButton" value="Edit"  disabled="disabled" style="opacity:0.6;" onclick="onUpdateProgram(this);">
									</td>
									<td style="border:0px;" align="center">
										
										<input type="button" name="liveButton" value="Live" onclick="onLive('<?php echo $userProgInfo["channel_id"][$i]?>','<?php echo $userProgInfo["program_id"][$i]?>');">
									</td>
								</tr>
							</table>
						</div>
						
						<script>

						
						var spanStartTime = document.getElementById("spanStartTime<?php echo $count?>");
						var spanEndTime = document.getElementById("spanEndTime<?php echo $count?>");
						
						var spStartDate = spanStartTime.innerHTML.split(" ")[0];
						var spStartTime = spanStartTime.innerHTML.split(" ")[1];
						var spEndDate = spanEndTime.innerHTML.split(" ")[0];
						var spEndTime = spanEndTime.innerHTML.split(" ")[1];

						
						
						var spYear = spStartDate.split("-")[0];
						var spMonth = spStartDate.split("-")[1];
						var spDate = spStartDate.split("-")[2];

						var spHours = spStartTime.split(":")[0];
						var spMinutes = spStartTime.split(":")[1];
						var spSeconds = spStartTime.split(":")[2];																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																																			

						if(spMonth.substring(0,1) == "0")
							spMonth = spMonth.substring(1,2);
						if(spDate.substring(0,1) == "0")
							spDate = spDate.substring(1,2);
						if(spHours.substring(0,1) == "0")
							spHours = spHours.substring(1,2);
						if(spMinutes.substring(0,1) == "0")
							spMinutes = spMinutes.substring(1,2);
						if(spSeconds.substring(0,1) == "0")
							spSeconds - spSeconds.substring(1,2);

						
						var svGmtTime = new Date(0);
						svGmtTime.setFullYear(parseInt(spYear));
						svGmtTime.setMonth(parseInt(spMonth) - 1);
						svGmtTime.setMonth(parseInt(spMonth) - 1);
						svGmtTime.setDate(parseInt(spDate));
						svGmtTime.setHours(parseInt(spHours));
						svGmtTime.setMinutes(parseInt(spMinutes));
						svGmtTime.setSeconds(parseInt(spSeconds));

						var ctGmtTime = svGmtTime.getTime() + periodClientServerGmtTime;
						var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);
						

						var ctYear = ctLocalTime.getFullYear().toString();
						var ctMonth = (ctLocalTime.getMonth() + 1).toString();
						var ctDate = ctLocalTime.getDate().toString();
						var ctHours = ctLocalTime.getHours().toString();
						var ctMinutes = ctLocalTime.getMinutes().toString();
						var ctSeconds = ctLocalTime.getSeconds().toString();

						if(ctYear.length == 1)
							ctYear = "0" + ctYear;
						if(ctMonth.length == 1)
							ctMonth = "0" + ctMonth;
						if(ctDate.length == 1)
							ctDate = "0" + ctDate;
						if(ctHours.length == 1)
							ctHours = "0" + ctHours;
						if(ctMinutes.length == 1)
							ctMinutes = "0" + ctMinutes;
						if(ctSeconds.length == 1)
							ctSeconds = "0" + ctSeconds;
						
						
						spanStartTime.innerHTML = ctYear + "-" + ctMonth + "-" + ctDate + " " + ctHours + ":" + ctMinutes + ":" + ctSeconds;


						spYear = spEndDate.split("-")[0];
						spMonth = spEndDate.split("-")[1];
						spDate = spEndDate.split("-")[2];

						spHours = spEndTime.split(":")[0];
						spMinutes = spEndTime.split(":")[1];
						spSeconds = spEndTime.split(":")[2];

						if(spMonth.substring(0,1) == "0")
							spMonth = spMonth.substring(1,2);
						if(spDate.substring(0,1) == "0")
							spDate = spDate.substring(1,2);
						if(spHours.substring(0,1) == "0")
							spHours = spHours.substring(1,2);
						if(spMinutes.substring(0,1) == "0")
							spMinutes = spMinutes.substring(1,2);
						if(spSeconds.substring(0,1) == "0")
							spSeconds - spSeconds.substring(1,2);

						svGmtTime = new Date(0);
						svGmtTime.setFullYear(parseInt(spYear));
						svGmtTime.setMonth(parseInt(spMonth) - 1);
						svGmtTime.setMonth(parseInt(spMonth) - 1);
						svGmtTime.setDate(parseInt(spDate));
						svGmtTime.setHours(parseInt(spHours));
						svGmtTime.setMinutes(parseInt(spMinutes));
						svGmtTime.setSeconds(parseInt(spSeconds));

						ctGmtTime = svGmtTime.getTime() + periodClientServerGmtTime;
						ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

						ctYear = ctLocalTime.getFullYear().toString();
						ctMonth = (ctLocalTime.getMonth() + 1).toString();
						ctDate = ctLocalTime.getDate().toString();
						ctHours = ctLocalTime.getHours().toString();
						ctMinutes = ctLocalTime.getMinutes().toString();
						ctSeconds = ctLocalTime.getSeconds().toString();

						if(ctYear.length == 1)
							ctYear = "0" + ctYear;
						if(ctMonth.length == 1)
							ctMonth = "0" + ctMonth;
						if(ctDate.length == 1)
							ctDate = "0" + ctDate;
						if(ctHours.length == 1)
							ctHours = "0" + ctHours;
						if(ctMinutes.length == 1)
							ctMinutes = "0" + ctMinutes;
						if(ctSeconds.length == 1)
							ctSeconds = "0" + ctSeconds;
						
						
						spanEndTime.innerHTML = ctYear + "-" + ctMonth + "-" + ctDate + " " + ctHours + ":" + ctMinutes + ":" + ctSeconds;
						
						<?php
						//if($userProgInfo["end_time"][$i] < $date)
						//{ 
						?>
						/*
							var liveButton = document.getElementsByName("liveButton");
							var updateButton = document.getElementsByName("updateButton");
							
							liveButton[parseInt("<?php echo $count?>")].value = "Passed";
							liveButton[parseInt("<?php echo $count?>")].disabled = true;
							liveButton[parseInt("<?php echo $count?>")].style.opacity = "0.6";
							updateButton[parseInt("<?php echo $count?>")].disabled = true;
							updateButton[parseInt("<?php echo $count?>")].style.opacity = "0.6";
						*/	
						<?php
						
						//	}
						//elseif($date < $userProgInfo["start_time"][$i])
						//{
						?>
							/*var liveButton = document.getElementsByName("liveButton");
							var updateButton = document.getElementsByName("updateButton");
							
							liveButton[parseInt("<?php echo $count?>")].value = "Wait";
							liveButton[parseInt("<?php echo $count?>")].disabled = true;
							liveButton[parseInt("<?php echo $count?>")].style.opacity = "0.6";
							updateButton[parseInt("<?php echo $count?>")].disabled = false;
							updateButton[parseInt("<?php echo $count?>")].style.opacity = "1.0";
						*/
						<?php
						 
							//}?>
						

						
						</script>
						<?php
						$count++;
						} 
					}?>
					</div>
				</td>
			</tr>
			
		</table>
	</div>
</form>
<script>


setInterval("fn_checkCurrentTime();", 1000);
var oldDate = new Date();
var newDate = new Date();
var diff = 0;
function fn_checkCurrentTime()
{
	newDate.setTime(new Date().getTime());
	diff = diff + newDate.getTime() - oldDate.getTime();
	oldDate.setTime(newDate.getTime());
	
	for(i = 0; i < userProgInfo.length; i++)
	{
		var start_date = userProgInfo[i].start_time.split(' ')[0];
		var end_date = userProgInfo[i].end_time.split(' ')[0];
		var start_time = userProgInfo[i].start_time.split(' ')[1];
		var end_time = userProgInfo[i].end_time.split(' ')[1];
		
		var sYear = start_date.split("-")[0];
		var sMonth = start_date.split("-")[1];
		var sDate = start_date.split("-")[2];
	
		var sHours = start_time.split(":")[0];
		var sMinutes = start_time.split(":")[1];
		var sSeconds = start_time.split(":")[2];
	
		if(sHours.length == 2 && sHours.substring(0,1) == "0")
			sHours = sHours.substring(1,2);
		if(sMinutes.length == 2 && sMinutes.substring(0,1) == "0")
			sMinutes = sMinutes.substring(1,2);
		if(sSeconds.length == 2 && sSeconds.substring(0,1) == "0")
			sSeconds = sSeconds.substring(1,2);
	
	
		var eYear = end_date.split("-")[0];
		var eMonth = end_date.split("-")[1];
		var eDate = end_date.split("-")[2];
	
		var eHours = end_time.split(":")[0];
		var eMinutes = end_time.split(":")[1];
		var eSeconds = end_time.split(":")[2];
	
		if(eHours.length == 2 && eHours.substring(0,1) == "0")
			eHours = eHours.substring(1,2);
		if(eMinutes.length == 2 && eMinutes.substring(0,1) == "0")
			eMinutes = eMinutes.substring(1,2);
		if(eSeconds.length == 2 && eSeconds.substring(0,1) == "0")
			eSeconds = eSeconds.substring(1,2);
	
		var dStart = new Date(0);
		var dEnd = new Date(0);
	
		dStart.setYear(sYear);
		dStart.setMonth(sMonth - 1);
		dStart.setMonth(sMonth - 1);
		dStart.setDate(sDate);
	
		dStart.setHours(sHours);
		dStart.setMinutes(sMinutes);
		dStart.setSeconds(sSeconds);
	
		dEnd.setYear(eYear);
		dEnd.setMonth(eMonth - 1);
		dEnd.setMonth(eMonth - 1);
		dEnd.setDate(eDate);
	
		dEnd.setHours(eHours);
		dEnd.setMinutes(eMinutes);
		dEnd.setSeconds(eSeconds);

		var ctLocalTime = new Date();
		var ctGmtTime = ctLocalTime.getTime() - periodClientLocalGmt;
		var svGmtTime = ctGmtTime - periodClientServerGmtTime;
	
		
		/*var svTime = "<?php echo date('Y/m/d H:i:s')?>";
		var serverDate = svTime.split(" ")[0];
		var serverTime = svTime.split(" ")[1];

		var year = serverDate.split("/")[0];
		var month = serverDate.split("/")[1];
		var intDate = serverDate.split("/")[2];

		var hours = serverTime.split(":")[0];
		var minutes = serverTime.split(":")[1];
		var seconds = serverTime.split(":")[2]

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

		year = parseInt(year);
		month = parseInt(month) - 1;
		
		intDate = parseInt(intDate);
		hours = parseInt(hours);
		minutes = parseInt(minutes);
		seconds = parseInt(seconds);

		var servTime = new Date(0);
		servTime.setFullYear(year);
		servTime.setMonth(month);
		servTime.setDate(intDate);
		servTime.setHours(hours);
		servTime.setMinutes(minutes);
		servTime.setSeconds(seconds);

		servTime.setTime(servTime.getTime() + diff);

		year = servTime.getFullYear();
		month = servTime.getMonth() + 1;
		month = servTime.getMonth() + 1;
		intDate = servTime.getDate();
		hours = servTime.getHours();
		minutes = servTime.getMinutes();
		seconds = servTime.getSeconds();

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

		svTime = year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
		*/
		
		//if(userProgInfo[i].end_time < svTime)
		if(dEnd.getTime() < svGmtTime)
		{
			
			var liveButton = document.getElementsByName("liveButton");
			var updateButton = document.getElementsByName("updateButton");
			
			liveButton[i].value = "Passed";
			liveButton[i].disabled = true;
			liveButton[i].style.opacity = "0.6";
			updateButton[i].disabled = true;
			updateButton[i].style.opacity = "0.6";
		}
		//else if(svTime < userProgInfo[i].start_time)
		else if(svGmtTime < dStart.getTime())
		{
			var liveButton = document.getElementsByName("liveButton");
			var updateButton = document.getElementsByName("updateButton");
			
			liveButton[i].value = "Wait";
			liveButton[i].disabled = true;
			liveButton[i].style.opacity = "0.6";
			updateButton[i].disabled = false;
			updateButton[i].style.opacity = "1.0";
		}
		else
		{
			var liveButton = document.getElementsByName("liveButton");
			var updateButton = document.getElementsByName("updateButton");
			
			liveButton[i].value = "Live";
			liveButton[i].disabled = false;
			liveButton[i].style.opacity = "1.0";
			updateButton[i].disabled = true;
			updateButton[i].style.opacity = "0.6";
		}
		
	}
}

</script>
<?php
get_footer(); 
?>