<?php
/*

Template name: admin_page

*/
get_header();

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
	
	$date = date("Y-m-d H:i:s");
	
	$sql_query = "select lp.program_id,
			lp.channel_id,
			lch.channel_name,
			lp.title,
			lp.image_path,
			lp.start_time,
			lp.end_time,
			lp.user_id,
			lct.category_name,
			max(lp.vote) vote
			 
	from 
			wp_program lp,wp_channel lch , wp_category lct
	where
			lp.channel_id = lch.channel_id 
	and 
			lch.category_id = lct.category_id 
	and 
			'" . $date . "' 
	between 
			lp.start_time and lp.end_time 
	group by        
			lp.channel_id";
	$result = mysql_query($sql_query);
	
	$programInfo = array("program_id" => array(),"channel_id" => array() , "channel_name" => array() ,"title" => array() , 
			"image_path" => array() , "start_time" => array() , "end_time" => array() , 
			"user_id" => array() , "category_name" => array(),"vote" => array());
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		if($i == 8)
			break;
		$programInfo["program_id"][$i] = $row["program_id"];
		$programInfo["channel_id"][$i] = $row["channel_id"];
		$programInfo["channel_name"][$i] = $row["channel_name"];
		$programInfo["title"][$i] = $row["title"];
		$programInfo["image_path"][$i] = home_url() . $row["image_path"];
		$programInfo["start_time"][$i] = $row["start_time"];
		$programInfo["end_time"][$i] = $row["end_time"];
		$programInfo["user_id"][$i] = $row["user_id"];
		$programInfo["category_name"][$i] = $row["category_name"];
		$programInfo["vote"][$i] = $row["vote"];
		$i++;
	}
	//echo "<script>alert('" . count($programInfo["program_id"]) . "');</script>";	
	mysql_free_result($result);
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	
	$voddate = date("Y-m-d H:i:s",time() - 24*60*60);
	$sql_query = "select lp.program_id,
			lp.channel_id,
			lch.channel_name,
			lp.title,
			lp.image_path,
			lp.start_time,
			lp.end_time,
			lp.user_id,
			lct.category_name,
			max(lp.vote) vote
			 
	from 
			wp_program lp,wp_channel lch , wp_category lct
	where
			lp.channel_id = lch.channel_id 
	and 
			lch.category_id = lct.category_id
	and 
			lp.end_time
	between
				'" . $voddate . "' and '" . $date . "' 
	and 
			lp.vod_enable = '1' 
	group by        
			lp.channel_id";
	$result = mysql_query($sql_query);
	
	
	$voteInfo = array("program_id" => array(),"channel_id" => array() , "channel_name" => array(),"title" => array() , 
			"image_path" => array() , "start_time" => array() , "end_time" => array() , 
			"user_id" => array() , "category_name" => array() ,"vote" => array());
	
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		if($i == 8)
			break;
		$voteInfo["program_id"][$i] = $row["program_id"];
		$voteInfo["channel_id"][$i] = $row["channel_id"];
		$voteInfo["channel_name"][$i] = $row["channel_name"];
		$voteInfo["title"][$i] = $row["title"];
		$voteInfo["image_path"][$i] = home_url() . $row["image_path"];
		$voteInfo["start_time"][$i] = $row["start_time"];
		$voteInfo["end_time"][$i] = $row["end_time"];
		$voteInfo["user_id"][$i] = $row["user_id"];
		$voteInfo["category_name"][$i] = $row["category_name"];
		$voteInfo["vote"][$i] = $row["vote"];
		$i++;
	}
	//echo "<script>alert('" . count($voteInfo["program_id"]) . "');</script>";
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
<script language="javascript">
function onLoginBtnClick()
{
	
	retJson = window.showModalDialog("<?php echo get_template_directory_uri()?>/login.php", "<?php echo get_template_directory_uri()?>", "dialogHeight: 280px; dialogWidth: 500px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable:0; scroll:no; status: no;");
	if(retJson == "success")
	{
	
		document.getElementById("bcImg").style.display = "inline";
		document.getElementById("lgImg").style.display = "none";
	}
}
var emapInfo = new Array();
emapInfo[0] = {x:20 , y:20};
emapInfo[1] = {x:20 , y:25};
emapInfo[2] = {x:25 , y:25};
emapInfo[3] = {x:25 , y:30};
emapInfo[4] = {x:30 , y:30};
emapInfo[5] = {x:30 , y:35};
emapInfo[6] = {x:25 , y:20};
emapInfo[7] = {x:25 , y:27};
emapInfo[8] = {x:30 , y:21};
emapInfo[9] = {x:35 , y:30};
emapInfo[10] = {x:40 , y:30};
emapInfo[11] = {x:45 , y:35};
emapInfo[12] = {x:50 , y:20};
emapInfo[13] = {x:37 , y:25};
emapInfo[14] = {x:46 , y:25};
emapInfo[15] = {x:56 , y:30};


function onimgclick(channel_id,isLiveOrVod,program_id)
{
	
	var ch = document.getElementById(channel_id);
	var inputTag = ch.getElementsByTagName("input");
	var channelUrlStr = inputTag[0].value;
	document.getElementById("hidChannelId").value = channel_id;
	document.getElementById("liveVod").value = isLiveOrVod;
	if(program_id)
	{
		
		document.getElementById("programId").value = program_id;
	} 
	document.aform.action = channelUrlStr;
	document.aform.submit(); 
}
</script>

<form name="aform" method="post" >
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="liveVod" name="liveVod" value="" >
<input type="hidden" id="programId" name="programId" value="" >
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
<br><br><br>
<div align="center" style="margin-top: 10px; min-height:450px;">
	
	<div style=" width:98%;">
		<table style="height:100%; width:100%;">
		<colgroup>
			<col width="*">
			<col width="400px">
		</colgroup>
			<tr>
				<td>
					<table style="width:100%;height:100%;" >
						<tr>
							<td align="left" style="padding-left: 220px;">
								<h1 >
									Live & Upcoming Events
								</h1>
							</td>
							
						</tr>
						<tr style="height:30px;">
						</tr>
						<tr>
							<td style="vertical-align: middle;padding-left: 200px;">
							<?php
							
							if(count($programInfo["program_id"]) > 4)
								$counter = 4;
							else 
								$counter = count($programInfo["program_id"]);
							
								for($i = 0; $i < $counter; $i++)
								{ 
							 ?>
								<div class="event_poster">
									<div class="poster_wrapper">
										<input type="hidden" name="hidchanneltag" value="<?php echo $programInfo["channel_id"][$i]?>&live">
										<a name="carouselimgs"  href="#" >
											<img  style="width:161px;height:242px; border-top-right-radius: 3px;
														border-top-left-radius: 3px;"  
														src="<?php echo $programInfo["image_path"][$i]?>" />
										</a>
										<div class="description">
											<h2 class="event_title">
												<?php echo $programInfo["title"][$i]?>
											</h2>
											<p class="event_blurb">conference on <a class="account_name"><?php echo $programInfo["channel_name"][$i]?></a> in <?php echo $programInfo["category_name"][$i]?></p>
										</div>
									</div>
									<div class="event_time" name="liveDiv1">
										<?php $vv = explode(" ",$programInfo["start_time"][$i]); echo $vv[1];?> - <?php $vv = explode(" ",$programInfo["end_time"][$i]); echo $vv[1]?> 
									</div>
								</div>
							<?php }
								?>	
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle;padding-left: 200px;">
							<?php
							
							if(count($programInfo["program_id"]) > 4)
							{
								for($i = 4; $i < count($programInfo["program_id"]); $i++)
								{
								?>
									<div class="event_poster">
										<div class="poster_wrapper">
											<input type="hidden" name="hidchanneltag" value="<?php echo $programInfo["channel_id"][$i]?>&live">
											<a name="carouselimgs"  href="javascript:void(0);" onclick="onimgclick('<?php echo $programInfo["channel_id"][$i]?>','live');">
												<img  style="width:161px;height:242px; border-top-right-radius: 3px;
															border-top-left-radius: 3px;"  
															src="<?php echo $programInfo["image_path"][$i]?>" />
											</a>
											<div class="description">
												<h2 class="event_title">
													<?php echo $programInfo["title"][$i]?>
												</h2>
												<p class="event_blurb">conference on <a class="account_name"><?php echo $programInfo["channel_name"][$i]?></a> in <?php echo $programInfo["category_name"][$i]?></p>
											</div>
										</div>
										<div class="event_time" name="liveDiv2">
											<?php $vv = explode(" ",$programInfo["start_time"][$i]); echo $vv[1];?> - <?php $vv = explode(" ",$programInfo["end_time"][$i]); echo $vv[1]?> 
										</div>
									</div>
								<?php 
								}
							}?>	
							</td>
						</tr>
						<tr >
							<td align="left" style="padding-left: 220px;padding-top: 20px;">
								<h1 >Archived Events</h1>
							</td>
						</tr>
						<tr style="height:30px;">
						</tr>
						<tr>
							<td style="vertical-align: middle;padding-left: 200px;">
							<?php
							
							if(count($voteInfo["program_id"]) > 4)
								$counter = 4;
							else 
								$counter = count($voteInfo["program_id"]);
							
								for($i = 0; $i < $counter; $i++)
								{ 
							 ?>
								<div class="event_poster">
									<div class="poster_wrapper">
										<input type="hidden" name="hidchanneltag" value="<?php echo $voteInfo["channel_id"][$i]?>&vod&<?php echo $voteInfo["program_id"][$i]?>">
										<a name="carouselimgs"  href="javascript:void(0);" onclick="onimgclick('<?php echo $voteInfo["channel_id"][$i]?>','vod','<?php echo $voteInfo["program_id"][$i]?>');">
											<img  style="width:161px;height:242px; border-top-right-radius: 3px;
														border-top-left-radius: 3px;"  
														src="<?php echo $voteInfo["image_path"][$i]?>" />
										</a>
										<div class="description">
											<h2 class="event_title">
												<?php echo $voteInfo["title"][$i]?>
											</h2>
											<p class="event_blurb">conference on <a class="account_name"><?php echo $voteInfo["channel_name"][$i]?></a> in <?php echo $voteInfo["category_name"][$i]?></p>
										</div>
									</div>
									<div class="event_time" name="vodDiv1">
										<?php $vv = explode(" ",$voteInfo["start_time"][$i]); echo $vv[1];?> - <?php $vv = explode(" ",$voteInfo["end_time"][$i]); echo $vv[1]?> 
									</div>
								</div>
							<?php }
								?>	
							</td>
						</tr>
						<tr>
							<td style="vertical-align: middle;padding-left: 200px;">
							<?php
							
							if(count($voteInfo["program_id"]) > 4)
							{
								for($i = 4; $i < count($voteInfo["program_id"]); $i++)
								{
								?>
									<div class="event_poster">
										<div class="poster_wrapper">
											<input type="hidden" name="hidchanneltag" value="<?php echo $voteInfo["channel_id"][$i]?>&vod&<?php echo $voteInfo["program_id"][$i]?>">
											<a name="carouselimgs"  href="javascript:void(0);" onclick="onimgclick('<?php echo $voteInfo["channel_id"][$i]?>' , 'vod','<?php echo $voteInfo["program_id"][$i]?>');">
												<img  style="width:161px;height:242px; border-top-right-radius: 3px;
															border-top-left-radius: 3px;"  
															src="<?php echo $voteInfo["image_path"][$i]?>" />
											</a>
											<div class="description">
												<h2 class="event_title">
													<?php echo $voteInfo["title"][$i]?>
												</h2>
												<p class="event_blurb">conference on <a class="account_name"><?php echo $voteInfo["channel_name"][$i]?></a> in <?php echo $voteInfo["category_name"][$i]?></p>
											</div>
										</div>
										<div class="event_time" name="vodDiv2">
											<?php $vv = explode(" ",$voteInfo["start_time"][$i]); echo $vv[1];?> - <?php $vv = explode(" ",$voteInfo["end_time"][$i]); echo $vv[1]?> 
										</div>
									</div>
								<?php 
								}
							}?>	
							</td>
						</tr>
					</table>
				</td>
				
				<td valign="top" >
					<input type="button" id="bcImg" style="display:none; background:url('<?php echo get_template_directory_uri()?>/images/broadcast1.png'); border-radius:10px; color:#ffffff; width:150px;height:50px;" value="Broadcast Live" onclick="location.href='<?php echo home_url( '/' )?>/broadcast/';">
					<input type="button" id="lgImg" style="display:none; background:url('<?php echo get_template_directory_uri()?>/images/login1.png'); border-radius:10px; color:#ffffff; width:150px;height:50px;" value="Log In" onclick="onLoginBtnClick();">
					<br><br>	
							<?php if(isset($_SESSION['user_id'])){?>
								<div style="display:none;
											width: 260px;
											height:220px;
											background: none repeat scroll 0 0 #FFF;
											border: solid 1px rgba(15,15,15,0.15);
											border-radius: 5px;
											box-shadow: 0 1px 3px rgba(0,0,0,0.07);
											padding-right: 15px;
											margin-right:15px;
											margin-bottom: 20px;">
											<div>
												<div style="padding:5px; margin:5px;">
													<a href="javascript:void(0);">
													<img style="width: 50px;height: 50px;
														border-radius: 3px;" src="<?php echo get_template_directory_uri()?>/images/userIcon.png">
													</a>
													
													<div style="float: right; width:70%;">
														<h3>Jakar</h3>
														<p style="color: #999;
															font-weight: normal;
															font-size: 11px;
															line-height: 18px;">view my profile setting</p>
													</div>
												</div>
											</div>
											<div style=" border:1px dashed #e0e0e0;border-right: 0px;height: 40px;">
												<a><h3 style="text-align:left; font-size: 11px; padding-left:5px;
														line-height: 30px;
														font-weight: bold;
														text-transform: uppercase;">Followers: <span>0</span></h3>
														
														
														</a>
												
											</div>
											<div style=" border:1px dashed #e0e0e0;border-right: 0px; border-top:0px; border-bottom:0px; height: 40px;">
												<a><h3 style="text-align:left; font-size: 11px; padding-left:5px;
														line-height: 30px;
														font-weight: bold;
														text-transform: uppercase;">Followers: <span>0</span></h3>
														
														
														</a>
												
											</div>
								</div>
								<?php }?>
								<div style="
											width: 260px;
											height:150px;
											background: none repeat scroll 0 0 #FFF;
											border: solid 1px rgba(15,15,15,0.15);
											border-radius: 5px;
											box-shadow: 0 1px 3px rgba(0,0,0,0.07);
											padding-right: 15px;
											margin-right:15px;
											margin-bottom: 20px;">
											<div>
												<div style="padding:5px; margin:5px;">
													
													
													<div style="float: right; width:70%;">
														<h3 style="color: red">Notice</h3>
													</div>
													<div style="clear:both; border:1px dashed #f0f0f0; word-spacing: 5px; min-height: 25px;line-height: 25px;">
														Because this site occured problem,
														from tomorrow,we does not run this one. 
													</div>
												</div>
											</div>
											
								</div>
					
				</td>
			</tr> 
		</table>
	</div>
	
</div>

</form>
<script language="javascript">
	
	
	var liveDiv1 = document.getElementsByName("liveDiv1");
	var liveDiv2 = document.getElementsByName("liveDiv2");
	var vodDiv1 = document.getElementsByName("vodDiv1");
	var vodDiv2 = document.getElementsByName("vodDiv2");
	
	for(i = 0; i < liveDiv1.length; i++)
	{
		var div = liveDiv1[i];
		
		var startEndDate = div.innerHTML;
		var startDate = startEndDate.split(" - ")[0];
		var endDate = startEndDate.split(" - ")[1];

		var tempDate = new Date(0);

		
		var hours = startDate.split(":")[0];
		var minutes = startDate.split(":")[1];
		var seconds = startDate.split(":")[2];
		
		if(hours.substring(hours.length - 2,hours.length - 1) == "0")
			hours = hours.substring(hours.length - 1,hours.length);
		
		if(minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

				
		tempDate.setHours(parseInt(hours));
		
		tempDate.setMinutes(parseInt(minutes));
		
		tempDate.setSeconds(parseInt(seconds));
		

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

		
		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var startDivDate = hours + ":" + minutes + ":" + seconds;

		

		tempDate.setTime(0);

		timeStr = endDate;

		

		var hours = timeStr.split(":")[0];
		var minutes = timeStr.split(":")[1];
		var seconds = timeStr.split(":")[2];

		
		if(hours.length > 1 && hours.substring(0,1) == "0")
			hours = hours.substring(1,2);
		if(minutes.length > 1 && minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.length > 1 && seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

		tempDate.setHours(parseInt(hours));
		tempDate.setMinutes(parseInt(minutes));
		tempDate.setSeconds(parseInt(seconds));

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);


		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var endDivDate = hours + ":" + minutes + ":" + seconds;

		div.innerHTML = startDivDate + " - " + endDivDate;
	}

	for(i = 0; i < liveDiv2.length; i++)
	{
		var div = liveDiv2[i];
		
		var startEndDate = div.innerHTML;
		var startDate = startEndDate.split(" - ")[0];
		var endDate = startEndDate.split(" - ")[1];

		var tempDate = new Date(0);

		
		var hours = startDate.split(":")[0];
		var minutes = startDate.split(":")[1];
		var seconds = startDate.split(":")[2];
		
		if(hours.substring(hours.length - 2,hours.length - 1) == "0")
			hours = hours.substring(hours.length - 1,hours.length);
		
		if(minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

				
		tempDate.setHours(parseInt(hours));
		
		tempDate.setMinutes(parseInt(minutes));
		
		tempDate.setSeconds(parseInt(seconds));
		

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

		
		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var startDivDate = hours + ":" + minutes + ":" + seconds;

		

		tempDate.setTime(0);

		timeStr = endDate;

		

		var hours = timeStr.split(":")[0];
		var minutes = timeStr.split(":")[1];
		var seconds = timeStr.split(":")[2];

		
		if(hours.length > 1 && hours.substring(0,1) == "0")
			hours = hours.substring(1,2);
		if(minutes.length > 1 && minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.length > 1 && seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

		tempDate.setHours(parseInt(hours));
		tempDate.setMinutes(parseInt(minutes));
		tempDate.setSeconds(parseInt(seconds));

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);


		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var endDivDate = hours + ":" + minutes + ":" + seconds;

		div.innerHTML = startDivDate + " - " + endDivDate;
	}
	
	for(i = 0; i < vodDiv1.length; i++)
	{
		var div = vodDiv1[i];
		
		var startEndDate = div.innerHTML;
		var startDate = startEndDate.split(" - ")[0];
		var endDate = startEndDate.split(" - ")[1];

		var tempDate = new Date(0);

		
		var hours = startDate.split(":")[0];
		var minutes = startDate.split(":")[1];
		var seconds = startDate.split(":")[2];
		
		if(hours.substring(hours.length - 2,hours.length - 1) == "0")
			hours = hours.substring(hours.length - 1,hours.length);
		
		if(minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

				
		tempDate.setHours(parseInt(hours));
		
		tempDate.setMinutes(parseInt(minutes));
		
		tempDate.setSeconds(parseInt(seconds));
		

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

		
		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var startDivDate = hours + ":" + minutes + ":" + seconds;

		

		tempDate.setTime(0);

		timeStr = endDate;

		

		var hours = timeStr.split(":")[0];
		var minutes = timeStr.split(":")[1];
		var seconds = timeStr.split(":")[2];

		
		if(hours.length > 1 && hours.substring(0,1) == "0")
			hours = hours.substring(1,2);
		if(minutes.length > 1 && minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.length > 1 && seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

		tempDate.setHours(parseInt(hours));
		tempDate.setMinutes(parseInt(minutes));
		tempDate.setSeconds(parseInt(seconds));

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);


		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var endDivDate = hours + ":" + minutes + ":" + seconds;

		div.innerHTML = startDivDate + " - " + endDivDate;
	}

	for(i = 0; i < vodDiv2.length; i++)
	{
		var div = vodDiv2[i];
		
		var startEndDate = div.innerHTML;
		var startDate = startEndDate.split(" - ")[0];
		var endDate = startEndDate.split(" - ")[1];

		var tempDate = new Date(0);

		
		var hours = startDate.split(":")[0];
		var minutes = startDate.split(":")[1];
		var seconds = startDate.split(":")[2];
		
		if(hours.substring(hours.length - 2,hours.length - 1) == "0")
			hours = hours.substring(hours.length - 1,hours.length);
		
		if(minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

				
		tempDate.setHours(parseInt(hours));
		
		tempDate.setMinutes(parseInt(minutes));
		
		tempDate.setSeconds(parseInt(seconds));
		

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

		
		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var startDivDate = hours + ":" + minutes + ":" + seconds;

		

		tempDate.setTime(0);

		timeStr = endDate;

		

		var hours = timeStr.split(":")[0];
		var minutes = timeStr.split(":")[1];
		var seconds = timeStr.split(":")[2];

		
		if(hours.length > 1 && hours.substring(0,1) == "0")
			hours = hours.substring(1,2);
		if(minutes.length > 1 && minutes.substring(0,1) == "0")
			minutes = minutes.substring(1,2);
		if(seconds.length > 1 && seconds.substring(0,1) == "0")
			seconds = seconds.substring(1,2);

		tempDate.setHours(parseInt(hours));
		tempDate.setMinutes(parseInt(minutes));
		tempDate.setSeconds(parseInt(seconds));

		var ctGmtTime = tempDate.getTime() + periodClientServerGmtTime;
		var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);


		hours = ctLocalTime.getHours();
		minutes = ctLocalTime.getMinutes();
		seconds = ctLocalTime.getSeconds();

		
		if(hours.toString().length == 1)
			hours = "0" + hours;
		if(minutes.toString().length == 1)
			minutes = "0" + minutes;
		if(seconds.toString().length == 1)
			seconds = "0" + seconds;

		var endDivDate = hours + ":" + minutes + ":" + seconds;

		div.innerHTML = startDivDate + " - " + endDivDate;
	}
</script>
<?php get_footer(); ?>