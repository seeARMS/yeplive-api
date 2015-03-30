<?php
/*

Template name: addprogram

*/
if(!isset($_SESSION)) session_start();

if($_SESSION['user_id'] == "")
{
	echo "please login!";
	exit();
}
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
		/*$sql_query = "select 
								channel_id,
								channel_name 
					  from 
								wp_channel 
					  where 
								category_id = '" . $categoryInfo['category_id'][0] ."'";*/
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
	
	mysql_close($link);
	
	$key="9dcde915a1a065fbaf14165f00fcc0461b8d0a6b43889614e8acdb8343e2cf15";
	
	//$ip=$_SERVER['REMOTE_ADDR'];
	// my adding 
	$ip = '';
	if ($_SERVER['HTTP_CLIENT_IP'])
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	else if($_SERVER['HTTP_X_FORWARDED_FOR'])
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if($_SERVER['HTTP_X_FORWARDED'])
		$ip = $_SERVER['HTTP_X_FORWARDED'];
	else if($_SERVER['HTTP_FORWARDED_FOR'])
		$ip = $_SERVER['HTTP_FORWARDED_FOR'];
	else if($_SERVER['HTTP_FORWARDED'])
		$ip = $_SERVER['HTTP_FORWARDED'];
	else if($_SERVER['REMOTE_ADDR'])
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = 'UNKNOWN';
	
	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=json";
	$cont = file_get_contents($url);
	$data = json_decode($cont , true);
	$dd = $data["latitude"];
	
	$serverCurrGmtTime = getdate(); // server gmt time
	
	
	$serverCurrLocalTime = time() * 1000; // server current time
?>


<script>
	var dateTemp = new Date();
	
	var localTime = Date.UTC(dateTemp.getFullYear()  , dateTemp.getMonth() , dateTemp.getDate() , dateTemp.getHours() , dateTemp.getMinutes() , dateTemp.getSeconds());
	
	var periodClientLocalGmt = localTime - (dateTemp.getTime() - dateTemp.getTime() % 1000) ;
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
var currentLat;
var currentLng;

$(document).ready( function () {
	var geocoder = new google.maps.Geocoder();
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
	    	currentLat = position.coords.latitude;
	    	currentLng = position.coords.longitude;	
	    	// alert(currentLat);
	    	// alert(currentLng);	  
	   
	     }, function() {
	     });
	 }else {}
});

function onBtnAddProgram()
{
	//alert( currentLat );
	//alert( currentLng );
	var clientLat = 0;
	var clientLng = 0;
	
	if ( (currentLat != 0 ) && (currentLng != 0 ) ) {
		clientLat = currentLat;
		clientLng = currentLng;
	}
	else {
		clientLat = "<?php echo $data['latitude']?>"
		clientLng = "<?php echo $data['longitude']?>"
	}
	delete start_timeArr;
	delete end_timeArr;
	start_timeArr = new Array();
	end_timeArr = new Array();
	var title = document.getElementById("title");
	
	var imgurl = document.getElementById("imgUrl").value;
	//var vod_enable = document.getElementById("vodEnable").checked == true ? 1 : 0;
	var vod_enable = 1; 
	var vod_path = "";
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
	
	if(document.getElementById("description").value == "")
	{
		alert("Input description");
		document.getElementById("description").focus();
		document.getElementById("addProgramBtn").disabled = false;
		return;
	}
	
	
	if(imgurl == "")
	{
		
	}
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
		if(xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
		    
		    var pid = xmlhttp.responseText.split(",")[0];
		    //var cid = xmlhttp.responseText.split(",")[1];

		    document.getElementById("addProgramBtn").disabled = false;
		    document.getElementById("wait").style.display = "none";
			//alert(pid + "," + cid);
		    document.getElementById("broadCh_id").value = document.getElementById("channels").value;
			document.getElementById("broadProg_id").value = pid;

			document.aform.action = "<?php echo home_url()?>/broadcast/";
			document.aform.submit();
		}
	  }
	 
	imgurl = imgurl.substring("<?php echo home_url()?>".length,imgurl.length);
	document.getElementById("wait").style.display = "inline"; 
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxAddProgram.php?channel_id="+channel_id + "&userid=" + "<?php echo $_SESSION['user_id']?>" + "&title=" + title.value + "&imgurl=" + imgurl + "&vod_enable=" + vod_enable + "&latitude=" + clientLat  + "&longitude=" + clientLng + "&vodpath="+document.getElementById("vodpath").value + "&description=" + document.getElementById("description").value,true);
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
		
		//onChannelChange(channelListBox);
	}
	
}
function onChannelChange(obj)
{
	
		
}


</script>
<form name="aform" method="post" >
<input type="hidden" id="imgUrl" name="imgUrl" value="">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
	<div style="width:100%; height:100%;" align="center">
	
		<table style="width:90%; heght:90%; margin:30px; border-radius:10px; background-color: #fff;-webkit-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
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
					<span>Description</span>
				</td>
				<td valign="middle" style="position: relative;" rowspan="3" align="left">
					<textarea style="resize:none; height:160px; width:350px;" id="description" name="description" value=""></textarea>
					<!-- <input   name="start_date" id="start_date" class='datepicker'  type="text" value="" onfocus="js_unmasking(this);" onblur="js_masking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="8" />
					     
	   				<input   type="text" name="start_time" id="start_time" value="" onfocus="js_timeunmasking(this);" onblur="js_timemasking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="6" class="ControlItemEdit" onkeypress="if(event.keyCode == 13) m_onGoToClicked(end_date.value, end_time.value);" onkeydown="event.cancelBubble = true;">
	   				 -->
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
					<span></span>
				</td>
				<!-- <td style="position:relative;">
					<input   name="end_date" id="end_date" class='datepicker' type="text" value="" onfocus="js_unmasking(this);" onblur="js_masking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="8"/>
	       			    
	   				<input    type="text" name="end_time" id="end_time" value="" onfocus="js_timeunmasking(this);" onblur="js_timemasking(this);" onkeypress="return js_checkNumeric(event);"  maxlength="6" class="ControlItemEdit" onkeypress="if(event.keyCode == 13) m_onGoToClicked(end_date.value, end_time.value);" onkeydown="event.cancelBubble = true;">
				</td> -->
			</tr>
			<tr >
				<td align="right" style="padding-right: 20px;">
					<span>Channels</span>
				</td>
				<td  align="center" style="padding-right: 20px; height:70px;" >
					<select style="width:322px;" id="channels" name="channels" >
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
				
				
				<td>
					<input id="vodpath" name="vodpath" type="hidden" style="width:345px;" readonly value="sample.mp4">
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding-right: 20px; padding-top:20px;" >
					<span></span>
				</td>
				<td align="center" style="padding-right: 20px; " valign="top">
					<!-- <select name="duration" id="duration" style="width:100%;">
						<option value="15">15 Min</option>
						<option value="30">30 Min</option>
						<option value="45">45 Min</option>
						<option value="60">1 Hour</option>
					</select>
					 -->		
				</td>
				
				<td colspan="2" align="left" style="padding-right: 20px; padding-left: 150px;" >
					<table style="height:100%; float: left; display:inline-block; ">
						<colgroup>
							<col width="50%">
							<col width="50%">
						</colgroup>
						<tr>
							<td align="center">
								<img id="charImg" style="border:2px solid #e0e0e0; border-radius:10px; width:150px;height:200px;margin-right:10px;" src="<?php echo get_template_directory_uri()?>/images/5.jpg" >
							</td>
							<td valign="top" align="left">
								<iframe frameborder="no" marginwidth="0px" marginheight="0px"  src="<?php echo home_url()?>/fileUpload/" style="margin-top:20px; width:230px;height:50px;" scrolling="no">
								</iframe>
								<div style="height:50px; margin-top:80px; padding-left: 20px;" align="left">
									<input type="button" id="addProgramBtn" onclick="onBtnAddProgram();" value="Add Program" style="color:#ffffff; width:150px; height:50px;border-radius:10px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');">
									<img id="wait" style="width:35px;height:25px; float:right;margin-top:10px; padding:0px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
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
	<input type="hidden" id="broadCh_id" name="broadCh_id" value="">
	<input type="hidden" id="broadProg_id" name="broadProg_id" value="">
</form>


<?php
	
get_footer(); 
?>