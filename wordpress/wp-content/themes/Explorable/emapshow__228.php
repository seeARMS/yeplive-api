<?php
/*

Template name: emapshow

*/
get_header();

/* global $user_ID;
if(!is_user_logged_in())
{
	echo "<script type='text/javascript'>alert ('First, you must login.'); location.href='".home_url() . "/login/"."'</script>";
	exit();
}
*/
	
	
	$emap_items = et_get_emap_items();


	$serverCurrGmtTime = getdate(); // server gmt time
	$serverCurrLocalTime = time() * 1000; // server current time
	
	$currentTime = date('Y-m-d H:i:s');
	
	$beforeDate = time() - (1 * 24 * 60 * 60);
	$beforeTime = date('Y-m-d H:i:s', $beforeDate);
?>

<script>
	
	var progInfo = new Array();
	<?php	
	
		foreach($emap_items as $row => $value)
		{?>

			var start = "<?php echo $value->start_time?>";
			var end = "<?php echo $value->end_time?>";
			
			var total_Time = dateCal(start , end);
		  	
			progInfo[ progInfo.length ] = 
				{
					pid : "<?php echo $value->pid?>",
					vote : "<?php echo $value->vote?>",
					cid : "<?php echo $value->cid?>",
					start_time : "<?php echo $value->start_time?>",
					end_time : "<?php echo $value->end_time?>",
					vod_path : "<?php echo $value->vod_path?>",
					vod_enable : "<?php echo $value->vod_enable?>",
					user_id : "<?php echo $value->userid?>",
					username : "<?php echo $value->username?>",
					facebook_name : "<?php echo $value->facebook_name?>",
					twitter_name : "<?php echo $value->twitter_name?>",
					lat : "<?php echo $value->lat?>",
					lgt : "<?php echo $value->lgt?>",
					title : "<?php echo $value->title?>",
					description : "<?php echo $value->description?>",
					isMobile : "<?php echo $value->isMobile?>",
					connect_count : "<?php echo $value->connect_count ?>",
					imagePath : "<?php echo $value->image_path ?>",
					totalTime : total_Time	
				};
		<?php 
		}
	?>

	function dateCal ( start , end ) {

		var total_Time;
		if ( start != "" && end != "")
	  	{
		  	start = start.split(" ");
		  	end = end.split(" ");
		  	var startData = start[1].split(":");
		    var endData = end[1].split(":");
		    
		    var diff = parseInt( (endData[0] * 60 * 60 ) + parseInt( endData[1] * 60 ) + parseInt( endData[2] ) 
				    		- parseInt( startData[0])*60*60 ) - parseInt( startData[1] *60 ) - parseInt( startData[2]);
		    var hours = Math.floor(diff / 60 / 60);
		    diff -= hours * 60 * 60;
		    var minutes = Math.floor(diff / 60);
		    var second = diff - minutes * 60;

			if ( hours > -1 && hours < 10 ) 
				hours = "0" + hours.toString();
			if ( minutes > -1 && minutes < 10 )
				minutes = "0" + minutes;
			if ( second > -1 && second < 10 )
				second = "0" + second.toString();
			
		    total_Time = hours + ":" + minutes + ":" + second ;
	  	}
		else {
			total_Time = "";
		}
		return total_Time;
	}

	
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
<script
src="http://maps.googleapis.com/maps/api/js?key=AIzaSyB9xlXkB3uTgBz4eI62ke009LcYVrC8AjM&sensor=true">
</script>
<style>
.et-map-slide-clone {
	display: none;
}
</style>
<div id="et_main_map" style="position: static !important;"></div>

<div id="video-list">
	<div class="category-title">
		<img class="cancel-img" src="<?php echo get_template_directory_uri()?>/images/close.png" onclick="onCancel();"></img>
		<p id="normal-letter"></p>
 		<div class="belt"></div> 
	</div>
	<div id="video" >
		
	</div>
	
	<div class="sub-videolist" style="display: none;">
		<img class="video-img" src="">
		<div class="video-title">
			<span class="title-attr"></span>
			<span class="video-period"></span>
		</div>
		<div class="video-author">
			<span class="author"></span>
			<span class="viewers"></span>
		</div>
		<input type="hidden" text="" value="" />
	</div>
	<div class="underline" style="display: none;">
	</div>
</div>


<script>
var $et_main_map = $( '#et_main_map' );


	function onCancel () {
		document.getElementById("video-list").style.display = "none";
		var myNode = document.getElementById("video");
		while (myNode.firstChild) {
		    myNode.removeChild(myNode.firstChild);
		}
	}
	
	function onmenuClick (categoryName) {
		show_menu_out();
		document.getElementById("video-list").style.display = "none";
		var myNode = document.getElementById("video");
		while (myNode.firstChild) {
		    myNode.removeChild(myNode.firstChild);
		}
	    switch(categoryName) {
	    case "News & Politics":
		    title = 40;
		    break;
	    case "Events/Concerts":
		    title = 41;
		    break;
	    case "Celebrities":
		    title = 42;
		    break;
	    case "People":
		    title = 43;
		    break;
	    case "Sports":
		    title = 44;
		    break;
	    case "Gaming":
		    title = 45;
		    break;
	    case "Must See!":
		    title = 39;
		    break;
		default:
		    break;
	    }

	    jQuery.ajax({
			type: "POST",
		  	url: "<?php echo get_template_directory_uri()?>/ajaxVideoListQuery.php/",
		  	data: "title=" + title,
		  	success: function(data) {
			  	if ( data == "" )
				  	alert("There is no video in that category");
			  	else 
				  	videoListShow(data , categoryName);					  	
		  	},
		  	error: function (data) {
			  	alert('error');
		  	}
		});
	    
	}
	
	function videoListShow (obj,categoryName) {
		
		document.getElementById("video-list").style.display = "block";
		if(document.getElementsByClassName("category-title").length > 0) {
			document.getElementById("normal-letter").innerHTML = "LIST OF VIDEOS FROM \"" + categoryName.toUpperCase() + "\"" ;
		}
		
		var tempArr = obj.split("---");
	  	var tempArr1;
	  	var tempDiv = document.getElementById("video-list");
		var homeUrl = "<?php echo home_url();?>";

		$et_main_map.gmap3({ clear: {} })
		var temp = new Array();
		
	  	for(i = 0; i < tempArr.length; i++)
	  	{
		  	tempArr1 = tempArr[i].split(",");
		  	var imgUrl = homeUrl + tempArr1[3];
		  	if ( tempArr1[3] == "" || tempArr1[3] == 0 ) {
			  	imgUrl = "<?php echo get_template_directory_uri()?>/images/myprofile.png";
		  	}
		  	var CloneObj = $('<div class="sub-videolist" style="display: none;">' +
								'<img class="video-img" src="">' +
								'<div class="video-title"><span class="title-attr"></span><span class="video-period"></span></div>' +
								'<input type="hidden" text="" id="programId" value="" />' +
								'<input type="hidden" text="" id="channelId" value="" />' +
								'<input type="hidden" text="" id="title" value="" />' +
								'<input type="hidden" text="" id="imagePath" value="" />' +
								'<input type="hidden" text="" id="vodEnable" value="" />' +
								'<input type="hidden" text="" id="vodPath" value="" />' +
								'<input type="hidden" text="" id="vote" value="" />' +
								'<input type="hidden" text="" id="latitude" value="" />' +
								'<input type="hidden" text="" id="longtitude" value="" />' +
								'<input type="hidden" text="" id="userId" value="" />' +
								'<input type="hidden" text="" id="startTime" value="" />' +
								'<input type="hidden" text="" id="endTime" value="" />' +
								'<input type="hidden" text="" id="description" value="" />' +
								'<input type="hidden" text="" id="connectCount" value="" />' +
								'<input type="hidden" text="" id="isMobile" value="" />' +
								'<input type="hidden" text="" id="authorName" value="" />' +
								'<input type="hidden" text="" id="timePeriod" value="" />' +  
							 	'<div class="video-author"><span class="author"></span><span class="viewers"></span></div></div>' +
							
							 '<div class="underline"></div>').clone();
			
			CloneObj.attr("style","");
		  	CloneObj.attr("id" , "sub" + i);
		  	CloneObj.find("img").attr("src",imgUrl);
		  	CloneObj.find(".video-title").find(".title-attr").html(tempArr1[2]);

		  	CloneObj.find("input#programId").attr("value",tempArr1[0]);
		  	CloneObj.find("input#channelId").attr("value",tempArr1[1]);
		  	CloneObj.find("input#title").attr("value",tempArr1[2]);
		  	CloneObj.find("input#imagePath").attr("value",tempArr1[3]);
		  	CloneObj.find("input#vodEnable").attr("value",tempArr1[4]);
		  	CloneObj.find("input#vodPath").attr("value",tempArr1[5]);
		  	CloneObj.find("input#vote").attr("value",tempArr1[6]);
		  	CloneObj.find("input#latitude").attr("value",tempArr1[7]);
		  	CloneObj.find("input#longtitude").attr("value",tempArr1[8]);
		  	CloneObj.find("input#userId").attr("value",tempArr1[9]);
		  	CloneObj.find("input#startTime").attr("value",tempArr1[10]);
		  	CloneObj.find("input#endTime").attr("value",tempArr1[11]);
		  	CloneObj.find("input#description").attr("value",tempArr1[12]);
		  	CloneObj.find("input#connectCount").attr("value",tempArr1[13]);
		  	CloneObj.find("input#isMobile").attr("value",tempArr1[14]);
		  	CloneObj.find("input#authorName").attr("value",tempArr1[15]);

		  	/* time period calculate */
		  	var start = CloneObj.find("input#startTime").val();
		  	var end = CloneObj.find("input#endTime").val();

		  	if ( start != "" && end != "")
		  	{
			  	start = start.split(" ");
			  	end = end.split(" ");
			  	var startData = start[1].split(":");
			    var endData = end[1].split(":");
			    
			    var diff = parseInt( (endData[0] * 60 * 60 ) + parseInt( endData[1] * 60 ) + parseInt( endData[2] ) 
					    		- parseInt( startData[0])*60*60 ) - parseInt( startData[1] *60 ) - parseInt( startData[2]);
			    var hours = Math.floor(diff / 60 / 60);
			    diff -= hours * 60 * 60;
			    var minutes = Math.floor(diff / 60);
			    var second = diff - minutes * 60;

				if ( hours > -1 && hours < 10 ) 
					hours = "0" + hours.toString();
				if ( minutes > -1 && minutes < 10 )
					minutes = "0" + minutes;
				if ( second > -1 && second < 10 )
					second = "0" + second.toString();
				
			    CloneObj.find(".video-title").find(".video-period").html(hours + ":" + minutes + ":" + second);  			// time period display
		  	}

			var author = CloneObj.find("input#authorName").val();
			var viewers = CloneObj.find("input#connectCount").val();
		  	CloneObj.find(".video-author").find(".author").html(author);
		  	CloneObj.find(".video-author").find(".viewers").html(viewers + "VIEWS");
		  	
		  	//CloneObj.find(".video-title").find(".video-period").html(hours + ":" + minutes + ":" + second);
		  	$("#video").append(CloneObj);

		  	if ( $("div#video").height() < parseInt($("div#video").css("max-height"), 10) ) {
		  		$("div#video").css("overflow-y" , "hidden");
		  	}
		  	else {
		  		$("div#video").css("overflow-y" , "scroll");
		  	}
		  	
		  	CloneObj.click(function (event){
			  	selectedVideo(this);			  	
		  	});

			var progId = CloneObj.find("input#programId").val();
			var lat = CloneObj.find("input#latitude").val();
		  	var lgt = CloneObj.find("input#longtitude").val();
			var title = CloneObj.find("input#title").val();
			var authorName = CloneObj.find("input#authorName").val();
			var vote = CloneObj.find("input#vote").val();
		  	
		  	var marker_id = 'et_marker_' + progId;
		  	var overlay = 'overlay_' + progId;
		  	
		  	temp[progId] = {pid:progId , title:title , author:authorName , rating:vote };
		  	
			var oIcon = "<?php echo get_template_directory_uri(); ?>/images/orange-marker-middle.png";
			var oAnimation = google.maps.Animation.DROP;
			
		  	$et_main_map.gmap3({	
				marker : {
					id : marker_id,
					latLng:[lat, lgt],
					events:{
						click:function(marker, event, context) {
							if ( marker.getAnimation() ) {
								marker.setAnimation(0);
							}	
							else {
								marker.setAnimation( google.maps.Animation.BOUNCE);
								alert(4);
								playOfSelectedCate(context.id);
							}
						},
						mouseover: function( marker, event, context ){
							var text_id = context.id.split("_")[2];
							var overlay = "overlay_" + text_id;
							var overlay_obj = $(this).gmap3({get:{id: overlay}});
							
							if(overlay_obj){
								overlay_obj.show();
								$( "#" + context.id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );
							}
							else {
								$(this).gmap3(
          						{			
									overlay:{
										id : overlay,
										latLng : [marker.position.d, marker.position.e],
										options:{
											content : '<div id="' + context.id + '" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>' + temp[text_id].title + '</h2> <div class="listing-info" ><p>' + temp[text_id].author +'</p></div> </div> <div class="location-rating"><span class="et-rating"><span style="width:' + (temp[text_id].rating * 17) + 'px;"></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
											offset:{
												y:-42,
												x:-120
											}
										},
										events:{
											click:function() {
												alert(7);
												/*if ( marker.getAnimation() ) {
													marker.setAnimation(0);
												}
												else {
													marker.setAnimation(1);
												}*/
											}
										}
										
									}
								})
								var overlay_obj = $(this).gmap3({get:{id: overlay}});
								setTimeout(function(){
									overlay_obj.show();	
								$( "#" + context.id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );	
								},500);
								
							}
						},
						mouseout: function( marker, event, context ){

							var text_id = context.id.split("_")[2];
							var overlay = "overlay_" + text_id;
							var overlay_obj = $(this).gmap3({get:{id: overlay}});
							//alert(overlay);
							
							if(overlay_obj){
								overlay_obj.hide();
								$( "#" + context.id).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
								$(this).css( { 'display' : 'none' } );
								});
							} else{
								$(this).gmap3(
          						{			
									overlay:{
										id : overlay,
										latLng : [marker.position.d, marker.position.e],
										options:{
											content : '<div id="' + context.id + '" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>' + title + '</h2> <div class="listing-info" ><p>' + authorName +'</p></div> </div> <div class="location-rating"><span class="et-rating"><span style="width:' + (vote * 17) + 'px;"></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
											offset:{
												y:-42,
												x:-60
											}
										}
										
									}
								})
								var overlay_obj = $(this).gmap3({get:{id: overlay}});
								overlay_obj.hide();
								$( "#" + context.id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
								$(this).css( { 'display' : 'none' } );
								});
							}
							
						}
					},
					options:{
						icon: oIcon,
						center: [lat,lgt],
						zoom: 4,
						animation: oAnimation
					}
				}
			}); 

	  	}
	  		  	
	}

	function playOfSelectedCate (progId) {
		var programId = progId.split("_")[2];
		for (var i=0 ; i<progInfo.length ; i++) {
			if ( progInfo[i].pid == programId ) {
				
				document.getElementById("video-list").style.display = "none";       
				var myNode = document.getElementById("video");
				while (myNode.firstChild) {
				    myNode.removeChild(myNode.firstChild);
				}
				
				VideoPlayer(progInfo[i].title ,progInfo[i].connect_count ,progInfo[i].pid ,progInfo[i].cid ,progInfo[i].user_id ,progInfo[i].isMobile ,progInfo[i].end_time ,progInfo[i].vod_path);
				break;
			}
		}
	}
	
	function selectedVideo (obj) {
		var selectedLat = $(obj).find("input#latitude").val();
		var selectedLon = $(obj).find("input#longtitude").val();
		 
		var oIcon = "<?php echo get_template_directory_uri(); ?>/images/blue-marker.png";
		var oAnimation = google.maps.Animation.BOUNCE;
		alert(5);
		var oCenter = [selectedLat , selectedLon];
		
	  	/*$et_main_map.gmap3({											// map marker animation => BOUNCE 
			marker : {
				//id : marker_id,
				latLng : [selectedLat, selectedLon],
				options: {
					icon : oIcon,
					animation: oAnimation,
					center: oCenter
				}
			}
		});*/

		
		var vodPath = $(obj).find("input#vodPath").val();
		
  		document.getElementById("video-list").style.display = "none";       
		var myNode = document.getElementById("video");
		while (myNode.firstChild) {
		    myNode.removeChild(myNode.firstChild);
		}


		var videoTitle = $(obj).find("input#title").val();
		var videoViewer = $(obj).find("input#connectCount").val();
		var programNum = $(obj).find("input#programId").val();
		var channelNum = $(obj).find("input#channelId").val();
		var userNum = $(obj).find("input#userId").val();
		var EndTime = $(obj).find("input#endTime").val();
		var bMobile = $(obj).find("input#isMobile").val();
		
		VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);
				
  	}																//  end of showing selected video 

  	
	function VideoPlayer ( videoTitle , videoViewer , programNum, channelNum , userNum ,bMobile , EndTime ,vodPath) {
		
		$('body').find('.et-map-slide').each(function(){                     // flash player all instance initialize
			$(this).removeClass( ' et-active-map-slide' );
			$(this).attr("style", "display:none; opacity: 0;");
		});
		
		var myNode = document.getElementById("et-slider-wrapper");
		while (myNode.firstChild) {
		    myNode.removeChild(myNode.firstChild);
		}
		$('#et-slider-wrapper').remove();
		
		var et_slider_wrapper = document.createElement("div");         
		
		et_slider_wrapper.className = "et-map-post ui-draggable";   // et-slider-wrapper
		et_slider_wrapper.id = "et-slider-wrapper";
		document.body.appendChild(et_slider_wrapper);
		
		var et_map_slides = document.createElement("div");
		
		et_map_slides.id = "et-map-slides";							// et-map-slides
		et_slider_wrapper.appendChild(et_map_slides);
	
		var et_map_slide = document.createElement("div");	
			
		et_map_slide.className = "et-map-slide";					// et-map-slide
		et_map_slide.className += " et-active-map-slide";
		et_map_slide.style.display="block";
		et_map_slide.style.opacity="1";
		et_map_slides.appendChild(et_map_slide);	   

		var objectDiv = document.createElement("div");
		
		objectDiv.className = "objectDiv";             				// flash player loading
		objectDiv.style.position = "fixed";
		objectDiv.style.width = "100%";

		objectDiv.style.height = "320px";
		objectDiv.style.bottom = 0 + "px";
		objectDiv.style.backgroundColor = "black";
		
		et_map_slide.appendChild(objectDiv);

		$('body').find(".video-info").css('display','block');
		$('body').find(".video-name").text("NAME OF VIDEO - " + videoTitle);
		$('body').find("#video-title-view").find("#hidden-video-title").attr('value',programNum);
		$('body').find(".visit-count").text(videoViewer + "  VIEWERS");
		$('body').find(".video-info").css('bottom','320px');
		
		var video_viewer_count = videoViewer;
		var programId = programNum;

		jQuery.ajax({
			type: "POST",
		  	url: "<?php echo get_template_directory_uri()?>/ajaxVideoViewerCount.php/",
		  	data: 'programId=' + programId,
		  	success: function(data) {
				//alert(data);  	
		  	},
		  	error: function (data) {
			  	alert('viewer counting error');
		  	}
		});
			
		var object = document.createElement("object");	                           //   flash player parameter setting
			
		object.type = "application/x-shockwave-flash";
		object.name = "StrobeMediaPlayback_" + (progInfo.length);
		object.data = "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback.swf";
		object.style.width = "480px";
		object.style.height = "320px";
		object.id = "video_player";
		object.style.visibility = "visible";
		objectDiv.appendChild(object);

		 var param = document.createElement("param");
		 
		 param.name = "allowFullScreen";                 
		 param.value = "true";
		 object.appendChild(param);

		 var param = document.createElement("param");
		 
		 param.name = "wmode";
		 param.value = "opaque";
		 object.appendChild(param);
		 
		var param = document.createElement("param");	
		param.name = "flashvars";
		

		if( EndTime == "" || EndTime == 0 )											//  check whether selected video is Mobile or not 
		{
			/*var pid = $(obj).find("input#programId").val();
			var cid = $(obj).find("input#channelId").val();
			var uid = $(obj).find("input#userId").val();*/
			
			if(  bMobile == "0")
			{
				param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream/" + channelNum + "_" + programNum + "_" + userNum + "&autoPlay=true&streamType=live&bgColor=#000000";
			}
			else
			{
				param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream/" + channelNum + "_" + programNum + "_" + userNum + "&autoPlay=true&streamType=live&bgColor=#000000";
			}	
		}
		else
		{
			param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/vod/mp4:" + vodPath + "&autoPlay=true&streamType=vod&bgColor=#000000";
		} 
		object.appendChild(param);
  	}
  	
  	<?php
  		$key="9dcde915a1a065fbaf14165f00fcc0461b8d0a6b43889614e8acdb8343e2cf15";
		
		$ipaddress = '';
		if ($_SERVER['HTTP_CLIENT_IP'])
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if($_SERVER['HTTP_X_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if($_SERVER['HTTP_X_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if($_SERVER['HTTP_FORWARDED_FOR'])
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if($_SERVER['HTTP_FORWARDED'])
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if($_SERVER['REMOTE_ADDR'])
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		
	  	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ipaddress&format=json";
	  	$cont = file_get_contents($url);
	  	$data = json_decode($cont , true);
	  	$user_latitude = $data["latitude"];
	  	$user_longitude = $data["longitude"];
	?>
	
	
	var currentLat = 0;
	var currentLng = 0;	 
	(function($){
		
		var oCenter = ['0', '0'];
	<?php if (sizeof($emap_items) > 0) {
		if ( ( isset($user_latitude) && $user_latitude != 0 ) && ( isset($user_longitude) && $user_longitude != 0) ) 
  			echo "oCenter=['".$user_latitude."', '".$user_longitude."'];";
  		else 
  			echo "oCenter=['".$emap_items[0]->lat."', '".$emap_items[0]->lgt."'];";
  	 }?>
	
	var loadFlag = 0;												
	var samePosMarker = new Array();
	var oIcon = "<?php echo get_template_directory_uri(); ?>/images/blue-marker.png";	
	
	var geocoder = new google.maps.Geocoder();
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
	    	currentLat = position.coords.latitude;
	    	currentLng = position.coords.longitude;	
	    	//alert(currentLat);
	    	//alert(currentLng);	  
	   
	     }, function() {
	  		//alert('error1'); 
	  		
	     });
	 }else {
		 //alert('error2');
	 }
	 
		
	$et_main_map.gmap3({
		map:{
			options:{
				zoom: 10,
				center: oCenter,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: true,
				mapTypeControlOptions: {
					position : google.maps.ControlPosition.RIGHT_TOP,
					style : google.maps.MapTypeControlStyle.DROPDOWN_MENU
				},
				navigationControl: true,
				scrollwheel: true,
				streetViewControl: true,
				zoomControl: true,
				zoomControlOptions: {
			        style: google.maps.ZoomControlStyle.LARGE
			    },
			    unitSystem: google.maps.UnitSystem.IMPERIAL
			}
		}
	});

	<?php
		$emap_info_list = array();
		$values_array = array();
		$overlay_array = array();
		foreach ($emap_items as $row => $values) {
			if($values->username != ""){
				$emap_info_list[ $row ] = array();
				$emap_info_list[ $row ]['lat'] = $values->lat;
				$emap_info_list[ $row ]['lng'] = $values->lgt;
				$emap_info_list[ $row ]['title'] = $values->title;
				$emap_info_list[ $row ]['vote'] = $values->vote;
				$emap_info_list[ $row ]['bPlayer'] = false;
				$emap_info_list[ $row ]['username'] = $values->username;
				$overlay_array[$row] = "overlay:{
													id : 'overlay_$row',
													latLng : [$values->lat, $values->lgt],
													options:{
														content : '<div id=\'et_marker_$row\' class=\'et_marker_info\'><div class=\'location-description\'> <div class=\'location-title\'> <h2>" . $values->title . "</h2> <div class=\'listing-info\' ><p>" . $values->username . "</p></div> </div> <div class=\'location-rating\'><span class=\'et-rating\'><span ></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
														offset:{
															y:-42,
															x:-122
														}
													}
													
												}";
				$values_array[$row] = "{
					id : 'et_marker_" . $row . "',
					latLng:[$values->lat,$values->lgt],
					events:{
						click:function(marker, event, context){
							marker.setAnimation(0);
							//marker.setAnimation( google.maps.Animation.BOUNCE);
							alert(2);
							marker.setIcon( '" . get_template_directory_uri() . "/images/orange-marker-middle.png' );
							playOfselectedVideo(context.id);
						},
						mouseover: function( marker, event, context ){
							var overlay = $(this).gmap3({get:{id: 'overlay_$row'}});
							if(overlay){
								overlay.show();
								$( '#et_marker_" . $row . "' ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );
							}
							else {
								$(this).gmap3(
          						{			
									overlay:{
										id : 'overlay_$row',
										latLng : [$values->lat, $values->lgt],
										options:{
											content : '<div id=\'et_marker_$row\' class=\'et_marker_info\'><div class=\'location-description\'> <div class=\'location-title\'> <h2>" . $values->title . "</h2> <div class=\'listing-info\' ><p>" . $values->username . "</p></div> </div> <div class=\'location-rating\'><span class=\'et-rating\'><span ></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
											offset:{
												y:-42,
												x:-60
											}
										}/*,
										events:{
											click:function(){
												alert(0);
											}
										}*/
										
									}
								})
								var overlay = $(this).gmap3({get:{id: 'overlay_$row'}});
								setTimeout(function(){
									overlay.show();	
								$( '#et_marker_" . $row . "' ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );	
								},100);
								
							}
						},
						mouseout: function( marker, event, context ){
						
							var overlay = $(this).gmap3({get:{id: 'overlay_$row'}});
							if(overlay){
								overlay.hide();
								$( '#et_marker_" . $row . "' ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
								$(this).css( { 'display' : 'none' } );
								});
							} else{
								$(this).gmap3(
          						{			
									overlay:{
										id : 'overlay_$row',
										latLng : [$values->lat, $values->lgt],
										options:{
											content : '<div id=\'et_marker_$row\' class=\'et_marker_info\'><div class=\'location-description\'> <div class=\'location-title\'> <h2>" . $values->title . "</h2> <div class=\'listing-info\' ><p>" . $values->username . "</p></div> </div> <div class=\'location-rating\'><span class=\'et-rating\'><span ></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
											offset:{
												y:-42,
												x:-60
											}
										}
										
									}
								})
								var overlay = $(this).gmap3({get:{id: 'overlay_$row'}});
								overlay.hide();
								$( '#et_marker_" . $row . "' ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
								$(this).css( { 'display' : 'none' } );
								});
							}
							
						}
					},
					options:{
						icon: '" . get_template_directory_uri() . "/images/orange-marker-middle.png'
					}
					
				}"; 
			}
		} 
	?>	
	function playOfselectedVideo (markerId) {
		var markId = parseInt(markerId.split("_")[2]);
		
		var videoTitle = progInfo[markId].title;
		var videoViewer = progInfo[markId].connect_count;
		var programNum = progInfo[markId].pid;
		var channelNum = progInfo[markId].cid;
		var userNum = progInfo[markId].userid;
		var EndTime = progInfo[markId].end_time;
		var bMobile = progInfo[markId].isMobile;
		var vodPath = progInfo[markId].vod_path;
		
		VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);
	}
	
	function et_add_marker( /*marker_lat, marker_lng, marker_description, nRating, bPlayer, username */){
 
		//var marker_id = 'et_marker_' + marker_order;
		var oIcon = "<?php echo get_template_directory_uri(); ?>/images/red-marker.png";
		var oAnimation = google.maps.Animation.DROP;

				
		$et_main_map.gmap3({
			marker : {
				/*id : marker_id,
				//latLng : [marker_lat, marker_lng],	    	    
				options: {
					icon : oIcon,
					animation: oAnimation,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					zoomControlOptions: {
				        style: google.maps.ZoomControlStyle.LARGE
				    }					 
				},*/
				values:[<?php echo implode(",", $values_array)?>],
				cluster:{
					radius:100,
					events:{
						mouseover:function(cluster, event, context){
							var d = cluster.main.getPosition().d;
							var e = cluster.main.getPosition().e;

							var overlay = $(this).gmap3({get:{id:"overlay_cluster"}});
							if(overlay){
								
								overlay.show();
							} else {
								$(this).gmap3(
		          						{		
		          							overlay:{
												id : 'overlay_cluster',
												latLng : [d, e],
												options:{
													content : "<div style='color: red; font-size:25px; font-weight: 700;'>" + context.data.markers.length + "</div>",
													offset:{
														y:-50,
														x:-10
													}
												}
												
											}
										})
										
								
									var overlay = $(this).gmap3({get:{id:"overlay_cluster"}});
									overlay.show();
												
							}
						},
						mouseout:function(cluster, event, context){
							var overlay = $(this).gmap3({get:{id:"overlay_cluster"}});
							if(overlay){
								overlay.hide();
								$(this).gmap3({
								    clear: {
								      id: "overlay_cluster"
								    }
								  });
							} else {
								$(this).gmap3(
		          						{					          							
											overlay:{
												id : 'overlay_cluster',
												latLng : [d, e],
												options:{
													content : "<div style='width:100px;padding:10px;background:rgb(162, 160, 162);border-radius:10px;text-align:center;'>Program List : " + context.data.markers.length + "</div>",
													offset:{
														y:-75,
														x:-55
													}
												}
											}
										})
										
								
									var overlay = $(this).gmap3({get:{id:"overlay_cluster"}});
									overlay.hide();
									$(this).gmap3({
									    clear: {
									      id: "overlay_cluster"
									    }
									});			
							}
						},
						click:function(cluster, event, context){

							var overlay = $(this).gmap3({get:{id:"overlay_cluster_list"}});
							if (overlay) {
								overlay.hide();
								$(this).gmap3({
								    clear: {
								      id: "overlay_cluster_list"
								    }
								});
							}

							var object = context.data.markers;
							var homeURL = "<?php echo home_url();?>";
							var imgURL = "";
							
							var clusterContent = "<div id='multiple' style='width:294px; height:auto;'><img id='clusterClose' src='<?php echo get_template_directory_uri()?>/images/close_cate.png' style='float:right; width:19px;height:19px; cursor:pointer; margin-top: 10px; margin-right: 9px;'></img><div style='width:100%; height:auto; background-color: #F5F9FF;border-top-right-radius:14px; border-top-left-radius:14px; text-align:center;line-height:40px; font-size: 18px; font-weight: 700;'>LIST OF VISEOS IN THE AREA</div>";
							clusterContent += "<div style='width:100%; height:9px; background-color: #C60D0D;'></div>";
							clusterContent += "<div style='width:100%; height:auto; overflow-x: hidden; max-height: 216px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px;'>";
							for(i=0 ; i<object.length ; i++) {
								var index = object[i].id.split("_")[2];
								if ( imgURL ) 
									imgURL = homeURL + progInfo[index].imagePath;
								else 
									imgURL = "<?php echo get_template_directory_uri()?>/images/myprofile.png";
								clusterContent += "<div id='selectVideo_" + index + "' class='selVideo' style='width:100%; height:53px; background-color: #F5F9FF; text-align:center; line-height: 27px; font-size: 14px; cursor: pointer;' >";
								clusterContent += "<img src='" + imgURL + "' style='width: 40px; height: 42px; float:left; margin: 5px 9px;'></img>";
								clusterContent += "<input type='hidden' id='indexProg' value='" + i + "' >";
								clusterContent += "<input type='hidden' id='indexMul' value='" + i + "' >";
								clusterContent += "<div style='display: inline-block; width: 79%; height:auto; line-height:12px;'><span id='Vtitle_" + index + "' style='float: left; margin-right: 5px; font-size: 18px; font-weight: 500; '>" + progInfo[index].title + "</span>";
								clusterContent += "<span id='Vtime_" + i + "' style='float: right; margin-right: 23px; font-size: 16px; font-weight: 500;'>" + progInfo[index].totalTime + "</span>" + "</div>";
								clusterContent += "<div style='width: 92%; height: auto; line-height: 12px;'><span id='author' style='float: left; font-size: 14px;'>" + progInfo[index].username + "</span>";
								clusterContent += "<span id='viewers' style='float: right;'>" + progInfo[index].connect_count + " VIEWS" + "</span></div></div>";
								clusterContent += "<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>";

							}
							
							clusterContent += "</div></div>";

	
							var d = cluster.main.getPosition().d;
							var e = cluster.main.getPosition().e;

							var overlay = $(this).gmap3({get:{id:"overlay_cluster_list"}});
							if(overlay) {
								
								overlay.show();
							} else {
								$(this).gmap3(
		          						{		
		          							overlay:{
												id : 'overlay_cluster_list',
												latLng : [d, e],
												options:{
													content : clusterContent,
													offset:{
														y:40,
														x:-145
													}
												},
												events:{														//  Cluster multiple overlay click
													click:function(overlay,event,context){
														if( event[0].target.id == "clusterClose" ){
															var myNode = document.getElementById("multiple");
															while (myNode.firstChild) {
															    myNode.removeChild(myNode.firstChild);
															}
															$("#multiple").remove();
															
														}
														if ( event[0].target.id != "" &&  event[0].target.id != "clusterClose" ) {
															var temp = event[0].target.id;
															var index = parseInt(temp.split('_')[1]);

															var videoTitle = progInfo[index].title;
															var videoViewer = progInfo[index].connect_count;
															var programNum = progInfo[index].pid;
															var channelNum = progInfo[index].cid;
															var userNum = progInfo[index].user_id;
															var EndTime = progInfo[index].end_time;
															var bMobile = progInfo[index].isMobile;
															var vodPath = progInfo[index].vod_path;
															//var authorName = progInfo[index].username;

															VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);

															var myNode = document.getElementById("multiple");
															while (myNode.firstChild) {
															    myNode.removeChild(myNode.firstChild);
															}
															$("#multiple").remove();
													
														}
													}
												}
											}
										})
										
									var overlay = $(this).gmap3({get:{id:"overlay_cluster_list"}});
									setTimeout(function(){
										overlay.show();	
										
									},300);
									
							}	

						}
					},
					0: {
				        content: "<div class='cluster cluster-1' style='color:black;z-index:10000;font-size:23px; cursor: pointer;'></div>",
				        width: 53,
				        height: 52,
				      },
			        5: {
			        	content: "<div class='cluster cluster-2' style='color:black;z-index:10000;font-size:23px; cursor: pointer;'></div>",
			        	width: 56,
			        	height: 57
			        },
			        10: {
			        	content: "<div class='cluster cluster-3' style='color:blue;z-index:10000;font-size:23px; cursor: pointer;'></div>",
			        	width: 66,
			        	height: 65
			        }
				}
		          	
			},
			events: {
				click:function(marker, event, context){
					marker.setAnimation( google.maps.Animation.BOUNCE);
					marker.setIcon( '" . get_template_directory_uri() . "/images/orange-marker-middle.png' );
					alert(1);
				}
			}

		});
		

	}

	<?php
	if (sizeof($emap_items) > 0 ) {
		//$latlng = array();
		$count;

		foreach ($emap_items as $row => $values) {
			
			
		}
		echo "et_add_marker()";

	} 
	?>

	 function reload_emap(){
		var $et_top_menu = $( 'ul.nav' ),
			$featured_slider = $('#et-slider-wrapper'),
			$et_listings_item = $('#et-listings li'),
			$comment_form = $('form#commentform'),
			$et_filter_form = $('#et-filter-map'),
			$et_list_view = $('#et-list-view'),
			$et_filter_listing_type,
			$et_filter_listing_location,
			$et_filter_listing_rating,
			$et_mobile_listings_item,
			et_filter_options_html = '';

		et_window_width = $(window).width();

		$et_top_menu.superfish({
			delay		: 500, 										// one second delay on mouseout
			animation	: { opacity : 'show', height : 'show' },	// fade-in and slide-down animation
			speed		: 'fast', 									// faster animation speed
			autoArrows	: true, 									// disable generation of arrow mark-up
			dropShadows	: false										// disable drop shadows
		});

		if ( $('ul.et_disable_top_tier').length ) $("ul.et_disable_top_tier > li > ul").prev('a').attr('href','#');

		$('#left-area').fitVids();

		$('.et-place-main-text').tinyscrollbar();

		$et_listings_item.find('.et-mobile-link').click( function( event ) {
			event.stopPropagation();
		} );

		$et_listings_item.click( function(){
			var $this_li = $(this);

			if ( $this_li.hasClass( 'et-active-listing' ) ) return false;

			$this_li.siblings( '.et-active-listing' ).removeClass( 'et-active-listing' );

			$this_li.addClass( 'et-active-listing' );

			google.maps.event.trigger( $("#et_main_map").gmap3({ get: { id: "et_marker_" + $this_li.index() } }), 'click' );
		} );

		if ( $('#et-list-view.et-normal-listings').length ){
			$('#et-list-view.et-normal-listings').append( '<a href="#" class="et-date">' + et_custom.toggle_text + '</a>' );

			$et_list_view = $('#et-list-view.et-normal-listings');

			$et_list_view.find( '.et-date' ).click( function() {

				if ( $et_list_view.hasClass( 'et-listview-open' ) )
					$et_list_view.removeClass( 'et-listview-open' );
				else
					$et_list_view.addClass( 'et-listview-open' );

				return false;
			} );
		}

		if ( $featured_slider.length ){
			$featured_slider.et_simple_slider( {
				slide : '.et-map-slide',
				use_controls : false,
				on_slide_changing : function( $next_slide ){
					google.maps.event.trigger($("#et_main_map").gmap3({ get: { id: "et_marker_" + $next_slide.index() } }), 'click');

					$et_listings_item.filter( '.et-active-listing' ).removeClass( 'et-active-listing' );
					$et_listings_item.eq( $next_slide.index() ).addClass( 'et-active-listing' );

					$et_mobile_listings_item.filter( '.et-active-listing' ).removeClass( 'et-active-listing' );
					$et_mobile_listings_item.eq( $next_slide.index() ).addClass( 'et-active-listing' );
					
				},
				on_slide_change_end : function( $next_slide ){
					if ( et_window_width >= 960 ) $('.et-place-main-text:visible').tinyscrollbar_update();

					// $next_slide.siblings().removeClass( 'et-active-map-slide' );
					$('body').find('.et-map-slide').each(function(){
						$(this).removeClass( 'et-active-map-slide' );
						$(this).attr("style", "display:none; opacity: 0;");
					});
					$next_slide.addClass( 'et-active-map-slide' );
					$next_slide.attr("style", "display:block; opacity: 1;");
				}
			} );

		}

		(function et_search_bar(){
			var $searchinput = $(".et-search-form .search_input"),
				searchvalue = $searchinput.val();

			$searchinput.focus(function(){
				if (jQuery(this).val() === searchvalue) jQuery(this).val("");
			}).blur(function(){
				if (jQuery(this).val() === "") jQuery(this).val(searchvalue);
			});
		})();

		$comment_form.find('input:text, textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_comment_label = $et_current_input.siblings('label'),
				et_comment_label_value = $et_current_input.siblings('label').text();
			if ( $et_comment_label.length ) {
				$et_comment_label.hide();
				if ( $et_current_input.siblings('span.required') ) {
					et_comment_label_value += $et_current_input.siblings('span.required').text();
					$et_current_input.siblings('span.required').hide();
				}
				$et_current_input.val(et_comment_label_value);
			}
		}).bind('focus',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === et_label_text) jQuery(this).val("");
		}).bind('blur',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === "") jQuery(this).val( et_label_text );
		});

		// remove placeholder text before form submission
		$comment_form.submit(function(){
			$comment_form.find('input:text, textarea').each(function(index,domEle){
				var $et_current_input = jQuery(domEle),
					$et_comment_label = $et_current_input.siblings('label'),
					et_comment_label_value = $et_current_input.siblings('label').text();

				if ( $et_comment_label.length && $et_comment_label.is(':hidden') ) {
					if ( $et_comment_label.text() == $et_current_input.val() )
						$et_current_input.val( '' );
				}
			});
		});

		et_duplicate_menu( $('ul.nav'), $('.mobile_nav'), 'mobile_menu', 'et_mobile_menu' );

		$('body').append( $('#et-list-view').clone().removeClass('et-normal-listings').addClass('et-mobile-listings') );
		$et_mobile_listings_item = $('.et-mobile-listings li');

		function et_duplicate_menu( menu, append_to, menu_id, menu_class ){
			var $cloned_nav;

			menu.clone().attr('id',menu_id).removeClass().attr('class',menu_class).appendTo( append_to );
			$cloned_nav = append_to.find('> ul');
			$cloned_nav.find('.menu_slide').remove();
			$cloned_nav.find('li:first').addClass('et_first_mobile_item');

			append_to.click( function(){
				if ( $(this).hasClass('closed') ){
					$(this).removeClass( 'closed' ).addClass( 'opened' );
					$cloned_nav.slideDown( 500 );
				} else {
					$(this).removeClass( 'opened' ).addClass( 'closed' );
					$cloned_nav.slideUp( 500 );
				}
				return false;
			} );

			append_to.find('a').click( function(event){
				event.stopPropagation();
			} );
		}

		if ( $et_filter_form.length ) {
			$et_filter_listing_type = $et_filter_form.find( '#et-listing-type' );
			$et_filter_listing_location = $et_filter_form.find( '#et-listing-location' );
			$et_filter_listing_rating = $et_filter_form.find( '#et-listing-rating' );

			$et_filter_listing_type.find( 'option' ).each( function() {
				var $this_option = $( this );

				et_filter_options_html += '<li data-value="' + $this_option.attr( 'value' ) + '">' + $this_option.text() + '</li>';
			} );

			$( 'a.listing-type' ).append( '<ul>' + et_filter_options_html + '</ul>' );

			et_filter_options_html = '';

			$et_filter_listing_location.find( 'option' ).each( function() {
				var $this_option = $( this );

				et_filter_options_html += '<li data-value="' + $this_option.attr( 'value' ) + '">' + $this_option.text() + '</li>';
			} );

			$( 'a.listing-location' ).append( '<ul>' + et_filter_options_html + '</ul>' );

			et_filter_options_html = '';

			for ( var i = 5; i >= 1; i-- ) {
				et_filter_options_html += '<li data-value="' + i + '">' + '<span class="et-rating"><span style="width: ' + ( 17 * i ) + 'px;"></span></span>' + '</li>';
			}

			$( 'a.listing-rating' ).append( '<ul>' + '<li data-value="none">' + $( 'a.listing-rating' ).text() + '</li>' + et_filter_options_html + '</ul>' );

			if ( $et_filter_listing_type.find( ':selected' ).length ) {
				$( 'a.listing-type .et_filter_text' ).text( $et_filter_listing_type.find( ':selected' ).text() );
			}

			if ( $et_filter_listing_location.find( ':selected' ).length ) {
				$( 'a.listing-location .et_filter_text' ).text( $et_filter_listing_location.find( ':selected' ).text() );
			}

			if ( $et_filter_listing_rating.find( ':selected' ).length ) {
				$( 'a.listing-rating .et_filter_text' ).html( $( 'a.listing-rating li[data-value=' + $et_filter_listing_rating.find( ':selected' ).val() + ']' ).html() );
			}

			$( 'a.filter-type' ).click( function() {
				var $this_element = $(this);

				if ( $this_element.hasClass( 'filter-type-open' ) ) return false;

				$this_element.addClass( 'filter-type-open' );

				$this_element.siblings( '.filter-type-open' ).each( function() {
					var $this_link = $(this);

					$this_link.removeClass( 'filter-type-open' ).find( 'ul' ).animate( { 'opacity' : 0 }, 500, function() {
						$(this).css( 'display', 'none' );
					} );
				} );

				$this_element.find( 'ul' ).css( { 'display' : 'block', 'opacity' : '0' } ).animate( { 'opacity' : 1 }, 500 );

				return false;
			} );

			$( 'a.filter-type li' ).click( function() {
				var $this_element = $(this),
					$parent_link = $this_element.closest('a'),
					$active_filter_option,
					filter_order;

				$parent_link.find( '.et_filter_text' ).html( $this_element.html() );
				filter_order = $parent_link.index('.filter-type');
				$active_filter_option = $et_filter_form.find( 'select' ).eq( filter_order );

				$active_filter_option.find( ':selected' ).removeAttr( 'selected' );
				$active_filter_option.find( 'option[value=' + $this_element.attr( 'data-value' ) + ']' ).attr("selected", "selected");

				$parent_link.removeClass( 'filter-type-open' ).find( 'ul' ).animate( { 'opacity' : 0 }, 500, function() {
					$(this).css( 'display', 'none' );
				} );

				return false;
			} );

			$( '.et_filter_arrow, .et_filter_text' ).click( function( event ) {
				var $this_element = $(this),
					$parent_link = $this_element.closest('a');

				if ( $parent_link.hasClass( 'filter-type-open' ) ) {
					$parent_link.find( 'ul' ).animate( { 'opacity' : 0 }, 500, function() {
						$(this).css( 'display', 'none' );
					} );

					$parent_link.removeClass( 'filter-type-open' );

					return false;
				}
			} );
		}
	 }
	
	window.setInterval(function() {
		
		var program_id;
		if( progInfo.length > 0 )
		{
			program_id = progInfo[ progInfo.length - 1 ].pid;
		}
		else
			program_id = "";
		
		if(window.XMLHttpRequest)
		{
			xmlhttp = new XMLHttpRequest();
		}
		else
		{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function()
		{
			//return;
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
			{
				
				if(xmlhttp.responseText == "")
					return;
				
				var emapinfo = xmlhttp.responseText.split("///");

				var progInfo_index = progInfo.length;						// original count remember.
				
				if( progInfo.length > 0 )
				{
					for ( var index=0 ; index < emapinfo.length-1 ; index++ ) {
						
						var pid = emapinfo[index].split("---")[0];
						var vote = emapinfo[index].split("---")[1];
						var cid = emapinfo[index].split("---")[2];
						var start_time = emapinfo[index].split("---")[3];
						var end_time = emapinfo[index].split("---")[4];
						var vod_path = emapinfo[index].split("---")[5];
						var vod_enable = emapinfo[index].split("---")[6];
						var user_id = emapinfo[index].split("---")[7];
						var username = emapinfo[index].split("---")[8];
						var facebook_name = emapinfo[index].split("---")[9];
						var twitter_name = emapinfo[index].split("---")[10];
						var lat = emapinfo[index].split("---")[11];
						var lgt = emapinfo[index].split("---")[12];
						var title = emapinfo[index].split("---")[13];
						var description = emapinfo[index].split("---")[14];
						var isMobile = emapinfo[index].split("---")[15];
						var connect_count = emapinfo[index].split("---")[16];
						var imagePath = emapinfo[index].split("---")[17];

						if ( index > progInfo.length - 1 ) {
							progInfo[progInfo.length] = { pid: pid,
							vote: vote,
							cid: cid,
							start_time: start_time,
							end_time: end_time,
							vod_path: vod_path,
							vod_enable: vod_enable,
							user_id: user_id,
							username: username,
							facebook_name: facebook_name,	
							twitter_name: twitter_name,
							lat: lat,
							lgt: lgt,
							title: title,
							description: description,
							isMobile: isMobile,
							connect_count: connect_count,
							imagePath: imagePath };

							var total_time = dateCal(start_time , end_time);
							progInfo[index] = { totalTime: total_time}; 
							console.log(progInfo);
							alert(emapinfo);
						}	
						else {
							if ( progInfo[index].pid != pid 
								|| progInfo[index].vote != vote 
								|| progInfo[index].cid != cid
								|| progInfo[index].start_time != start_time
								|| progInfo[index].end_time != end_time 
								|| progInfo[index].vod_path != vod_path
								|| progInfo[index].vod_enable != vod_enable
								|| progInfo[index].user_id != user_id
								|| progInfo[index].username != username
								|| progInfo[index].facebook_name != facebook_name
								|| progInfo[index].twitter_name != twitter_name
								|| progInfo[index].lat != lat
								|| progInfo[index].lgt != lgt
								|| progInfo[index].title != title
								|| progInfo[index].description != description
								|| progInfo[index].isMobile != isMobile
								|| progInfo[index].connect_count != connect_count)
							{
								progInfo[index].pid = pid;
								progInfo[index].vote = vote; 
								progInfo[index].cid = cid;
								progInfo[index].start_time = start_time;
								progInfo[index].end_time = end_time;
								progInfo[index].vod_path = vod_path;
								progInfo[index].vod_enable = vod_enable;
								progInfo[index].user_id = user_id;
								progInfo[index].username = username;
								progInfo[index].facebook_name = facebook_name;
								progInfo[index].twitter_name = twitter_name;
								progInfo[index].lat = lat;
								progInfo[index].lgt = lgt;
								progInfo[index].title = title;
								progInfo[index].description = description;
								progInfo[index].isMobile = isMobile;
								progInfo[index].connect_count = connect_count;
								progInfo[index].imagePath = imagePath;
	
								var total_time = dateCal(start_time , end_time);
								progInfo[index].totalTime = total_time;

								console.log(progInfo);
								alert(emapinfo);	
							}
						}
					}
					
				}
				

				var expProcessFlag = false;
				if(progInfo.length == 0)
				{
					expProcessFlag = true;
				}
				for(i = progInfo_index ; i < emapinfo.length -1 ; i++)
				{
					progInfo[ progInfo.length ] = {
							pid : emapinfo[i].split("---")[0], // pid;
							vote : emapinfo[i].split("---")[1], // vote;
							cid : emapinfo[i].split("---")[2], // cid;
							start_time : emapinfo[i].split("---")[3], // start_time;
							end_time : emapinfo[i].split("---")[4], // end_time;
							vod_path : emapinfo[i].split("---")[5], // vod_path;
							vod_enable : emapinfo[i].split("---")[6], // vod_enable;
							user_id : emapinfo[i].split("---")[7], // user_id;
							username : emapinfo[i].split("---")[8], // username;
							facebook_name : emapinfo[i].split("---")[9], // facebook_name;
							twitter_name : emapinfo[i].split("---")[10], // twitter_name;
							lat : emapinfo[i].split("---")[11], // lat;
							lgt : emapinfo[i].split("---")[12], // lgt;
							title : emapinfo[i].split("---")[13], // title;
							description : emapinfo[i].split("---")[14], // description;
							isMobile : emapinfo[i].split("---")[15], // isMobile;
							connect_count : emapinfo[i].split("---")[16], // connect_count;
							imagePath : emapinfo[i].split("---")[17] // image_path;

							
						}
						var total_time = dateCal(start_time , end_time);
						progInfo[index].totalTime = total_time;
					//reload_emap();    

						var oIcon = "<?php echo get_template_directory_uri(); ?>/images/black-marker-middle.png";
						var oAnimation = google.maps.Animation.BOUNCE;
						alert(3);
						var oCenter = [progInfo[progInfo_index].lat , progInfo[progInfo_index].lgt];
						var marker_id = 'et_marker_' + i;
						var overlay = 'overlay_' + i;
					
					    if ( !progInfo[progInfo_index].end_time ) {
						  	$et_main_map.gmap3({											// map marker animation => BOUNCE 
								marker : {
									id : marker_id,
									latLng : [progInfo[progInfo_index].lat, progInfo[progInfo_index].lgt],
									options: {
										icon : oIcon,
										animation: oAnimation,
										center: oCenter
									}
								}
							});
						}

						/* $et_main_map.gmap3({	
							marker : {
								id : marker_id,
								latLng:[progInfo[progInfo_index].lat, progInfo[progInfo_index].lgt],
								events:{
									click:function(marker, event, context) {
										if ( marker.getAnimation() ) {
											marker.setAnimation(0);
										}	
										else {
											marker.setAnimation( google.maps.Animation.BOUNCE);
											marker.setIcon( "<?php echo get_template_directory_uri() ?>/images/orange-marker-middle.png" );
											playOfSelectedCate(context.id);
										}
									},
									mouseover: function( marker, event, context ){
										var text_id = context.id.split("_")[2];
										var overlay = "overlay_" + text_id;
										var overlay_obj = $(this).gmap3({get:{id: overlay}});
										
										if(overlay_obj){
											overlay_obj.show();
											$( "#" + context.id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );
										}
										else {
											$(this).gmap3(
			          						{			
												overlay:{
													id : overlay,
													latLng : [marker.position.d, marker.position.e],
													options:{
														content : '<div id="' + context.id + '" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>' + temp[text_id].title + '</h2> <div class="listing-info" ><p>' + temp[text_id].author +'</p></div> </div> <div class="location-rating"><span class="et-rating"><span style="width:' + (temp[text_id].rating * 17) + 'px;"></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
														offset:{
															y:-42,
															x:-120
														}
													},
													events:{
														click:function() {
															//alert(0);
															if ( marker.getAnimation() ) {
																marker.setAnimation(0);
																//alert(2);
															}
															else {
																marker.setAnimation(1);
																//alert(3);
															}
														}
													}
													
												}
											})
											var overlay_obj = $(this).gmap3({get:{id: overlay}});
											setTimeout(function(){
												overlay_obj.show();	
											$( "#" + context.id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );	
											},500);
											
										}
									},
									mouseout: function( marker, event, context ){

										var text_id = context.id.split("_")[2];
										var overlay = "overlay_" + text_id;
										var overlay_obj = $(this).gmap3({get:{id: overlay}});
										//alert(overlay);
										
										if(overlay_obj){
											overlay_obj.hide();
											$( "#" + context.id).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
											$(this).css( { 'display' : 'none' } );
											});
										} else{
											$(this).gmap3(
			          						{			
												overlay:{
													id : overlay,
													latLng : [marker.position.d, marker.position.e],
													options:{
														content : '<div id="' + context.id + '" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>' + title + '</h2> <div class="listing-info" ><p>' + authorName +'</p></div> </div> <div class="location-rating"><span class="et-rating"><span style="width:' + (vote * 17) + 'px;"></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
														offset:{
															y:-42,
															x:-60
														}
													}
													
												}
											})
											var overlay_obj = $(this).gmap3({get:{id: overlay}});
											overlay_obj.hide();
											$( "#" + context.id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
											$(this).css( { 'display' : 'none' } );
											});
										}
										
									}
								},
								options:{
									icon: oIcon,
									center: [lat,lgt],
									zoom: 4,
									animation: oAnimation
								}
							}
						});*/
						

				}

			}
		}
		
		xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxemapshow.php?program_id=" + program_id,true);
		xmlhttp.send();

	}, 5000);
	
})(jQuery)

function onEmapClick(pid,cid,vodLive,obj)
{
	
	if(realChannel_ids == "")
		return;
	
	var startTime,endTime,currServerTime;
	var emapValues = document.getElementsByName("emapValues");
	
	<?php
	$i = 0;
	foreach ($emap_items as $row => $values) {
	?>
		endTime = "<?php echo $values->end_time?>";
		currServerTime = "<?php echo date("Y-m-d H:i:s")?>";
		
		if(emapValues[parseInt("<?php echo $i?>")] == obj)
		{
			
			if(endTime == "")
			{
				
				document.getElementById("liveVod").value = "live";
			}
			else 
			{
				
				document.getElementById("liveVod").value = "vod";
			}		
		}
		
	<?php
	$i++; 
	} 
	?>
	
	//var ch = document.getElementById(cid);
	//
	//var inputTag = ch.getElementsByTagName("input");
	//for(i = 0; i < inputTag.length; i++)
	//{
		//
	//}
	//var channelUrlStr = inputTag[0].value;
	var menuNames = document.getElementsByName("menuNames");
	//realChannel_ids
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
	
	//location.href = channelUrlStr + "?pid=" + pid + "&cid=" + cid;
}
function fn_dateConvert(dateStr)
{
	if ( dateStr == "") {
		return 0;
	}
	
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

function emap_pollingss()
{
	
	
}
	
</script>

<form name="aform" method="post" style="width:100%;height:100%;display:none;">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="liveVod" name="liveVod" value="" >
<input type="hidden" id="programId" name="programId" value="" >
<input type="hidden" id="programIdArr" name="programIdArr" value="">
</form>
<?php if (sizeof($emap_items) > 0) {
?>
<div class="video-info" >
		<div id="crossMulti" class="cross-multi" onclick="videoClose();"></div>
		<div class="cross-add" onclick="videoOpen();"></div>
		<div id="video-title-view" onclick="PlayerOnMap(this);">
			<div class="video-name" ></div>
			<input type="hidden" id="hidden-video-title" value="">
			<span class="visit-count"></span>
		</div>
</div>
 <div id="et-slider-wrapper" class="et-map-post ui-draggable">

<!-- <div id="et-map-slides">
	<?php
	//$i = 0; 
	foreach ($emap_items as $row => $values) {
		if ( $row == sizeof($emap_items)-1 )  {
?>
		<div class="et-map-slide <?php  echo 'et-active-map-slide'; ?>">
			
				<div class="objectDiv" style="height: 320px;background-color:black; text-align: center; position:fixed; bottom:0px;">
				
				<?php if($values->end_time != "" && ($values->vod_path == "" || $values->vod_path == "null")) {?>
				
							<div style="text-align:center; color:#ffffff;  vertical-align: middle;position: absolute;top: 160px;left: 43%;">Please,wait...</div>
							
				<?php } else if ( $value->end_time != "" && $value->start_time != "" && ( $value->vod_path != "" || $value->vod_path != 'null' )) { ?>
							<object type="application/x-shockwave-flash" name="StrobeMediaPlayback" data="<?php echo get_template_directory_uri()?>/StrobeMediaPlayback.swf" width="480" height="320" id="video_player" style="visibility: visible;">
						
								<param name="allowFullScreen" value="true">
								<param name="wmode" value="opaque">
								<param name="flashvars" value="<?php if ($values->end_time == "") {
											if($values->isMobile == "0")
												echo 'src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream/'.$values->cid.'_'.$values->pid.'_'.$values->userid.'&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000';
											else
												echo 'src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livemobile/'.$values->cid.'_'.$values->pid.'_'.$values->userid.'&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000';
										} else {
											
											echo 'src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/vod/mp4:'.get_vod_title($values->vod_path).'&amp;autoPlay=true&amp;streamType=vod&amp;bgColor=#ff0000';
										}?>">
							</object>
				<?php }?>
				
				</div>
		<?php
		//$i++; 
	}
}?>
	</div> 	
	</div>  -->
</div>
<script>
	
	function videoClose() {
		$('body').find('.et-map-slide').each(function() {
			//$(this).removeClass( ' et-active-map-slide' );
			$(this).attr("style", "display:none; opacity: 0;");
		});
		//$('body').find('.video-info').attr('style','bottom: 0px;');
		$('body').find('.video-info').attr('style','bottom: 0px; display: block; ');
		
	}
	
	function videoOpen() {
		$('body').find('.et-map-slide.et-active-map-slide').attr('style','display: block; opacity: 1;');
		$('body').find('.video-info').attr('style','bottom: 320px; display: block;');
	}
	
	function PlayerOnMap (obj) {
		var title = $(obj).find("input#hidden-video-title").val()
		for (var i=0 ; i<progInfo.length ; i++) {
			if ( title == progInfo[i].pid ) {
				
				paramids = progInfo[ i ].pid + "," + progInfo[ i ].cid + "," + progInfo[ i ].vod_enable + ":";
				
				document.getElementById("programIdArr").value = paramids.substring(0,paramids.length - 1);
				document.aform.action = "<?php echo home_url()?>/program_list";
				document.aform.submit();
				return;	
			}
		}
	}
	
</script>
<?php } ?>

<?php get_footer();?>

