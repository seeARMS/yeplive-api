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

<div style="width: 100%; height: 100%; min-width: 1260px; margin:auto auto; position: fixed; top: 0px;"><div id="et_main_map" style="position: static !important;"></div></div>

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
		
		if ( title == 39 )
			return;
			
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

		//$et_main_map.gmap3({ clear: {} })               // When category click , gmap2 initialize
		var temp = new Array();
		
	  	for(i = 0; i < tempArr.length; i++)
	  	{
		  	tempArr1 = tempArr[i].split(",,");
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
								'<input type="hidden" text="" id="facebookauthor" value="" />' +
								'<input type="hidden" text="" id="twitterautorh" value="" />' +
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
		  	CloneObj.find("input#facebookauthor").attr("value",tempArr1[16]);
		  	CloneObj.find("input#twitterauthor").attr("value",tempArr1[17]);

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
			var facebook_author = CloneObj.find("input#facebookauthor").val();
			var twitter_author = CloneObj.find("input#twitterauthor").val();
			var viewers = CloneObj.find("input#connectCount").val();
			if ( author != "" || author == undefined )
		  		CloneObj.find(".video-author").find(".author").html(author);
			if	( facebook_author != "" || facebook_author == undefined)
				CloneObj.find(".video-author").find(".author").html(facebook_author);
			if	( twitter_author != "" || twitter_author == undefined)
				CloneObj.find(".video-author").find(".author").html(twitter_author);
			
		  	CloneObj.find(".video-author").find(".viewers").html(viewers + "VIEWS");
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

/*			var progId = CloneObj.find("input#programId").val();
			var lat = CloneObj.find("input#latitude").val();
		  	var lgt = CloneObj.find("input#longtitude").val();
			var title = CloneObj.find("input#title").val();
			var authorName = CloneObj.find("input#authorName").val();
			var vote = CloneObj.find("input#vote").val();
		  	
		  	var marker_id = 'et_marker_' + progId;
		  	var overlay = 'overlay_' + progId;
		  	
		  	temp[progId] = {pid:progId , title:title , author:authorName , rating:vote };
		  	
			var oIcon = "<?php echo get_template_directory_uri(); ?>/images/black-marker-middle.png";
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
			}); */

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
		
/*		var selectedLat = $(obj).find("input#latitude").val();
		var selectedLon = $(obj).find("input#longtitude").val();
		 
		var oIcon = "<?php echo get_template_directory_uri(); ?>/images/blue-marker.png";
		var oAnimation = google.maps.Animation.BOUNCE;
		alert(5);
		var oCenter = [selectedLat , selectedLon];
		
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
		}); */
		
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
		if ( myNode != null) {
			while (myNode.firstChild) {
				myNode.removeChild(myNode.firstChild);
			}
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
		$('body').find(".video-name").text(videoTitle);
		$('body').find("#video-title-view").find("#hidden-video-title").attr('value',programNum);
		$('body').find(".visit-count").text(videoViewer + "  VIEWERS");
		$('body').find(".video-info").css('bottom','320px');
		$('body').find(".cross-add").css('display','none');
		$('body').find(".cross-multi").css('display','block');
		
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
		object.data = "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf";
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
		 
		 param.name = "controlBarMode";
		 param.value = "floating";
		 object.appendChild(param);
		 
		 param.name = "initialBufferTime";
		 param.value = 0.5;
		 object.appendChild(param);
		 
		 param.name = "bufferTime";
		 param.value = 2;
		 object.appendChild(param);
		 
		 param.name = "liveBufferTime";
		 param.value = 2;
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
				param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livemobile/" + channelNum + "_" + programNum + "_" + userNum + "&autoPlay=true&streamType=live&bgColor=#000000";
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
	
	
	var currentLat;
	var currentLng;	 
	
(function($){
		
		var oCenter = ['0', '0'];
	<?php 
		if ( ( isset($user_latitude) && $user_latitude != 0 ) && ( isset($user_longitude) && $user_longitude != 0) ) 
  			echo "oCenter=['".$user_latitude."', '".$user_longitude."'];";
  		else 
  			echo "oCenter=['".$emap_items[0]->lat."', '".$emap_items[0]->lgt."'];";
  	?>
	
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
				zoom: 7,
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

	var firstPage = 0;
	
	<?php
		$emap_info_list = array();
		$values_array = array();
		$overlay_array = array();
		foreach ($emap_items as $row => $values) {
			if($values->username != "" || $values->facebook_name != "" || $values->twitter_name != "" ) {
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
							//marker.setAnimation(0);
							//marker.setAnimation( google.maps.Animation.BOUNCE);
							//marker.setIcon( '" . get_template_directory_uri() . "/images/black-marker-middle.png' );
							if ( progInfo.length > 0 ) {
								if ( firstPage == 0 ) {
									firstPage = 1;
								}
								else 
									playOfselectedVideo(context.id);
							}
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
												x:-123
											}
										}
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
						icon: '" . get_template_directory_uri() . "/images/red-marker-small.png'
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
		var userNum = progInfo[markId].user_id;
		var EndTime = progInfo[markId].end_time;
		var bMobile = progInfo[markId].isMobile;
		var vodPath = progInfo[markId].vod_path;
		
		VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);
	}

	
	function et_add_marker( /*marker_lat, marker_lng, marker_description, nRating, bPlayer, username */){
		//et_add_marker_update();
		//var marker_id = 'et_marker_' + marker_order;
		var oIcon = "<?php echo get_template_directory_uri(); ?>/images/red-marker.png";
		var oAnimation = google.maps.Animation.DROP;
				
		$et_main_map.gmap3({
			marker : {
				values:[<?php echo implode(",", $values_array)?>],
				cluster:{
					radius:100,
					events:{
						mouseover:function(cluster, event, context){
							var d = cluster.main.getPosition().k;
							var e = cluster.main.getPosition().A;

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
							
							var clusterContent = "<div id='multiple' style='width:294px; height:auto;'><img id='clusterClose' src='<?php echo get_template_directory_uri()?>/images/close_cate.png' style='float:right; width:19px;height:19px; cursor:pointer; margin-top: 10px; margin-right: 9px;'></img><div style='width:100%; height:auto; background-color: #F5F9FF;border-top-right-radius:14px; border-top-left-radius:14px; text-align:center;line-height:40px; font-size: 18px; font-weight: 700;'>LIST OF VIDEOS IN THE AREA</div>";
							clusterContent += "<div style='width:100%; height:9px; background-color: #C60D0D;'></div>";
							clusterContent += "<div style='width:100%; height:auto; overflow-x: hidden; max-height: 216px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px;'>";
							for(i=0 ; i<object.length ; i++) {
								
								var author = "";
								var imgURL = "";
								 
								var index = object[i].id.split("_")[2];

								//if ( progInfo[index].pid != "" && progInfo[index].cid != "" && progInfo[index].start_time != "" ) {
									
									if ( progInfo[index].username != "" || progInfo[index].username == undefined )
										author = progInfo[index].username;
									if ( progInfo[index].facebook_name != "" || progInfo[index].facebook_name == undefined )
										author = progInfo[index].facebook_name;
									if ( progInfo[index].twitter_name != "" || progInfo[index].twitter_name == undefined )
										author = progInfo[index].twitter_name;
									 
									if ( progInfo[index].imagePath ) 
										imgURL = homeURL + progInfo[index].imagePath;
									else 
										imgURL = "<?php echo get_template_directory_uri()?>/images/myprofile.png";
									clusterContent += "<div id='selectVideo_" + index + "' class='selVideo'>";
									//clusterContent += "<div class='live-state'>LIVE</div>";
									clusterContent += "<img src='" + imgURL + "' style='width: 40px; height: 42px; float:left; margin: 5px 9px;'></img>";
									clusterContent += "<input type='hidden' id='indexProg' value='" + i + "' >";
									clusterContent += "<input type='hidden' id='indexMul' value='" + i + "' >";
									clusterContent += "<div style='display: inline-block; width: 79%; height:auto;'><span id='Vtitle_" + index + "' class='category-video-name' >" + progInfo[index].title + "</span>";
									clusterContent += "<span id='Vtime_" + i + "' class='category-video-time' >" + progInfo[index].totalTime + "</span>" + "</div>";
									clusterContent += "<div style='width: 92%; height: auto; line-height: 12px;'><span id='author_" + i + "' class='map-author-name'>" + author + "</span>";
									clusterContent += "<span id='viewers_" + i + "' class='map-viewer'>" + progInfo[index].connect_count + " VIEWS" + "</span></div></div>";
									clusterContent += "<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>";

									if ( progInfo[index].vod_path == "" && progInfo[index].start_time == "" ) {
										
									}
								// }

							}
							
							clusterContent += "</div></div>";

	
							var d = cluster.main.getPosition().k;
							var e = cluster.main.getPosition().A;

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
					//marker.setAnimation( google.maps.Animation.BOUNCE);
					//marker.setIcon( '" . get_template_directory_uri() . "/images/black-marker-middle.png' );
				}
			}

		});
	}

	<?php
	if (sizeof($emap_items) > 0 ) {
		echo "et_add_marker()";
	} 
	?>

	
	 function et_add_marker_update() {

		 $et_main_map.gmap3({ clear: {} }); 
		 	var tempArr = new Array();
		 	
		 	for ( var i = 0 ; i < progInfo.length ; i++) {
			 	
		 		tempArr[ tempArr.length ] = {
				 		id : 'et_marker_' + i,
				 		latLng : [progInfo[i].lat, progInfo[i].lgt],
				 		data: [progInfo[i].title , progInfo[i].username],
				 		events:{
							click:function(marker, event, context){
								//marker.setAnimation(0);
								//marker.setAnimation( google.maps.Animation.BOUNCE);
								//marker.setIcon( "<?php echo get_template_directory_uri()?>/images/black-marker-middle.png" );
								playOfselectedVideo(context.id);
							},
							mouseover: function( marker, event, context ){
								var tmp_ind = context.id.split("_")[2];
								var overlay = $(this).gmap3({get:{id: "overlay_" + tmp_ind}});
								if(overlay){
									overlay.show();
									$( "#et_marker_" + tmp_ind ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );
								}
								else {
									$(this).gmap3(
	          						{			
										overlay:{
											id : "overlay_" + tmp_ind,
											latLng : [marker.getPosition().k, marker.getPosition().A],
											options:{
												content : "<div id='et_marker_" + tmp_ind + "' class='et_marker_info'><div class='location-description'> <div class='location-title'> <h2>" + context.data[0] + "</h2> <div class='listing-info' ><p>" + context.data[1] + "</p></div> </div> <div class='location-rating'><span class='et-rating'><span ></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->",
												offset:{
													y:-42,
													x:-123	
												}
											}
											
										}
									})
									var overlay = $(this).gmap3({get:{id: "overlay_" + tmp_ind}});
									setTimeout(function(){
										overlay.show();	
									$( "#et_marker_" + tmp_ind ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 0 );	
									},100);
									
								}
							},
							mouseout: function( marker, event, context ){
								var tmp_ind = context.id.split("_")[2];
								var overlay = $(this).gmap3({get:{id: "overlay_" + tmp_ind}});
								if(overlay){
									overlay.hide();
									$( "#et_marker_" + tmp_ind ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
									$(this).css( { 'display' : 'none' } );
									});
								} else{
									$(this).gmap3(
	          						{			
										overlay:{
											id : "overlay_" + tmp_ind,
											latLng : [marker.getPosition().k, marker.getPosition().A],
											options:{
												content : "<div id='et_marker_" + tmp_ind + "' class='et_marker_info'><div class='location-description'> <div class='location-title'> <h2>" + context.data[0] + "</h2> <div class='listing-info' ><p>" + context.data[1] + "</p></div> </div> <div class='location-rating'><span class='et-rating'><span ></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->",
												offset:{
													y:-42,
													x:-60
												}
											}
											
										}
									})
									var overlay = $(this).gmap3({get:{id:"overlay_" + tmp_ind}});
									overlay.hide();
									$( "#et_marker_" + tmp_ind  ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 0, function() {
									$(this).css( { 'display' : 'none' } );
									});
								}
								
							}
						},
						options:{
							icon: "<?php echo get_template_directory_uri()?>/images/red-marker-small.png"
						}
			 	};	
		 	}		 	

			$et_main_map.gmap3({
				marker : {
					values:tempArr,
					cluster:{
						radius:100,
						events:{
							mouseover:function(cluster, event, context){
								var d = cluster.main.getPosition().k;
								var e = cluster.main.getPosition().A;

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
								var d = cluster.main.getPosition().k;
								var e = cluster.main.getPosition().A;
								
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
								
								var clusterContent = "<div id='multiple' style='width:294px; height:auto;'><img id='clusterClose' src='<?php echo get_template_directory_uri()?>/images/close_cate.png' style='float:right; width:19px;height:19px; cursor:pointer; margin-top: 10px; margin-right: 9px;'></img><div style='width:100%; height:auto; background-color: #F5F9FF;border-top-right-radius:14px; border-top-left-radius:14px; text-align:center;line-height:40px; font-size: 18px; font-weight: 700;'>LIST OF VIDEOS IN THE AREA</div>";
								clusterContent += "<div style='width:100%; height:9px; background-color: #C60D0D;'></div>";
								clusterContent += "<div style='width:100%; height:auto; overflow-x: hidden; max-height: 216px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px;'>";
								
								for(i=0 ; i<object.length ; i++) {

									var author = "";
									var imgURL = "";
									 
									var index = object[i].id.split("_")[2];

									//if ( progInfo[index].pid != "" && progInfo[index].cid != "" && progInfo[index].start_time != "" ) {
										
										if ( progInfo[index].username != "" || progInfo[index].username == undefined )
											author = progInfo[index].username;
										if ( progInfo[index].facebook_name != "" || progInfo[index].facebook_name == undefined )
											author = progInfo[index].facebook_name;
										if ( progInfo[index].twitter_name != "" || progInfo[index].twitter_name == undefined )
											author = progInfo[index].twitter_name;
										 
										if ( progInfo[index].imagePath ) 
											imgURL = homeURL + progInfo[index].imagePath;
										else 
											imgURL = "<?php echo get_template_directory_uri()?>/images/myprofile.png";
										clusterContent += "<div id='selectVideo_" + index + "' class='selVideo'>";
										//clusterContent += "<div class='live-state'></div>";
										clusterContent += "<img src='" + imgURL + "' style='width: 40px; height: 42px; float:left; margin: 5px 9px;'></img>";
										clusterContent += "<input type='hidden' id='indexProg' value='" + i + "' >";
										clusterContent += "<input type='hidden' id='indexMul' value='" + i + "' >";
										clusterContent += "<div style='display: inline-block; width: 79%; height:auto;'><span id='Vtitle_" + index + "' class='category-video-name' >" + progInfo[index].title + "</span>";
										clusterContent += "<span id='Vtime_" + i + "' class='category-video-time' >" + progInfo[index].totalTime + "</span>" + "</div>";
										clusterContent += "<div style='width: 92%; height: auto; line-height: 12px;'><span id='author_" + i + "' class='map-author-name'>" + author + "</span>";
										clusterContent += "<span id='viewers_" + i + "' class='map-viewer'>" + progInfo[index].connect_count + " VIEWS" + "</span></div></div>";
										clusterContent += "<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>";
									//}
								}
								
								clusterContent += "</div></div>";

		
								var d = cluster.main.getPosition().k;
								var e = cluster.main.getPosition().A;

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
						//marker.setAnimation( google.maps.Animation.BOUNCE);
						//marker.setIcon( "<?php get_template_directory_uri() ?>/images/black-marker-middle.png" );
					}
				},
				options:{
					icon: "<?php get_template_directory_uri()?>/images/red-marker-small.png"
				}

			});
			
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
				if ( emapinfo.length == 1 )
					return;
				var progInfo_index = progInfo.length;						// original count remember.
				
				if( progInfo.length == emapinfo.length - 1 )
				{
					var pid = emapinfo[emapinfo.length - 2].split("---")[0];
					var vote = emapinfo[emapinfo.length - 2].split("---")[1];
					var cid = emapinfo[emapinfo.length - 2].split("---")[2];
					var start_time = emapinfo[emapinfo.length - 2].split("---")[3];
					var end_time = emapinfo[emapinfo.length - 2].split("---")[4];
					var vod_path = emapinfo[emapinfo.length - 2].split("---")[5];
					var vod_enable = emapinfo[emapinfo.length - 2].split("---")[6];
					var user_id = emapinfo[emapinfo.length - 2].split("---")[7];
					var username = emapinfo[emapinfo.length - 2].split("---")[8];
					var facebook_name = emapinfo[emapinfo.length - 2].split("---")[9];
					var twitter_name = emapinfo[emapinfo.length - 2].split("---")[10];
					var lat = emapinfo[emapinfo.length - 2].split("---")[11];
					var lgt = emapinfo[emapinfo.length - 2].split("---")[12];
					var title = emapinfo[emapinfo.length - 2].split("---")[13];
					var description = emapinfo[emapinfo.length - 2].split("---")[14];
					var isMobile = emapinfo[emapinfo.length - 2].split("---")[15];
					var connect_count = emapinfo[emapinfo.length - 2].split("---")[16];
					var imagePath = emapinfo[emapinfo.length - 2].split("---")[17];

					if ( progInfo[progInfo.length-1].pid != pid 
							|| progInfo[progInfo.length-1].vote != vote 
							|| progInfo[progInfo.length-1].cid != cid
							|| progInfo[progInfo.length-1].start_time != start_time
							|| progInfo[progInfo.length-1].end_time != end_time 
							|| progInfo[progInfo.length-1].vod_path != vod_path
							|| progInfo[progInfo.length-1].vod_enable != vod_enable
							|| progInfo[progInfo.length-1].user_id != user_id
							|| progInfo[progInfo.length-1].username != username
							|| progInfo[progInfo.length-1].facebook_name != facebook_name
							|| progInfo[progInfo.length-1].twitter_name != twitter_name
							|| progInfo[progInfo.length-1].lat != lat
							|| progInfo[progInfo.length-1].lgt != lgt
							|| progInfo[progInfo.length-1].title != title
							|| progInfo[progInfo.length-1].description != description
							|| progInfo[progInfo.length-1].isMobile != isMobile
							|| progInfo[progInfo.length-1].connect_count != connect_count)
					{
						progInfo[progInfo.length-1].pid = pid;
						progInfo[progInfo.length-1].vote = vote; 
						progInfo[progInfo.length-1].cid = cid;
						progInfo[progInfo.length-1].start_time = start_time;
						progInfo[progInfo.length-1].end_time = end_time;
						progInfo[progInfo.length-1].vod_path = vod_path;
						progInfo[progInfo.length-1].vod_enable = vod_enable;
						progInfo[progInfo.length-1].user_id = user_id;
						progInfo[progInfo.length-1].username = username;
						progInfo[progInfo.length-1].facebook_name = facebook_name;
						progInfo[progInfo.length-1].twitter_name = twitter_name;
						progInfo[progInfo.length-1].lat = lat;
						progInfo[progInfo.length-1].lgt = lgt;
						progInfo[progInfo.length-1].title = title;
						progInfo[progInfo.length-1].description = description;
						progInfo[progInfo.length-1].isMobile = isMobile;
						progInfo[progInfo.length-1].connect_count = connect_count;
						progInfo[progInfo.length-1].imagePath = imagePath;

						var total_time = dateCal(start_time , end_time);
						progInfo[progInfo.length-1].totalTime = total_time;

						et_add_marker_update();
					}
				}
				else if ( progInfo.length < emapinfo.length - 1 ) {
					temp = new Array();
					for ( var index = progInfo.length; index < emapinfo.length-1 ; index++ ) {
						
						progInfo[index] = { 
							pid: emapinfo[index].split("---")[0],
							vote: emapinfo[index].split("---")[1],
							cid: emapinfo[index].split("---")[2],
							start_time: emapinfo[index].split("---")[3],
							end_time: emapinfo[index].split("---")[4],
							vod_path: emapinfo[index].split("---")[5],
							vod_enable: emapinfo[index].split("---")[6],
							user_id: emapinfo[index].split("---")[7],
							username: emapinfo[index].split("---")[8],
							facebook_name: emapinfo[index].split("---")[9],	
							twitter_name: emapinfo[index].split("---")[10],
							lat: emapinfo[index].split("---")[11],
							lgt: emapinfo[index].split("---")[12],
							title: emapinfo[index].split("---")[13],
							description: emapinfo[index].split("---")[14],
							isMobile: emapinfo[index].split("---")[15],
							connect_count: emapinfo[index].split("---")[16],
							imagePath: emapinfo[index].split("---")[17] 
						};

						if ( ( progInfo[index].start_time ) && ( progInfo[index].end_time ) ) {
							var total_time = dateCal(progInfo[index].start_time , progInfo[index].end_time);
							progInfo[index] = { totalTime: total_time};
						} 
						
						// map reloading
						
						et_add_marker_update();
						
					}
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
	<?php if (sizeof($emap_items) > 0) {
	//$i = 0; 
	foreach ($emap_items as $row => $values) {
		if ( $row == sizeof($emap_items)-1 )  {
?>
		<div class="et-map-slide <?php  echo 'et-active-map-slide'; ?>">
			
				<div class="objectDiv" style="height: 320px;background-color:black; text-align: center; position:fixed; bottom:0px;">
				
				<?php if($values->end_time != "" && ($values->vod_path == "" || $values->vod_path == "null")) {?>
				
							<div style="text-align:center; color:#ffffff;  vertical-align: middle;position: absolute;top: 160px;left: 43%;">Please,wait...</div>
							
				<?php } else if ( $value->end_time != "" && $value->start_time != "" && ( $value->vod_path != "" || $value->vod_path != 'null' )) { ?>
							<object type="application/x-shockwave-flash" name="StrobeMediaPlayback" data="<?php echo get_template_directory_uri()?>/StrobeMediaPlayback1.swf" width="480" height="320" id="video_player" style="visibility: visible;">
						
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
		$('body').find('.cross-multi').attr('style','display:none');
		$('body').find('.cross-add').attr('style','display:block');
		
	}
	
	function videoOpen() {
		$('body').find('.et-map-slide.et-active-map-slide').attr('style','display: block; opacity: 1;');
		$('body').find('.video-info').attr('style','bottom: 320px; display: block;');
		$('body').find('.cross-multi').attr('style','display: block;');
		$('body').find('.cross-add').attr('style','display: none;');
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

