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
	
	$mirror = $_GET['mirrorStatus'];
	
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

    $lat = $data["latitude"];
    $lon = $data["longitude"];
    $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lon&language=en";
    $cont = file_get_contents($url);
    $location_data = json_decode($cont , true);

	$serverCurrGmtTime = getdate(); // server gmt time
	
	
	$serverCurrLocalTime = time() * 1000; // server current time
?>

<script>
    document.getElementById("main-header").style.display = "none";
    document.getElementById("headerShadowDiv").style.display = "none";

	var dateTemp = new Date();
    var clientlocation = "";
	
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

    var thumbnailName = (Math.floor(Math.random() * (999999999 - 100000000 + 1)) + 100000000) + ".png";


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

var mirror = "";
var cameraNo = "";

var clientLat = 0;
var clientLng = 0;

jQuery(document).ready( function () {
	
	//document.documentElement.style.overflow = 'hidden';
	var temp = "<?php echo $mirror?>";
	mirror = temp.split("_")[0];
	cameraNo = temp.split("_")[1];
	
	document.getElementById("mirrorFlag").value = mirror;
	document.getElementById("camNumber").value = cameraNo;
	
	var browserWidth = $(window).width();
	document.getElementById("main-header").style.minWidth = browserWidth + "px";

	document.getElementById("main-header").style.position = "relative";
			
	var geocoder = new google.maps.Geocoder();
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
	    	currentLat = position.coords.latitude;
	    	currentLng = position.coords.longitude;
	    	//alert(currentLat);
	    	//alert(currentLng);


            setLocation(currentLat, currentLng);
	   
	     }, function() {
	     });
	 }else {}

});

function setLocation(latitude, longtitude)
{
    //
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        req = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        req = new ActiveXObject("Microsoft.XMLHTTP");
    }
    req.onreadystatechange=function()
    {
        if(req.readyState==4 && req.status==200)
        {
            if (req.responseText)
            {
                var json = JSON.parse(req.responseText);
                clientlocation = json.results[0].formatted_address;
            }
        }
    }

    req.open("GET","http://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," + longtitude + "&language=en", true);
    req.send();
}

 
function onBtnAddProgram()
{
	//alert( currentLat );
	//alert( currentLng );

    if ( (currentLat != 0 ) && (currentLng != 0 ) ) {
        clientLat = currentLat;
        clientLng = currentLng;
    }
    else {
        clientLat = "<?php echo $data['latitude']?>"
        clientLng = "<?php echo $data['longitude']?>"
    }

    var location;
    if (clientlocation) location = clientlocation;
    else location = "<?php echo $location_data["results"][0]["formatted_address"]?>"

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
//	if(document.getElementById("channels").value == "")
//	{
//		alert("select channel!");
//		document.getElementById("addProgramBtn").disabled = false;
//
//		return;
//	}

    var tags = document.getElementById("tags");

    if(tags.value == "")
    {
        alert("Input tags!");
        tags.focus();
        document.getElementById("addProgramBtn").disabled = false;

        return;
    }

    var re = /#[a-zA-Z0-9]+/g;
    var str = tags.value;
    var m;

    var tagsString = "";
    var tagsFormattedString = "";

    while ((m = re.exec(str)) != null) {
        if (m.index === re.lastIndex) {
            re.lastIndex++;
        }
        var tag = m[0].substr(1, m[0].length);
        tagsString = tagsString + tag + ",";
        tagsFormattedString = tagsFormattedString + "#" + tag + " ";
        // View your result using the m-variable.
        // eg m[0] etc.
    }

    if (tagsString == "")
    {
        alert("Please, enter tags in the following format: #tag1 #tag2 #tag3");
        tags.focus();
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
		//alert("Upload image");
		//return;
	}
//	var channel_id = document.getElementById("channels").value;
	var channel_id = 40;
	
	
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


		    if (xmlhttp.responseText.indexOf("banned") > -1)
            {
                if (xmlhttp.responseText.split("banned ")[1] == "-1")
                    alert ("You have been banned permanently");
                else
                    alert ("You have been banned until " + xmlhttp.responseText.split("banned ")[1] + " (server time)");
                parent.fn_logout();
                parent.fn_closePopup();
                return;
            }
		    var pid = xmlhttp.responseText.split(",")[0];
		    //var cid = xmlhttp.responseText.split(",")[1];

		    document.getElementById("addProgramBtn").disabled = false;
		    document.getElementById("wait").style.display = "none";
			//alert(pid + "," + cid);
//		    document.getElementById("broadCh_id").value = document.getElementById("channels").value;
            document.getElementById("broadCh_id").value = 40;
			document.getElementById("broadProg_id").value = pid;

<!--			document.aform.action = "--><?php //echo home_url()?><!--/broadcast/";-->
<!--			document.aform.submit();-->


            parent.showRecordingPage(document.getElementById("broadCh_id").value, document.getElementById("broadProg_id").value, title.value, document.getElementById("description").value, tagsFormattedString, imgurl, clientlocation);
            parent.fn_closePopup();

		}
	  }
	 
	imgurl = imgurl.substring("<?php echo home_url()?>".length,imgurl.length);
	document.getElementById("wait").style.display = "inline"; 
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxAddProgram.php?channel_id="+channel_id + "&userid=" + "<?php echo $_SESSION['user_id']?>" + "&title=" + encodeURIComponent(title.value) + "&imgurl=" + imgurl + "&vod_enable=" + vod_enable + "&latitude=" + clientLat  + "&longitude=" + clientLng + "&location=" + clientlocation + "&vodpath="+document.getElementById("vodpath").value + "&description=" + encodeURIComponent(document.getElementById("description").value) + "&tags=" + encodeURIComponent(tagsString),true);
	xmlhttp.send();
}

function onTagsChange(tagsTextArea)
{
    var tagsRegExp = /^[#\w\s]*$/;
    if (tagsTextArea.value.match(tagsRegExp) == null)
    {
        tagsTextArea.value = tagsTextArea.value.slice(0, -1);
        alert("Invalid character! Please, use only letters, numbers and underscore.");
    }
}

var localStream;
function takePicture()
{
        // Grab elements, create settings, etc.
        var canvas = document.getElementById("canvas"),
            context = canvas.getContext("2d"),
            video = document.getElementById("video"),
            videoObj = { "video": true },
            errBack = function(error) {
                console.log("Video capture error: ", error.code);
            };
        if (!video.paused) return;
        canvas.width = 163;
        canvas.height = 163;

        if (!navigator.mozGetUserMedia) MediaStreamTrack.getSources(function(sourceInfos) {
            var audioSource = null;
            var videoSource = null;
            var currentVideoSourceId = -1;

            for (var i = 0; i != sourceInfos.length; ++i) {
                var sourceInfo = sourceInfos[i];
                if (sourceInfo.kind === 'video') {
                    currentVideoSourceId++;
                    if (currentVideoSourceId == window.parent.document.getElementById("camNo").value) videoSource = sourceInfo.id;
                } else {
                    console.log('Some other kind of source: ', sourceInfo);
                }
            }

            sourceSelected(videoSource);
        });
        else
        {
            navigator.mozGetUserMedia(videoObj, function(stream){
                video.src = window.URL.createObjectURL(stream);
                video.play();
                localStream = stream;
            }, errBack);

            video.addEventListener("timeupdate", onVideoTimeUpdate, false);
        }

        function sourceSelected(videoSource) {
            var constraints = {
                video: {
                    optional: [{sourceId: videoSource}]
                }
            };
            // Put video listeners into place
            if(navigator.getUserMedia) { // Standard
                navigator.getUserMedia(constraints, function(stream) {
                    video.src = stream;
                    video.play();
                    localStream = stream;
                }, errBack);
            } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
                navigator.webkitGetUserMedia(constraints, function(stream){
                    video.src = window.webkitURL.createObjectURL(stream);
                    video.play();
                    localStream = stream;
                }, errBack);
            }
            else if(navigator.mozGetUserMedia) { // Firefox-prefixed
                navigator.mozGetUserMedia(videoObj, function(stream){
                    video.src = window.URL.createObjectURL(stream);
                    video.play();
                    localStream = stream;
                }, errBack);
            }
            video.addEventListener("timeupdate", onVideoTimeUpdate, false);
        }
}

function onVideoTimeUpdate()
{
    var canvas = document.getElementById("canvas"),
        context = canvas.getContext("2d"),
        video = document.getElementById("video");

    context.drawImage(video, 0, 0, 163, 163);
    var dataURL = canvas.toDataURL();
    document.getElementById('charImg').src = dataURL;
    if (video.duration > 10)
    {
        video.removeEventListener("timeupdate", onVideoTimeUpdate, false);
        video.pause();
        localStream.stop();

        var blobBin = atob(dataURL.split(',')[1]);
        var array = [];
        for(var i = 0; i < blobBin.length; i++) {
            array.push(blobBin.charCodeAt(i));
        }
        var file = new Blob([new Uint8Array(array)], {type: 'image/png'});

        var reader = new FileReader();

        reader.onload = function(event){
            var fd = {};
            fd["fname"] = thumbnailName;
            fd["data"] = event.target.result;
            $.ajax({
                type: 'POST',
                url: "<?php echo get_template_directory_uri()?>/uploadThumbnail.php",
                data: fd,
                dataType: 'text'
            });
        };
        // trigger the read from the reader...
        reader.readAsDataURL(file);

        document.getElementById('imgUrl').value = "<?php echo home_url()?>/wp-content/uploads/thumbnails/" + thumbnailName;
    }
}


</script>
	
<!-- <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/add-program.css" />  -->

<form name="aform" method="post" >
<input type="hidden" id="imgUrl" name="imgUrl" value="">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" value="" id="emapInfo" name="emapInfo">	
<input type="hidden" value="" id="focusValue" name="focusValue">

	<div style="width:100%; height:100%; margin-left: auto; margin-right: auto; min-width: 1024px; max-width: 1366px;" align="center">
	
		<table style="width:100%; height:100%; background-color: #fff;-webkit-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		-moz-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		box-shadow: 0 10px 25px rgba(0,0,0,0.5);" border="0" cellspacing="10" cellpadding="10" >
			<colgroup>
				<col width="25%">
				<col width="45%">
				<col width="30%">
			</colgroup>
<!-- 		<tr style="height:30px;">
				<td colspan="3">
					<input id="vodpath" name="vodpath" type="hidden" style="width:345px;" readonly value="sample.mp4">	
				</td>
			</tr>  -->	
			
			<tr style="height:30px;" >
				<td colspan="3" style="text-align: center;" >
					<input type="hidden" style="width:89%: height: 48px;" value="" /><img src="<?php echo get_template_directory_uri() ?>/images/add-program-title.png" style="width:86%; height: 43px; margin-top: 13px;">
				</td>
			</tr>
			
			<tr style="height:70px;" >
				<td colspan="3" style="height: 176px;" align="center">
					<div style="width: 100%; height: auto; position: relative; text-align: center; font-family: 'Open Sans', Arial, sans-serif; font-size: 17px; font-weight: 600;">
						<img src="<?php echo get_template_directory_uri() ?>/images/stream-info.png" style="height:197px; width:86%;">
						<span style="position: absolute; top: 31%; left: 9%; ">Title</span>
						<input type="text"  id="title" class="addprogram-title" name="title">
						<span style="position: absolute; top: 31%; left: 39%;" >Description</span>
						<textarea style="resize:none; height: 43%; width: 39%; position: absolute; top: 30%; right: 13%; margin-right: -4%;" id="description" class="addprogram-description" name="description" value=""></textarea>
						<span style="position: absolute; top: 56%; left: 9%;" >Tags</span>
						<textarea style="resize:none; height: 17%; width: 30%; position: absolute; top: 56%; left: 15%; margin-right: -4%" id="tags" name="tags" oninput="onTagsChange(this);"></textarea>
						
					</div>
				</td>
			</tr>
			
			<tr>
				<td align="center">
					<img id="charImg" style="border:2px solid #e0e0e0; border-radius:10px; width: 163px; height: 163px; margin-left:29%;" src="<?php echo get_template_directory_uri()?>/images/profileImage.png" >
                    <canvas id="canvas" style="border:2px solid #e0e0e0; border-radius:10px; width: 163px; height: 163px; margin-left:29%; display:none"></canvas>
                </td>
				<td valign="top" align="left">
					<iframe frameborder="no" marginwidth="0px" marginheight="0px"  src="<?php echo home_url()?>/fileUpload/" style="float: left; margin-top:20px; width: 273px; height:50px; margin-left: -4px;" scrolling="no">
					</iframe>
					<div style="float: left; margin-left: 8px; margin-top: 22px;"><img src="<?php echo get_template_directory_uri()?>/images/thumnail.png" style="width: 102px;"></div>
					
					<img src="<?php echo get_template_directory_uri() ?>/images/takePictureButton.png" class="take-picture-button" onclick="takePicture();">
					<img src="<?php echo get_template_directory_uri() ?>/images/optional.png">
                    <video id="video" width="163" height="163" autoplay style="display:none"></video>

				</td>
				<td  align="center" style="padding-right: 20px; height:70px; display:none;" >
					<select style="width:322px;" id="channels" name="channels" >
						<?php
						
							for($i = 1; $i < count($channelInfo['channel_id']); $i++)
							{
								if($channelInfo['category_id'][$i] == $categoryInfo['category_id'][1])
								{ ?>
								<option value="<?php echo $channelInfo['channel_id'][$i]?>" ><?php echo $channelInfo['channel_name'][$i] ?> </option>
						<?php   } 
							}
						 ?>
					</select>				
				</td>				
			</tr>
			
			<tr>
				<td align="center">
					<div style="margin-right: -36%;">
						<a href="<?php echo home_url() ?>/voice_test/" ><img src="<?php echo get_template_directory_uri() ?>/images/previousButton.png" class="previousButton" ></a>
					</div>
				</td>
				<td>
					<img id="wait" style="width:35px;height:25px; float:right;margin-top:10px; padding:0px; display:none;"  src="<?php echo get_template_directory_uri() ?>/images/small_loading.gif">
					<input id="vodpath" name="vodpath" type="hidden" style="width:345px;" readonly value="sample.mp4">
				</td>
				<td align="center">
					<div style="margin-right: 23%;">
						<input type="button" id="addProgramBtn" onclick="onBtnAddProgram();" value="" style="width: 210px; height: 43px; cursor: pointer; background-image: url(<?php echo get_template_directory_uri()?>/images/recordNowButton.png);">
					</div>
				</td>
			</tr>
			

		</table>
	</div>
	<input type="hidden" id="broadCh_id" name="broadCh_id" value="">
	<input type="hidden" id="broadProg_id" name="broadProg_id" value="">
	<input type="hidden" id="mirrorFlag" name="mirrorFlag" value="">
	<input type="hidden" id="camNumber" name="camNumber" value="">
</form>

<style>
	
.previousButton {
	border: 2px solid #dddddd;
}
.previousButton:hover {
	opacity: 0.6;
}
#addProgramBtn:hover {
	opacity: 0.6;
}
.take-picture-button {
	cursor: pointer;
	border: 2px solid #dddddd;
}
.take-picture-button:hover {
	opacity: 0.6;
}
</style>

<?php
	
//get_footer(); 
?>