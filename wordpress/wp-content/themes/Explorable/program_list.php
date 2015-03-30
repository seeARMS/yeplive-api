<?php
/*

Template name: program_list

*/
get_header(); 

$progidArr = explode(":", $_POST['programIdArr']);
$sqlStrPart = "";

for($i = 0; $i < count($progidArr); $i++)
{
	$pid_cid_vodenable = explode(",", $progidArr[$i]);
	$sqlStrPart .= "lp.program_id = " . $pid_cid_vodenable[0] . " or "; // pid 
}
if($sqlStrPart != "");
	$sqlStrPart = substr($sqlStrPart, 0, strlen($sqlStrPart) - 4);
	

$progInfo = array("program_id" => array(),"channel_id" => array(), "user_id" => array(), "title" => array(), "image_path" => array(), "start_time" => array(),
		"end_time" => array(), "vod_enable" => array() , "vod_path" => array() , "vote" => array() , "latitude" => array() , "longitude" => array(),
		"connect_count" => array(), "facebook_id" => array() , "facebook_name" => array() , "twitter_id" => array() ,"twitter_img" => array() , "twitter_name" => array(),
		"user_login" => array() , "picture_path" => array(), "isMobile" => array(),"description" => array());

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
		wu.user_login,
		lp.description
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
		where " . $sqlStrPart . "";

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
		$progInfo["description"][$i] = $row->description;
		
		if($row->picture_path != "")
			$progInfo["picture_path"][$i] = home_url() . $row->picture_path;
		else
			$progInfo["picture_path"][$i] = "";
	
		$i++;
	}
	
	
	$serverCurrGmtTime = getdate(); // server gmt time
	
?>
<script>
var progInfo = new Array();
<?php
			for($i = 0; $i < count($progInfo['program_id']); $i++)
			{
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
						isMobile : "<?php echo $progInfo['isMobile'][$i]?>",
						description : "<?php echo $progInfo['description'][$i]?>"
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
<script>
$(function(){
	$(".subcontent").hover(function(){
		$(this).css("background-color","#fafafa");
		$(this).find(".img").css("background-color","#fafafa");
		$(this).find(".des").css("background-color","#fafafa");
		},function(){
			$(this).css("background-color","#ffffff");
			$(this).find(".img").css("background-color","#ffffff");
			$(this).find(".des").css("background-color","#ffffff");
			});
	
});
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
</script>
<style>
		div.content
		{
			margin:0px auto;
			width:800px;
			height:75%;
			overflow:auto; 
			-webkit-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
			-moz-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
			box-shadow: 0 10px 25px rgba(0,0,0,0.5); 
			border-radius:10px; 
			background-color: white;
			
		}
		div.subcontent
		{
			text-align:center;
			/*border:1px solid #000000;*/
			border-bottom:1px solid #eeeeee; 
			background-color: white;
			border-radius:5px;
		}
		
		div.img
		{
			width: 152px;
			height: 152px;
			display: inline-block;
			border-bottom: 0px;
			
			background-size: 80% 80%;
			background-repeat: no-repeat;
			background-position: center center;
			
		}
		
		img.img
		{
			width:100px; 
			height:100px;
			border-radius:5px; 
			border:1px solid #000000;
			position: relative;top: 21px;
		}
		div.des
		{
			width:550px;
			/*height: 132px;*/ 
			display:inline-block; 
			vertical-align:top; 
			text-align:left;
			border-bottom:0px;
			margin-left:-4px;
			border-left:0px;
			padding: 10px 10px;
			line-height:30px;word-wrap: break-word;
			background-color: white;
		}
		
	</style>
	<script>
		function onSubContent(obj)
		{
			var subContent = document.getElementsByName("subcontent");
			var index = -1;
			var pid , cid , vod_enable , endTime;
			for(i = 0; i < subContent.length; i++)
			{
				if(subContent[i] == obj)
				{
					pid = progInfo[i].program_id;
					cid = progInfo[i].channel_id;
					vod_enable = progInfo[i].vod_enable;
					endTime = progInfo[i].end_time;
					break;
				}
			}

			if(realChannel_ids == "")
				return;
			
			
				
			if(endTime == "")
			{
				
				document.getElementById("liveVod").value = "live";
			}
			else 
			{
				
				document.getElementById("liveVod").value = "vod";
			}		
			
				
			
			
			
			var menuNames = document.getElementsByName("menuNames");
			
			for(i = 1; i < menuNames.length; i++)
			{
				if(realChannel_ids[i-1] == cid)
				{
					var hiddenValue = document.getElementsByName(menuNames[i].id);
					channelUrlStr = hiddenValue[0].value;		
				}
			}
			 
			document.getElementById("hidChannelId").value = cid;
			if(pid)
				document.getElementById("programId").value = pid;
			
			document.aform.action = channelUrlStr;
			document.aform.submit();
		}
		
	</script>
	<br>
	<div class="content">
		<?php for($i = 0; $i < count($progInfo["user_id"]); $i++)
		{?>
		<div class="subcontent" name="subcontent" onclick="onSubContent(this);">
			<div class="img" style="background-image:url(<?php
		  if($progInfo["image_path"][$i] != "") 
		  	echo $progInfo["image_path"][$i];
		  else if($progInfo["picture_path"][$i] != "")
		  	echo $progInfo["picture_path"][$i];
		  else if($progInfo["facebook_id"][$i] != "")
		  	echo "https://graph.facebook.com/" . $progInfo["facebook_id"][$i] . "/picture/?type=large";
		  else if($progInfo["twitter_img"][$i] != "")
		  	echo $progInfo["twitter_img"][$i];
		  ?>);">
				
			</div>
			<div class="des" name="des">
			</div>
			<script>
				
				var desObj = document.getElementsByName("des");
					

					var tempStartTime = new Date(0);
					var tempEndTime = new Date(0);

					if(progInfo[<?php echo $i?>].start_time.trim() != "")
						tempStartTime.setTime(fn_dateConvert(progInfo[<?php echo $i?>].start_time));
					if(progInfo[<?php echo $i?>].end_time.trim() != "")
						tempEndTime.setTime(fn_dateConvert(progInfo[<?php echo $i?>].end_time));

					var tempStartStr = "",tempEndStr = "";
					if(progInfo[<?php echo $i?>].start_time.trim() != "")
					{

						var year = tempStartTime.getFullYear();
						var month = tempStartTime.getMonth() + 1;
						var intDate = tempStartTime.getDate();
						var hours = tempStartTime.getHours();
						var minutes = tempStartTime.getMinutes();
						var seconds = tempStartTime.getSeconds();

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

						tempStartStr = year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
								
					}
					
					if(progInfo[<?php echo $i?>].end_time.trim() != "")
					{
						var year = tempEndTime.getFullYear();
						var month = tempEndTime.getMonth() + 1;
						var intDate = tempEndTime.getDate();
						var hours = tempEndTime.getHours();
						var minutes = tempEndTime.getMinutes();
						var seconds = tempEndTime.getSeconds();

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

						tempEndStr = year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
					}					
	

					desObj[<?php echo $i?>].innerHTML = "<b>Title:</b>" + progInfo[<?php echo $i?>].title + "<br>";
					desObj[<?php echo $i?>].innerHTML += "<b>Start Time:</b>" + tempStartStr + "<br>";
					desObj[<?php echo $i?>].innerHTML += "<b>End Time:</b>" + tempEndStr + "<br>";
					desObj[<?php echo $i?>].innerHTML += "<b>Description:</b>" + progInfo[<?php echo $i?>].description;
					
			</script>
		</div>
		<?php 
		}?>
	</div>
	<form name="aform">
		<input type="hidden" id="hidMenus" name="hidMenus" value="">
		<input type="hidden" id="hidFlags" name="hidFlags" value="">
		<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
		<input type="hidden" id="channelIds" name="channelIds" value="">
		<input type="hidden" id="liveVod" name="liveVod" value="" >
		<input type="hidden" id="programId" name="programId" value="" >
		<input type="hidden" id="programIdArr" name="programIdArr" value="">
	</form>
<?php
get_footer(); 
?>