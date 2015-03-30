<?php
/*

Template name: updateprogram

*/
	get_header();
	
	require_once(ABSPATH . WPINC . '/registration.php');
	global $wpdb, $user_ID;

	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
	if(!$link)
	{
		echo "mysql_connect fail!";
		exit;
	}
	
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db fail!";
		exit;
	}

	$sql_query = "select 
							category_id,
							category_name 
				  from 
							wp_category 
			      where 
							category_name != 'E-Map' 
				  and
							category_name != 'Home'";
	$result = mysql_query($sql_query);
	
	if(!$result)
	{
		echo "mysql_query fail";
		exit;
	}
	$categoryInfo = array("category_id" => array(),"category_name"=> array());
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		$categoryInfo['category_id'][$i] = $row['category_id'];
		$categoryInfo['category_name'][$i] = $row['category_name'];
		$i++;
	}
	mysql_free_result($result);
	
	
	if(count($categoryInfo['category_id']) > 0)
	{
		$sql_query = "select 
								ch.channel_id,
								ch.channel_name,
								cate.category_id 
					  from 
								wp_category cate 
					  inner join 
								wp_channel ch 
					  on 
								cate.category_id = ch.category_id";
		
		if(!mysql_select_db(DB_NAME,$link))
		{
			echo "mysql_select_db fail";
			exit;
		}
		$result = mysql_query($sql_query);
		if(!result)
		{
			echo "mysql_query fail";
			exit;
		}
		$i = 0;
		$channelInfo = array("channel_id" => array(),"channel_name" => array());
		while($row = mysql_fetch_array($result))
		{
			$channelInfo["channel_id"][$i] = $row['channel_id'];
			$channelInfo["channel_name"][$i] = $row['channel_name'];
			$channelInfo["category_id"][$i] = $row['category_id'];
			$i++;
		}
		
		mysql_free_result($result);
		
		
	}
	
	$selChSetTime = array("channel_id" => array() , "start_time" => array() , "end_time" => array());
	
	mysql_select_db(DB_NAME,$link);
	
	$sql_query = "select channel_id, start_time , max(end_time) end_time from wp_program group by channel_id";
	
	$result = mysql_query($sql_query);
	if($result)
	{
		$i = 0;
		while($row = mysql_fetch_array($result))
		{
			$selChSetTime['channel_id'][$i] = $row['channel_id'];
			$selChSetTime['start_time'][$i] = $row['start_time'];
			$selChSetTime['end_time'][$i] = $row['end_time'];
			$i++;
		}
		mysql_free_result($result);
	
	}
	
	$key="9dcde915a1a065fbaf14165f00fcc0461b8d0a6b43889614e8acdb8343e2cf15";
	$ip=$_SERVER['REMOTE_ADDR'];
	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=json";
	$cont = file_get_contents($url);
	$data = json_decode($cont , true);
	
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
var progInfo = new Array();
var categoryInfo = new Array();
var channelInfo = new Array();
var start_timeArr = new Array();
var end_timeArr = new Array();
<?php
	 for($i = 0; $i < count($categoryInfo['category_id']); $i++)
	 {?>
		categoryInfo[ categoryInfo.length ] = { 
								category_id: "<?php echo $categoryInfo['category_id'][$i] ?>",
								category_name: "<?php echo $categoryInfo['category_name'][$i] ?>"
										};
<?php }?>
<?php
		 for($i = 0; $i < count($channelInfo['category_id']); $i++)
		 {?>
			channelInfo[ channelInfo.length ] = { 
									category_id: "<?php echo $channelInfo['category_id'][$i] ?>",
									channel_id: "<?php echo $channelInfo['channel_id'][$i] ?>",
									channel_name: "<?php echo $channelInfo['channel_name'][$i] ?>"
											};
	<?php }?>

function js_unmasking(obj) {
	obj.value = obj.value.replace("/", "");
	obj.value = obj.value.replace("/", "");
}
function js_masking(obj) {
	var tStr = "";
	for(var idx=0; idx< obj.value.length; idx++) { 
		if (idx == 4 || idx == 6) tStr = tStr + "/";
		tStr = tStr + obj.value.charAt(idx);
	 }
	obj.value = tStr;
}
function js_checkNumeric(evt) {
	var result = true;
	
	var keyCode = (evt.which) ? evt.which : event.keyCode;
	var charCode = String.fromCharCode(keyCode);
	var re = new RegExp("[0-9]");
	if(charCode!="\r" && charCode !="\b" && !re.test(charCode)) result = false;
	delete re;
	return result;
	
}
function js_timemasking(obj) {
	var tStr = "";
	for(var idx=0; idx< obj.value.length; idx++) { 
		if (idx == 2 || idx == 4) tStr = tStr + ":";
		tStr = tStr + obj.value.charAt(idx);
	 }
	obj.value = tStr;
}
function js_timeunmasking(obj) {
	obj.value = obj.value.replace(":", "");
	obj.value = obj.value.replace(":", "");
}
function js_checkDate(obj) {
	var sDate;
	if (obj.type == "text") sDate = obj.value;
	else sDate= obj;

	if (!sDate) return false;
	sDate=sDate.replace("/", "");
	sDate=sDate.replace("/", "");
	
	//Validation Logic for Date..
	var iYear = null;
    var iMonth = null;
    var iDay = null;
    var iDaysInMonth = null;
	var flag = true;
	var aDaysInMonth=new Array(31,28,31,30,31,30,31,31,30,31,30,31);

	if ( sDate.length != 8 ) flag = false ;

	if (flag) {
		iYear = eval(sDate.substr(0,4));
		iMonth = eval(sDate.substr(4,2));
		iDay = eval(sDate.substr(6,2));
		if (iYear > 2100) flag = false;
	}
	
	if (flag) {
		var iDaysInMonth = (iMonth != 2) ? aDaysInMonth[iMonth-1] : (( iYear%4 == 0 && iYear%100 != 0 || iYear % 400==0 ) ? 29 : 28 );
		if( iDay==null || iMonth==null || iYear==null || iMonth > 12 || iMonth < 1 || iDay < 1 || iDay > iDaysInMonth ) {
			flag = false;
		}
	}
	
	delete aDaysInMonth;
	return flag;
}

function js_checkTime(obj) {
	var sTime;
	if (obj.type == "text") sTime = obj.value;
	else sTime= obj;

	if (!sTime) return false;
	sTime=sTime.replace(":", "");
	sTime=sTime.replace(":", "");
	
	//Validation Logic for Time..
	var iHour = null;
    var iMin = null;
    var iSec = null;
    var flag = true;

	if ( sTime.length != 6 ) flag = false ;
	
	if (flag) {
		iHour = eval(sTime.substr(0,2));
		iMin = eval(sTime.substr(2,2));
		iSec = eval(sTime.substr(4,2));
	}
	
	if (flag) {
		if( iHour==null || iMin==null || iSec==null ||
				iHour > 24 || iMin > 60 || iSec > 60 ) {
			flag = false;
		}
	}
	
	return flag;
}
function onBtnUpdateProgram()
{
	
	delete start_timeArr;
	delete end_timeArr;
	start_timeArr = new Array();
	end_timeArr = new Array();
	var title = document.getElementById("title");
	var categorySel = document.getElementById("category");
	var channelSel = document.getElementById("channels");
	var start_date = document.getElementById("start_date");
	var start_time = document.getElementById("start_time");
	var end_date = document.getElementById("end_date");
	var end_time = document.getElementById("end_time");
	var charImg = document.getElementById("charImg");
	var imgurl = document.getElementById("imgUrl").value;
	var description = document.getElementById("description").value;
	//var vod_enable = document.getElementById("vodEnable").checked == true ? 1 : 0;
	vod_enable = 1;
	
	var sdate = start_date.value.split("/")[0] + "-" + start_date.value.split("/")[1] + "-" + start_date.value.split("/")[2] + " " + start_time.value;
	var edate = end_date.value.split("/")[0] + "-" + end_date.value.split("/")[1] + "-" + end_date.value.split("/")[2] + " " + end_time.value; 
	var vod_path = "";
	
	if(progInfo.title == title.value &&
			progInfo.image_path == charImg.src &&
			categorySel.value == progInfo.category_id &&
			channelSel.value == progInfo.channel_id &&
			sdate == progInfo.start_time &&
			edate == progInfo.end_time &&
			progInfo.description == description)
	{
		alert("No data was changed!");
		title.focus();
		return;
	}	
	
	
	document.getElementById("addProgramBtn").disabled = true;
	if(document.getElementById("channels").value == "")
	{
		alert("select channel!");
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(title.value == "")
	{
		alert("Input title!");
		title.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(start_date.value == "")
	{
		alert("Input start date!");
		start_date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(start_time.value == "")
	{
		alert("Input start time!");
		start_time.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(end_date.value == "")
	{
		alert("Input end date!");
		end_date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(end_time.value == "")
	{
		alert("Input end time!");
		end_time.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(!js_checkDate(start_date))
	{
		alert("Correctly input start date!");
		start_date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(!js_checkTime(start_time))
	{
		alert("Correctly input start time!");
		start_time.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(!js_checkDate(end_date))
	{
		alert("Correctly input end date!");
		end_date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(!js_checkTime(end_time))
	{
		alert("Correctly input end time!");
		end_time.focus();
		document.getElementById("addProgramBtn").disabled = false;
		
		return;
	}
	if(document.getElementById("description").value == "")
	{
		alert("Input description");
		document.getElementById("description").focus();
		document.getElementById("addProgramBtn").disabled = false;
		return;
	}
	var sYear = start_date.value.split("/")[0];
	var sMonth = start_date.value.split("/")[1];
	var sDate = start_date.value.split("/")[2];

	var sHours = start_time.value.split(":")[0];
	var sMinutes = start_time.value.split(":")[1];
	var sSeconds = start_time.value.split(":")[2];

	if(sHours.length == 2 && sHours.substring(0,1) == "0")
		sHours = sHours.substring(1,2);
	if(sMinutes.length == 2 && sMinutes.substring(0,1) == "0")
		sMinutes = sMinutes.substring(1,2);
	if(sSeconds.length == 2 && sSeconds.substring(0,1) == "0")
		sSeconds = sSeconds.substring(1,2);


	var eYear = end_date.value.split("/")[0];
	var eMonth = end_date.value.split("/")[1];
	var eDate = end_date.value.split("/")[2];

	var eHours = end_time.value.split(":")[0];
	var eMinutes = end_time.value.split(":")[1];
	var eSeconds = end_time.value.split(":")[2];

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

	if(dEnd.getTime() - dStart.getTime() <= 0)
	{
		alert("Start time can't be bigger than end time!");
		start_date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		document.getElementById("wait").style.display = "none";
		return;
	}
	
	/*<?php $dtTemp = getdate();?>

	var dtTemp = new Date(0);
	dtTemp.setYear(parseInt("<?php echo $dtTemp["year"]?>"));
	dtTemp.setMonth(parseInt("<?php echo $dtTemp["mon"]?>") - 1);
	dtTemp.setMonth(parseInt("<?php echo $dtTemp["mon"]?>") - 1);
	
	dtTemp.setDate(parseInt("<?php echo $dtTemp["mday"]?>"));
	dtTemp.setHours(parseInt("<?php echo $dtTemp["hours"]?>"));
	dtTemp.setMinutes(parseInt("<?php echo $dtTemp["minutes"]?>"));
	dtTemp.setSeconds(parseInt("<?php echo $dtTemp["seconds"]?>"));*/
	
	//if(dStart.getTime() <= dtTemp.getTime())
	if(dStart.getTime() <= new Date().getTime())
	{
		document.getElementById("addProgramBtn").disabled = false;
		document.getElementById("wait").style.display = "none";
		alert("Start time can't be smaller than current time!");
		start_date.focus();
		return;
	}
	if(dEnd.getTime() - dStart.getTime() >= 5 * 60 * 60 * 1000)
	{
		alert("broadcasting duration can't be more than 5 hours!");
		start.date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		document.getElementById("wait").style.display = "none";
		return;
	}
	/*if(imgurl == "")
	{
		alert("Input image url!");
		document.getElementById("addProgramBtn").disabled = false;
		document.getElementById("wait").style.display = "none";
		return;
	}*/
	var channel_id = document.getElementById("channels").value;
	
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
		    
		    if(xmlhttp.responseText != "")
		    {    
			    sTimeArr = xmlhttp.responseText.split('/')[0];
			    eTimeArr = xmlhttp.responseText.split('/')[1];
	
			    start_timeArr = sTimeArr.split(",");
			    end_timeArr = eTimeArr.split(",");
		    }
		    
		    
		    insertProgramInfo(channel_id);
		}
	  }
	  
	document.getElementById("wait").style.display = "inline"; 
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetProgramInfo.php?channel_id=" + channel_id + "&programIndex=" + progInfo.programIndex,true);
	xmlhttp.send();
	
	
}
function insertProgramInfo(channel_id)
{
	var start_date = document.getElementById("start_date");
	var end_date = document.getElementById("end_date");
	var start_time = document.getElementById("start_time");
	var end_time = document.getElementById("end_time");

	//var rStartTime = start_date.value.split('/')[0] + "-" + start_date.value.split('/')[1] + "-" + start_date.value.split('/')[2] + " " + start_time.value; // 2012-03-18 22:01:12
	//var rEndTime = end_date.value.split('/')[0] + "-" + end_date.value.split('/')[1] +  "-" + end_date.value.split('/')[2] + " " + end_time.value; // 2012-03-18 22:01:12
	
	var year = start_date.value.split("/")[0];
	var month = start_date.value.split("/")[1];
	var dateStr = start_date.value.split("/")[2];
	
	var hours = start_time.value.split(":")[0];
	var minutes = start_time.value.split(":")[1];
	var seconds = start_time.value.split(":")[2];

	if(month.substring(0,1) == "0")
		month = month.substring(1,2);
	if(dateStr.substring(0,1) == "0")
		dateStr.substring(1,2);
	if(hours.substring(0,1) == "0")
		hours = hours.substring(1,2);
	if(minutes.substring(0,1) == "0")
		minutes = minutes.substring(1,2);
	if(seconds.substring(0,1) == "0")
		seconds = seconds.substring(1,2);

	var rctStartDate = new Date(0);
	rctStartDate.setFullYear(parseInt(year));
	rctStartDate.setMonth(parseInt(month) - 1);
	rctStartDate.setMonth(parseInt(month) - 1);
	rctStartDate.setDate(parseInt(dateStr));
	rctStartDate.setHours(parseInt(hours));
	rctStartDate.setMinutes(parseInt(minutes));
	rctStartDate.setSeconds(parseInt(seconds));

	var rctGmtStartDate = rctStartDate.getTime() - periodClientLocalGmt;
	var rsvGmtStartDate = new Date(rctGmtStartDate - periodClientServerGmtTime);

	year = rsvGmtStartDate.getFullYear().toString();
	month = (rsvGmtStartDate.getMonth() + 1).toString();
	dateStr = (rsvGmtStartDate.getDate()).toString();
	hours = (rsvGmtStartDate.getHours()).toString();
	minutes = (rsvGmtStartDate.getMinutes()).toString();
	seconds = (rsvGmtStartDate.getSeconds()).toString();

	if(month.length == 1)
		month = "0" + month;
	if(dateStr.length == 1)
		dateStr = "0" + dateStr;
	if(hours.length == 1)
		hours = "0" + hours;
	if(minutes.length == 1)
		minutes = "0" + minutes;
	if(seconds.length == 1)
		seconds = "0" + seconds;

	rStartTime = year + "-" + month + "-" + dateStr + " " + hours + ":" + minutes + ":" + seconds;




	year = end_date.value.split("/")[0];
	month = end_date.value.split("/")[1];
	dateStr = end_date.value.split("/")[2];
	
	hours = end_time.value.split(":")[0];
	minutes = end_time.value.split(":")[1];
	seconds = end_time.value.split(":")[2];

	if(month.substring(0,1) == "0")
		month = month.substring(1,2);
	if(dateStr.substring(0,1) == "0")
		dateStr.substring(1,2);
	if(hours.substring(0,1) == "0")
		hours = hours.substring(1,2);
	if(minutes.substring(0,1) == "0")
		minutes = minutes.substring(1,2);
	if(seconds.substring(0,1) == "0")
		seconds = seconds.substring(1,2);

	var rctEndDate = new Date(0);
	rctEndDate.setFullYear(parseInt(year));
	rctEndDate.setMonth(parseInt(month) - 1);
	rctEndDate.setMonth(parseInt(month) - 1);
	rctEndDate.setDate(parseInt(dateStr));
	rctEndDate.setHours(parseInt(hours));
	rctEndDate.setMinutes(parseInt(minutes));
	rctEndDate.setSeconds(parseInt(seconds));

	var rctGmtEndDate = rctEndDate.getTime() - periodClientLocalGmt;
	var rsvGmtEndDate = new Date(rctGmtEndDate - periodClientServerGmtTime);

	year = rsvGmtEndDate.getFullYear().toString();
	month = (rsvGmtEndDate.getMonth() + 1).toString();
	dateStr = (rsvGmtEndDate.getDate()).toString();
	hours = (rsvGmtEndDate.getHours()).toString();
	minutes = (rsvGmtEndDate.getMinutes()).toString();
	seconds = (rsvGmtEndDate.getSeconds()).toString();

	if(month.length == 1)
		month = "0" + month;
	if(dateStr.length == 1)
		dateStr = "0" + dateStr;
	if(hours.length == 1)
		hours = "0" + hours;
	if(minutes.length == 1)
		minutes = "0" + minutes;
	if(seconds.length == 1)
		seconds = "0" + seconds;

	rEndTime = year + "-" + month + "-" + dateStr + " " + hours + ":" + minutes + ":" + seconds;
	

	var possibleFlag = true; 
	for(i = 0; i < start_timeArr.length; i++)
	{
		if(start_timeArr[i] <= rStartTime && rStartTime <= end_timeArr[i])
		{
			possibleFlag = false;
			break;
		}
		if(start_timeArr[i] <= rEndTime && rEndTime <= end_timeArr[i])
		{
			possibleFlag = false;
			break;
		} 
	}	
	if(possibleFlag == false)
	{
		alert("Because Other user already registered program in time that you entered, you have to input other time!");
		start_date.focus();
		document.getElementById("addProgramBtn").disabled = false;
		document.getElementById("wait").style.display = "none";
		return;
	}
	
	var title = document.getElementById("title");
	var imgurl = document.getElementById("imgUrl").value;
	//var vod_enable = document.getElementById("vodEnable").checked == true ? 1 : 0;
	var vod_enable = 1;
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
			    alert("Successfully saved!");
			    document.aform.action = "<?php echo home_url()?>/broadprogram/";
			    document.aform.submit();
			}
		    document.getElementById("addProgramBtn").disabled = false;
		    document.getElementById("wait").style.display = "none";
		}
	  }
	  
	imgurl = imgurl.substring("<?php echo home_url()?>".length,imgurl.length);
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxAddProgram.php?channel_id="+channel_id + "&userid=" + "<?php echo $_SESSION['user_id']?>" + "&title=" + title.value + "&imgurl=" + imgurl + "&start_time=" + rStartTime + "&end_time=" + rEndTime + "&vod_enable=" + vod_enable + "&programIndex=" + progInfo.programIndex + "&latitude=" + "<?php echo $data['latitude']?>"  + "&longitude=" + "<?php echo $data['longitude']?>" + "&description=" + document.getElementById("description").value,true);
	xmlhttp.send();
}
function onListChange(obj)
{
	
	var channelListBox = document.getElementById("channels");
	channelListBox.innerHTML = "";
	
	var category_id = obj.value;
	
	for(i = 0; i < channelInfo.length; i++)
	{
		if(channelInfo[i].category_id == category_id)
		{
			var option = document.createElement("option");
		  	option.value = channelInfo[i].channel_id;
		  	option.text = channelInfo[i].channel_name;
			channelListBox.options.add(option);
		}
	}
	if(channelListBox.options.length > 0)
	{
		channelListBox.options[0].selected = true;
		
		onChannelChange(channelListBox);
	}
}
function onChannelChange(obj)
{
	var flag = false;
	<?php 
	if(count($selChSetTime['channel_id']) > 0)
	{
		
		for($i = 0; $i < count($selChSetTime['channel_id']); $i++)
		{
	?>
		
		if(obj.value == "<?php echo $selChSetTime['channel_id'][$i]?>")
		{
			/*var temp = "<?php echo $selChSetTime['start_time'][$i]?>".split(" ")[0].replace("-","/");
			temp = temp.replace("-","/");
			document.getElementById("start_date").value = temp;
			document.getElementById("start_time").value = "<?php echo $selChSetTime['start_time'][$i]?>".split(" ")[1];

			temp = "<?php echo $selChSetTime['end_time'][$i]?>".split(" ")[0].replace("-","/");
			temp = temp.replace("-","/");
			document.getElementById("end_date").value = temp;
			document.getElementById("end_time").value = "<?php echo $selChSetTime['end_time'][$i]?>".split(" ")[1];
			*/

			

			var dtDate = "<?php echo $selChSetTime['end_time'][$i]?>".split(" ")[0];
			var dtTime = "<?php echo $selChSetTime['end_time'][$i]?>".split(" ")[1];

			var year = dtDate.split("-")[0];
			var month = dtDate.split("-")[1];
			var intDate = dtDate.split("-")[2];

			var hours = dtTime.split(":")[0];
			var minutes = dtTime.split(":")[1];
			var seconds = dtTime.split(":")[2];

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

			var pDate = new Date(0);
			pDate.setYear(year);
			pDate.setMonth(month);
			pDate.setMonth(month);
			pDate.setDate(intDate);
			pDate.setHours(hours);
			pDate.setMinutes(minutes);
			pDate.setSeconds(seconds);
			pDate.setTime(pDate.getTime() + 1000);

			

			var ctGmtTime = pDate.getTime() + periodClientServerGmtTime;
			var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

			var year = ctLocalTime.getFullYear();
			var month = ctLocalTime.getMonth() + 1;
				month = ctLocalTime.getMonth() + 1;
			var intDate = ctLocalTime.getDate();
			
			var hours = ctLocalTime.getHours();
			var minutes = ctLocalTime.getMinutes();
			var seconds = ctLocalTime.getSeconds();

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

			document.getElementById("start_date").value = year + "/" + month + "/" + intDate;
			document.getElementById("start_time").value = hours + ":" + minutes + ":" + seconds;

			if(ctLocalTime.getTime() < new Date().getTime())
			{
				
				var svlcStartTime = new Date();
				
				var year = svlcStartTime.getFullYear();
				var month = svlcStartTime.getMonth() + 1;
					month = svlcStartTime.getMonth() + 1;
				var intDate = svlcStartTime.getDate();
				
				var hours = svlcStartTime.getHours();
				var minutes = svlcStartTime.getMinutes();
				var seconds = svlcStartTime.getSeconds();

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

				document.getElementById("start_date").value = year + "/" + month + "/" + intDate;
				document.getElementById("start_time").value = hours + ":" + minutes + ":" + seconds;
			}

			ctLocalTime.setTime(ctLocalTime.getTime() + 60*60*1000);

			year = ctLocalTime.getFullYear();
			month = ctLocalTime.getMonth() + 1;
			intDate = ctLocalTime.getDate();
			hours = ctLocalTime.getHours();
			minutes = ctLocalTime.getMinutes();
			seconds = ctLocalTime.getSeconds();

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

			
			document.getElementById("end_date").value = year + "/" + month + "/" + intDate;
			document.getElementById("end_time").value = hours + ":" + minutes + ":" + seconds;

			if(ctLocalTime.getTime() < new Date().getTime())
			{
				
				/*document.getElementById("start_date").value = "<?php echo date('Y/m/d H:i:s')?>".split(" ")[0];
				document.getElementById("start_time").value = "<?php echo date('Y/m/d H:i:s')?>".split(" ")[1];
				document.getElementById("end_date").value = "<?php echo date('Y/m/d H:i:s',time() + 60*60)?>".split(" ")[0];
				document.getElementById("end_time").value = "<?php echo date('Y/m/d H:i:s',time() + 60*60)?>".split(" ")[1];*/

				var svlcEndTime = new Date();
				svlcEndTime.setTime(svlcEndTime.getTime() + 60*60*1000);

				var  year = svlcEndTime.getFullYear();
				var month = svlcEndTime.getMonth() + 1;
				 month = svlcEndTime.getMonth() + 1;
				var  intDate = svlcEndTime.getDate();
				
				var hours = svlcEndTime.getHours();
				var minutes = svlcEndTime.getMinutes();
				var seconds = svlcEndTime.getSeconds();

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

				
				document.getElementById("end_date").value = year + "/" + month + "/" + intDate;
				document.getElementById("end_time").value = hours + ":" + minutes + ":" + seconds;
			}
			flag = true;
		}
		
	<?php 
		}
	} 
	?>
	if(flag == false)
	{
		/*var temp = "<?php echo date('Y-m-d H:i:s')?>".split(" ")[0].replace("-","/");
		temp = temp.replace("-","/");
		document.getElementById("start_date").value = temp;
		document.getElementById("start_time").value = "<?php echo date('Y-m-d H:i:s')?>".split(" ")[1];

		temp = "<?php echo date('Y-m-d H:i:s')?>".split(" ")[0].replace("-","/");
		temp = temp.replace("-","/");
		
		document.getElementById("end_date").value = temp;
		document.getElementById("end_time").value = "<?php echo date('Y-m-d H:i:s',time() + 60*60)?>".split(" ")[1];
		*/



		var svlcStartTime = new Date();
		var svlcEndTime = new Date();
		svlcEndTime.setTime(svlcEndTime.getTime() + 60*60*1000);

		var year = svlcStartTime.getFullYear();
		var month = svlcStartTime.getMonth() + 1;
			month = svlcStartTime.getMonth() + 1;
		var intDate = svlcStartTime.getDate();
		
		var hours = svlcStartTime.getHours();
		var minutes = svlcStartTime.getMinutes();
		var seconds = svlcStartTime.getSeconds();

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

		document.getElementById("start_date").value = year + "/" + month + "/" + intDate;
		document.getElementById("start_time").value = hours + ":" + minutes + ":" + seconds;


		 year = svlcEndTime.getFullYear();
		 month = svlcEndTime.getMonth() + 1;
		 month = svlcEndTime.getMonth() + 1;
		 intDate = svlcEndTime.getDate();
		
		 hours = svlcEndTime.getHours();
		 minutes = svlcEndTime.getMinutes();
		 seconds = svlcEndTime.getSeconds();

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

		
		document.getElementById("end_date").value = year + "/" + month + "/" + intDate;
		document.getElementById("end_time").value = hours + ":" + minutes + ":" + seconds;
	}
		
}
</script>
<form name="aform" method="post" style="width:100%; height:100%;">
<input type="hidden" id="imgUrl" name="imgUrl" value="">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
	<div style="width:100%; height:100%;" align="center">
	
		<table style="width:90%; heght:90%; margin:50px; border-radius:10px; background-color: #fff;-webkit-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		-moz-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		box-shadow: 0 10px 25px rgba(0,0,0,0.5);" border="0" cellspacing="5" cellpadding="5" >
			<colgroup>
				<col width="15%">
				<col width="25%">
				<col width="15%">
				<col width="45%">
			</colgroup>
			<tr style="height:40px;">
				<td colspan="4">
					
				</td>
			</tr>
			<tr style="height:30px;">
				<td colspan="4">
					<h2 style="margin-left: 30px;">Live Post</h2>
					<hr style="width:95%; margin-left: 30px; margin-right: 30px;" align="left">
				</td>
				
			</tr>
			<tr style="height:70px;">
				<td align="right" style="padding-right: 20px;">
					<span >Title</span>
				</td>
				<td >
					<input type="text" style="width:300px;" id="title" name="title">
				</td>
				<td align="right" style="padding-right: 20px;" >
					<span>Start Date</span>
				</td>
				<td valign="middle">
					<input   name="start_date" id="start_date" class='datepicker'  type="text" value="" onfocus="js_unmasking(this);" onblur="js_masking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="8" />
					     
	   				<input   type="text" name="start_time" id="start_time" value="" onfocus="js_timeunmasking(this);" onblur="js_timemasking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="6" class="ControlItemEdit" onkeypress="if(event.keyCode == 13) m_onGoToClicked(end_date.value, end_time.value);" onkeydown="event.cancelBubble = true;">
				</td>
			</tr>
			<tr style="height:70px;">
				<td align="right" style="padding-right: 20px;">
					<span>Category</span>
				</td>
				<td >
					<select style="width:322px;" id="category" name="category" onchange="onListChange(this);">
						<?php
						for($i = 0; $i < count($categoryInfo['category_id']); $i++)
						{?>
							<option value="<?php echo $categoryInfo['category_id'][$i]?>" <?php if($i==0){ ?> selected="selected" <?php }?>><?php echo $categoryInfo['category_name'][$i];?></option>
						<?php 
						}?>
					</select>
				</td>
				<td align="right" style="padding-right: 20px;" >
					<span>End Date</span>
				</td>
				<td >
					<input   name="end_date" id="end_date" class='datepicker' type="text" value="" onfocus="js_unmasking(this);" onblur="js_masking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="8"/>
	       			    
	   				<input    type="text" name="end_time" id="end_time" value="" onfocus="js_timeunmasking(this);" onblur="js_timemasking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="6" class="ControlItemEdit" onkeypress="if(event.keyCode == 13) m_onGoToClicked(end_date.value, end_time.value);" onkeydown="event.cancelBubble = true;">
				</td>
			</tr>
			<tr >
				<td align="right" style="padding-right: 20px;">
					<span>Channels</span>
				</td>
				<td  align="center" style="padding-right: 20px; height:70px;" >
					<select style="width:322px;" id="channels" name="channels" onchange="onChannelChange(this);">
						<?php
						
							for($i = 0; $i < count($channelInfo['channel_id']); $i++)
							{
								if($channelInfo['category_id'][$i] == $categoryInfo['category_id'][0])
								{ ?>
								<option value="<?php echo $channelInfo['channel_id'][$i]?>" ><?php echo $channelInfo['channel_name'][$i] ?> </option>
						<?php   } 
							}
						 ?>
					</select>				
				</td>
				
				<td align="right" style="padding-right: 20px;" >
					
				</td>
				<td>
					
				</td>
			</tr>
			
			<tr>
				<td valign="top" align="right" style="padding-right: 20px; padding-top: 30px;">
					<span>Description</span>
				</td>
				<td align="center" style="padding-right: 20px; height:70px;" >
					<textarea style="resize:none; height:220px; width:300px;" id="description" name="description" value=""></textarea>		
				</td>
				
				<td colspan="2" align="right" style="padding-right: 20px; padding-left: 100px;" >
					<table style="height:100%; float: right; display:inline-block">
						<colgroup>
							<col width="50%">
							<col width="50%">
						</colgroup>
						<tr>
							<td align="right" >
								<img id="charImg" style="border:5px solid #e0e0e0; width:190px;height:240px;margin-right:30px;" src="<?php echo get_template_directory_uri()?>/images/5.jpg" >
							</td>
							<td valign="top" align="left">
								<iframe frameborder="no" marginwidth="0px" marginheight="0px"  src="<?php echo home_url()?>/fileUpload/" style="margin-top:20px; width:400px;height:50px;" scrolling="no">
								</iframe>
								<div style="height:50px; margin-top:120px; margin-right: 100px;" >
									<input type="button" id="addProgramBtn" onclick="onBtnUpdateProgram();" value="Update Program" style="color:#ffffff; width:150px; height:50px;border-radius:20px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');">
									<img id="wait" style="width:35px;height:25px;margin-left:30px; padding-top:10px; padding:0px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div style="height:50px;"></div>
					<div></div>
				</td>
			</tr>
			
		</table>
	</div>
	
</form>
<?php
	$programIndex = $_POST['programIndex'];
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	
	$sql_query = "select 
							lpr.program_id,
							lpr.channel_id,
							lpr.title,
							lpr.image_path,
							lpr.vod_enable,
							lpr.latitude,
							lpr.longitude,
							lpr.start_time,
							lpr.end_time,
							lpr.description,
							lch.category_id 
				  from 
							wp_program lpr
				  inner join
							wp_channel lch
				  on 		
							lch.channel_id = lpr.channel_id
				  where 	
							lpr.program_id = '" . $programIndex . "'"; 
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query fail.";
		exit;
	}
	
	$row = mysql_fetch_array($result);
	mysql_free_result($result);
	mysql_close($link);
?>
<script>
	
	progInfo = {
			programIndex : "<?php echo $programIndex?>",
			title : "<?php echo $row['title']?>",
			start_time : "<?php echo $row['start_time']?>",
			end_time : "<?php echo $row['end_time']?>",
			vod_enable : "<?php echo $row['vod_enable']?>",
			category_id : "<?php echo $row['category_id']?>",
			channel_id : "<?php echo $row['channel_id']?>",
			description : "<?php echo $row['description']?>",
			image_path : "<?php echo home_url() . $row['image_path']?>"
			};

	/*var tempDate = "<?php echo $row['start_time']?>".split(" ")[0].replace("-","/");
	tempDate = tempDate.replace("-","/");
	
	document.getElementById("start_date").value = tempDate;
	document.getElementById("start_time").value = "<?php echo $row['start_time']?>".split(" ")[1];

	tempDate = "<?php echo $row['end_time']?>".split(" ")[0].replace("-","/");
	tempDate = tempDate.replace("-","/");
	
	document.getElementById("end_date").value = tempDate;
	document.getElementById("end_time").value = "<?php echo $row['end_time']?>".split(" ")[1];
	*/
	var start_date = "<?php echo $row['start_time']?>".split(" ")[0];
	var start_time = "<?php echo $row['start_time']?>".split(" ")[1];
	var end_date = "<?php echo $row['end_time']?>".split(" ")[0];
	var end_time = "<?php echo $row['end_time']?>".split(" ")[1];
	
	var year = start_date.split("-")[0];
	var month = start_date.split("-")[1];
	var dateStr = start_date.split("-")[2];

	var hours = start_time.split(":")[0];
	var minutes = start_time.split(":")[1];
	var seconds = start_time.split(":")[2];

	if(month.substring(0,1) == "0")
		month = month.substring(1,2);
	if(dateStr.substring(0,1) == "0")
		dateStr = dateStr.substring(1,2);
	if(hours.substring(0,1) == "0")
		hours = hours.substring(1,2);
	if(minutes.substring(0,1) == "0")
		minutes = minutes.substring(1,2);
	if(seconds.substring(0,1) == "0")
		seconds = seconds.substring(1,2);

	var svGmtTime = new Date(0);
	svGmtTime.setFullYear(parseInt(year));
	svGmtTime.setMonth(parseInt(month) - 1);
	svGmtTime.setMonth(parseInt(month) - 1);
	svGmtTime.setDate(parseInt(dateStr));
	svGmtTime.setHours(parseInt(hours));
	svGmtTime.setMinutes(parseInt(minutes));
	svGmtTime.setSeconds(parseInt(seconds));

	var ctGmtTime = svGmtTime.getTime() + periodClientServerGmtTime;
	var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

	year = ctLocalTime.getFullYear().toString();
	month = (ctLocalTime.getMonth() + 1).toString();
	dateStr = ctLocalTime.getDate().toString();
	hours = ctLocalTime.getHours().toString();
	minutes = ctLocalTime.getMinutes().toString();
	seconds = ctLocalTime.getSeconds().toString();

	if(month.length == 1)
		month = "0" + month;
	if(dateStr.length == 1)
		dateStr = "0" + dateStr;
	if(hours.length == 1)
		hours = "0" + hours;
	if(minutes.length == 1)
		minutes = "0" + minutes;
	if(seconds.length == 1)
		seconds = "0" + seconds;

	document.getElementById("start_date").value = year + "/" + month + "/" + dateStr; 
	document.getElementById("start_time").value = hours + ":" + minutes + ":" + seconds;



	year = end_date.split("-")[0];
	month = end_date.split("-")[1];
	dateStr = end_date.split("-")[2];

	hours = end_time.split(":")[0];
	minutes = end_time.split(":")[1];
	seconds = end_time.split(":")[2];

	if(month.substring(0,1) == "0")
		month = month.substring(1,2);
	if(dateStr.substring(0,1) == "0")
		dateStr = dateStr.substring(1,2);
	if(hours.substring(0,1) == "0")
		hours = hours.substring(1,2);
	if(minutes.substring(0,1) == "0")
		minutes = minutes.substring(1,2);
	if(seconds.substring(0,1) == "0")
		seconds = seconds.substring(1,2);

	var svGmtTime = new Date(0);
	svGmtTime.setFullYear(parseInt(year));
	svGmtTime.setMonth(parseInt(month) - 1);
	svGmtTime.setMonth(parseInt(month) - 1);
	svGmtTime.setDate(parseInt(dateStr));
	svGmtTime.setHours(parseInt(hours));
	svGmtTime.setMinutes(parseInt(minutes));
	svGmtTime.setSeconds(parseInt(seconds));

	var ctGmtTime = svGmtTime.getTime() + periodClientServerGmtTime;
	var ctLocalTime = new Date(ctGmtTime + periodClientLocalGmt);

	year = ctLocalTime.getFullYear().toString();
	month = (ctLocalTime.getMonth() + 1).toString();
	dateStr = ctLocalTime.getDate().toString();
	hours = ctLocalTime.getHours().toString();
	minutes = ctLocalTime.getMinutes().toString();
	seconds = ctLocalTime.getSeconds().toString();

	if(month.length == 1)
		month = "0" + month;
	if(dateStr.length == 1)
		dateStr = "0" + dateStr;
	if(hours.length == 1)
		hours = "0" + hours;
	if(minutes.length == 1)
		minutes = "0" + minutes;
	if(seconds.length == 1)
		seconds = "0" + seconds;

	document.getElementById("end_date").value = year + "/" + month + "/" + dateStr; 
	document.getElementById("end_time").value = hours + ":" + minutes + ":" + seconds;
	
	document.getElementById("title").value = "<?php echo $row['title']?>";

	
	

	

	

	var category = document.getElementById("category");
	for(i = 0; i < category.options.length; i++)
	{
		if(category.options[i].value.toString() == "<?php echo $row['category_id']?>")
		{
			category.options[i].selected = true;
			break;
		}
	}

	var channels = document.getElementById("channels");
	for(i = 0; i < channels.options.length; i++)
	{
		if(channels.options[i].value.toString() == "<?php echo $row['channel_id']?>")
		{
			channels.options[i].selected = true;
			break;
		}
	}

	document.getElementById("charImg").src = "<?php echo home_url() . $row['image_path']?>";
	document.getElementById("imgUrl").value = "<?php echo home_url() . $row['image_path']?>";
	document.getElementById("description").value = "<?php echo $row['description']?>";
</script>
<?php

get_footer(); 
?>