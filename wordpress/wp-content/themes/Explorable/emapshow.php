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


    if (isset($_GET['programId']))
    {
        $get_program_id = $_GET['programId'];
        $get_program_id =  str_replace("/", "", $get_program_id);
    }



    if (isset($_GET['user']))
    {
        $get_user = $_GET['user'];

    }

    if (isset($_POST['programId']))
    {
        $post_program_id = $_POST['programId'];
    }


    if (isset($_POST['programerName']))
    {
        $post_user = $_POST['programerName'];

    }
	
	
	$emap_items = et_get_emap_items();


	$serverCurrGmtTime = getdate(); // server gmt time
	$serverCurrLocalTime = time() * 1000; // server current time
	
	$currentTime = date('Y-m-d H:i:s');
	
	$beforeDate = time() - (1 * 24 * 60 * 60);
	$beforeTime = date('Y-m-d H:i:s', $beforeDate);


    $userpic = get_template_directory_uri() . "/images/profile-thumb.png";
    $username = "";

    if (isset($_SESSION['user_id']))
    {
        include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
        include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
        include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
        include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
        include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

        $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
        $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
        if($link)
        {
            if(mysql_select_db(DB_NAME,$link))
            {
                $sql_query = "SELECT picture_path, facebook_name, facebook_id, twitter_name, twitter_img, (SELECT display_name FROM wp_users WHERE id in (SELECT wp_user_id FROM wp_user_yep WHERE user_Id = " . $_SESSION['user_id'] . ")) as username FROM wp_user_yep WHERE user_id =" . $_SESSION['user_id'];

                $result = mysql_query($sql_query);
                if(!result)
                {
                    echo "error";
                }

                $row = mysql_fetch_array($result);

                if ($row['facebook_name'] != "")
                {
                    $username = $row['facebook_name'];
                    $userpic = "https://graph.facebook.com/" . $row['facebook_id'] . "/picture/?type=large";
                }
                else if ($row['twitter_name'] != "")
                {
                    $username = $row['twitter_name'];
                    $userpic = $row['twitter_img'];
                }
                if ($row['username'] != "")
                    $username = $row['username'];

                if ($row['picture_path'] != "")
                    $userpic = $row['picture_path'];

            }
        }
    }
?>

<form name="aform" method="post" style="width:100%;height:100%;display:none;">
    <input type="hidden" id="hidMenus" name="hidMenus" value="">
    <input type="hidden" id="hidFlags" name="hidFlags" value="">
    <input type="hidden" id="hidChannelId" name="hidChannelId" value="">
    <input type="hidden" id="channelIds" name="channelIds" value="">
    <input type="hidden" id="liveVod" name="liveVod" value="" >
    <input type="hidden" id="programId" name="programId" value="" >
    <input type="hidden" id="programIdArr" name="programIdArr" value="">
    <input type="hidden" id="programerID" name="programerID" value="">
    <input type="hidden" id="programerName" name="programerName" value="">
    <input type="hidden" id="micNo" name="micNo" value="0">
    <input type="hidden" id="camNo" name="camNo" value="0">
</form>


<script>
    document.getElementById("main-header").style.display = "block";
    document.getElementById("headerShadowDiv").style.display = "block";

<!--    if ('--><?php //echo $get_program_id?><!--' != "")-->
<!--    {-->
<!--        document.getElementById('programId').value = '--><?php //echo $get_program_id?><!--';-->
<!--        document.aform.action = "--><?php //echo home_url()?><!--".split("/wordpress")[0];-->
<!--        document.aform.submit();-->
<!--    }-->
<!---->
<!--    if ('--><?php //echo $get_user?><!--' != "")-->
<!--    {-->
<!--        document.getElementById('programerName').value = '--><?php //echo $get_user?><!--';-->
<!--        document.aform.action = "--><?php //echo home_url()?><!--".split("/wordpress")[0];-->
<!--        document.aform.submit();-->
<!--    }-->


    if (document.getElementById("myProfileImage") != null)
    {
        document.getElementById("myProfileImage").src = "<?php echo $userpic ?>";
        document.getElementById("myProfileName").innerHTML = "<?php echo $username ?>";
    }


    var vod_path_GLOBAL;
    var g_channel_id;
    var g_program_id;
    var g_user_id;
    var g_isLive;
    var g_votes;
    var g_neg_votes;
    var myUserId;
    var facebookAcc;
    var twitterAcc;
    var facebookName;
    var twitterName;
    var mutex;
    var g_videoURL;
    var isRecording;
    var isRecordingStarted = false;
    var tags = "";
    var search_user_id = "";
    var search_filter = "nofilter";
    var program_info;
    var info_thread;
    var serverTime;
	
	var is_my_video = false;

	var progInfo = new Array();

    document.title = "YepLive";

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
                    vote_neg : "<?php echo $value->vote_neg?>",
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
                    location : "<?php echo $value->location?>",
					title : "<?php echo $value->title?>",
					description : "<?php echo $value->description?>",
					isMobile : "<?php echo $value->isMobile?>",
					connect_count : "<?php echo $value->connect_count ?>",
					imagePath : "<?php echo $value->image_path ?>",
					totalTime : "<?php echo $value->duration?>",
                    userpic : "<?php echo $value->userpic?>",
                    facebook_id : "<?php echo $value->facebook_id?>",
                    twitter_id : "<?php echo $value->twitter_id?>"
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
		<img class="cancel-img" src="<?php echo get_template_directory_uri()?>/images/close.png" onclick="onCancel();"/>
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

//	function playOfSelectedCate (progId) {
//		var programId = progId.split("_")[2];
//		for (var i=0 ; i<progInfo.length ; i++) {
//			if ( progInfo[i].pid == programId ) {
//
//				document.getElementById("video-list").style.display = "none";
//				var myNode = document.getElementById("video");
//				while (myNode.firstChild) {
//				    myNode.removeChild(myNode.firstChild);
//				}
//
//				VideoPlayer(progInfo[i].title ,progInfo[i].connect_count ,progInfo[i].pid ,progInfo[i].cid ,progInfo[i].user_id ,progInfo[i].isMobile ,progInfo[i].end_time ,progInfo[i].vod_path);
//
//				break;
//			}
//		}
//	}
	
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

        program_info = "";
        openVideoById(programNum);
		//VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);
				
  	}																//  end of showing selected video 



    window.mobilecheck = function() {
        var check = false;
        (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
        return check; }


	function VideoPlayer ( videoTitle , videoViewer , programNum, channelNum , userNum ,bMobile , EndTime ,vodPath)
    {
        isRecording = false;
        if (typeof timer_thread != 'undefined') clearInterval(timer_thread);
        if (typeof stop_record_timer_thread != 'undefined') clearInterval(stop_record_timer_thread);
        if (typeof info_thread != 'undefined') clearInterval(info_thread);
        if (typeof getStartTime_Thread != 'undefined') clearInterval(getStartTime_Thread);
        if (typeof menuLoad_thread != 'undefined') clearInterval(menuLoad_thread);

//        $('body').find('.objectDiv').attr('style','width: 480px; height: 320px; bottom: 0px; right: 0px;');
//        $('body').find('.objectVideo').attr('style','width: 480px; height: 320px; bottom: 0px; right: 0px;');
        $('body').find('.video-info').attr('style','display: block;');
        $('body').find('.cross-multi').attr('style','display: block;');
        $('body').find('.cross-expand').attr('style','display:block');
//        $('body').find('.cross-collapse').attr('style','display:none');
//        $('body').find('.video-bottom').attr('style','display: none;');

        $('body').find('.video-bottom').attr('style','display: none;');
        $('body').find('.video-textchat').attr('style','display: none;');
        $('body').find('.video-header-title').attr('style','display: none;');
        $('body').find('.video-header-user').attr('style','display: none;');
        $('body').find('.video-location').attr('style','display: none;');
        $('body').find('.video-location-live').attr('style','display: none;');

        $('body').find('.videoUserpicHeader').attr('style','display: block;');
        $('body').find('.videoUserinfoHeader').attr('style','display: block;');

        //$('body').find('.reportDiv').attr('style','display: block;');

        $('body').find('.expandedVideo').attr('style','display: block; z-index: 500; height: 0px; width: 0px; position: fixed; right: 0px; bottom: 0px');
        $('body').find('.stopRecordImg').attr('style','display: none;');

        if (document.getElementById("textchatDiv").contains(document.getElementById("textchat"))) document.getElementById("textchatDiv").removeChild(document.getElementById("textchat"));

        vod_path_GLOBAL = vodPath;
        g_channel_id = channelNum;
        g_program_id = programNum;
        g_user_id = userNum;

        var end_time;

        document.getElementById("thumbUpImg").style.display = "block";
        document.getElementById("thumbUpClickedImg").style.display = "none";
        document.getElementById("thumbDownImg").style.display = "block";
        document.getElementById("thumbDownClickedImg").style.display = "none";


        if ((program_info != "")&&(program_info != undefined))
        {
            facebookAcc = program_info[17];
            twitterAcc = program_info[18];
            facebookName = program_info[19];

            g_votes = program_info[4];
            $('body').find(".votesDiv").text(g_votes);

            g_neg_votes = program_info[5];
            $('body').find(".negVotesDiv").text(g_neg_votes);

            $('body').find(".bottomVideoTitleDiv").text(program_info[0]);
            $('body').find(".bottomVideoTimeDiv").text(program_info[10]);
            startDate = program_info[10];

            //setLocation(progInfo[i].lat, progInfo[i].lgt);

            $('body').find(".videoTitleHeader").text(videoTitle);
            $('body').find(".videoTimeHeader").text(moment(program_info[10]).format('h:mmA - MMMM Do, YYYY'));
            $('body').find(".videoDescHeader").text(program_info[12]);
            $('body').find(".videoTagsHeader").text(program_info[20]);


            if (facebookAcc == "") $('body').find(".videoFacebookHeader").attr('style','display:none');
            else
            {
                $('body').find(".videoFacebookHeader").attr('style','display:block');
                $('body').find(".videoUsernameHeader").text(facebookName);
            }
            if (twitterAcc == "") $('body').find(".videoTwitterHeader").attr('style','display:none');
            else
            {
                $('body').find(".videoTwitterHeader").attr('style','display:block');
                $('body').find(".videoUsernameHeader").text(twitterAcc);
            }

            if ((program_info[1] == "") || (program_info[1] == undefined)) document.getElementById("videoThumbnailHeader").src = "/wp-content/themes/Explorable/images/myprofile.png";
            else document.getElementById("videoThumbnailHeader").src = program_info[1];


            if (program_info[16] == "") document.getElementById("videoUserpicHeader").src = "/wp-content/themes/Explorable/images/profile-thumb.png";
            else document.getElementById("videoUserpicHeader").src = program_info[16];

            myUserId = "<?php echo $_SESSION['user_id']; ?>";

            if ((myUserId == userNum) || (myUserId == "")) {
                $('body').find(".followDivHeader").attr('style','display:none');
                is_my_video = true;
            }
            else {
                $('body').find(".followDivHeader").attr('style','vertical-align: middle; display: table-cell;');
                is_my_video = false;
            }

            if (program_info[15] != "") $('body').find(".videoUsernameHeader").text(program_info[15]);

            $('body').find(".locationText").text(program_info[8]);

            var now = new Date();
            //$('body').find(".timeAgoDiv").text(moment(progInfo[i].end_time, "YYYY-MM-DD hh:mm:ss").from(now.valueOf() + (now.getTimezoneOffset() * 60 * 1000)));
            $('body').find(".bottomVideoViewersDiv").text(numeral(program_info[13]).format('0,0') + " viewers");

            getIfVoted();
            getProgramVotes(programNum);
            getProgramNegVotes(programNum);
        }
        else
        {
            for (var i=0 ; i < progInfo.length ; i++)
            {
                if ( progInfo[i].pid == programNum )
                {
                    facebookAcc = progInfo[i].facebook_id;
                    twitterAcc = progInfo[i].twitter_id;
                    facebookName = progInfo[i].facebook_name;
                    twitterName = progInfo[i].twitter_name;

                    g_votes = progInfo[i].vote;
                    $('body').find(".votesDiv").text(g_votes);

                    g_neg_votes = progInfo[i].vote_neg;
                    $('body').find(".negVotesDiv").text(g_neg_votes);

                    $('body').find(".bottomVideoTitleDiv").text(progInfo[i].title);
                    $('body').find(".bottomVideoTimeDiv").text(progInfo[i].start_time);
                    startDate = progInfo[i].start_time;

                    //setLocation(progInfo[i].lat, progInfo[i].lgt);

                    $('body').find(".videoTitleHeader").text(progInfo[i].title);
                    $('body').find(".videoTimeHeader").text(moment(progInfo[i].start_time).format('h:mmA - MMMM Do, YYYY'));
                    $('body').find(".videoDescHeader").text(progInfo[i].description);


                    if (facebookAcc == "") $('body').find(".videoFacebookHeader").attr('style','display:none');
                    else
                    {
                        $('body').find(".videoFacebookHeader").attr('style','display:block');
                        $('body').find(".videoUsernameHeader").text(facebookName);
                    }
                    if (twitterAcc == "") $('body').find(".videoTwitterHeader").attr('style','display:none');
                    else
                    {
                        $('body').find(".videoTwitterHeader").attr('style','display:block');
                        $('body').find(".videoUsernameHeader").text(twitterName);
                    }

                    if ((progInfo[i].imagePath == "") || (progInfo[i].imagePath == undefined)) document.getElementById("videoThumbnailHeader").src = "/wp-content/themes/Explorable/images/myprofile.png";
                    else document.getElementById("videoThumbnailHeader").src = progInfo[i].imagePath;


                    if (progInfo[i].userpic == "") document.getElementById("videoUserpicHeader").src = "/wp-content/themes/Explorable/images/profile-thumb.png";
                    else document.getElementById("videoUserpicHeader").src = progInfo[i].userpic;

                    myUserId = "<?php echo $_SESSION['user_id']; ?>";

                    if ((myUserId == userNum) || (myUserId == "")) {
                        $('body').find(".followDivHeader").attr('style','display:none');
                        is_my_video = true;
                    }
                    else {
                        $('body').find(".followDivHeader").attr('style','vertical-align: middle; display: table-cell;');
                        is_my_video = false;
                    }

                    if (progInfo[i].username != "") $('body').find(".videoUsernameHeader").text(progInfo[i].username);

                    $('body').find(".locationText").text(progInfo[i].location);

                    var now = new Date();
                    //$('body').find(".timeAgoDiv").text(moment(progInfo[i].end_time, "YYYY-MM-DD hh:mm:ss").from(now.valueOf() + (now.getTimezoneOffset() * 60 * 1000)));
                    $('body').find(".bottomVideoViewersDiv").text(numeral(progInfo[i].connect_count).format('0,0') + " viewers");

                    getIfVoted();
                    getProgramVotes(programNum);
                    getProgramNegVotes(programNum);

                }
            }
        }

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


        document.getElementById("expandedVideo").appendChild(et_slider_wrapper);

        document.getElementById("expandedVideo").style.display = "block";
		
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

        objectDiv.id = "objectDiv";
        objectDiv.className = "objectDiv";             				// flash player loading
		//objectDiv.style.width = "100%";

		//objectDiv.style.height = "320px";
		objectDiv.style.bottom = 0 + "px";
		objectDiv.style.backgroundColor = "black";
		
		et_map_slide.appendChild(objectDiv);

		$('body').find(".video-info").css('display','block');
		$('body').find(".video-name").text(videoTitle);
		$('body').find("#video-title-view").find("#hidden-video-title").attr('value',programNum);
		$('body').find(".visit-count").text(numeral(videoViewer).format('0,0') + " viewers");
		$('body').find(".video-info").css('bottom','47vh');
		$('body').find(".cross-add").css('display','none');
		$('body').find(".cross-multi").css('display','block');
        $('body').find(".cross-expand").css('display','block');
        $('body').find(".cross-collapse").css('display','none');
		
		var video_viewer_count = videoViewer;
		var programId = programNum;

		jQuery.ajax({
			type: "GET",
		  	url: "<?php echo get_template_directory_uri()?>/ajaxVideoViewerCount.php/",
		  	data: 'programId=' + programId,
		  	success: function(data) {
				//alert(data);  	
		  	},
		  	error: function (data) {
			  	alert('viewer counting error');
		  	}
		});

        if( EndTime == "" || EndTime == 0 )
            g_isLive = "live";
        else
            g_isLive = "vod";

        if (mobilecheck())
        {
            if (g_isLive == "live")
            {
                $('body').find('.video-info').attr('style','display: block; color: #efefef; background-color: #5f0000;');
                $('body').find('.video-title-live').attr('style','display: block');
                $('body').find('.video-name').attr('style','width: 48%;');

                $('body').find('.cross-multi').attr('style','display: block; background: url(/wp-content/themes/Explorable/images/cross-multi-white.png)');
                $('body').find('.cross-add').attr('style','display: none; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
                $('body').find('.cross-expand').attr('style','display:block; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');

                //alert("Cannot play live video in mobile browser");
            }
            else
            {
                $('body').find('.video-title-live').attr('style','display: none');
                $('body').find('.video-name').attr('style','width: 60%;');
            }
            var video = document.createElement("video");
            //   video.src = "http://yeplive.fora-soft.com/wordpress/wp-content/content/40_2596_148_aac.mp4";
            video.style.width = "100%";
            video.style.height = "100%";
            //   video.width = "320";
            //  video.height = "240";
            video.controls = true;
            video.preload = "preload";
            video.autoplay = true;
            video.style.position = "absolute";
            video.style.left = "0px";
            video.style.top = "0px";

            var source = document.createElement("source");
            if (g_isLive == "live")
            {
                source.src = "<?php echo HTTP_WOWZA?>livestream/" + channelNum + "_" + programNum + "_" + userNum + "_aac" + "/playlist.m3u8";
            }
            else
            {
                source.src = "<?php echo home_url();?>/wp-content/content/" + vodPath;
               // source.src = "<?php echo HTTP_WOWZA?>vod/mp4:" + channelNum + "_" + programNum + "_" + userNum + ".mp4/playlist.m3u8";
            }
            video.appendChild(source);

            getTimeAgo(startDate);

            objectDiv.appendChild(video);

            var watermark = document.createElement("img");
            watermark.src = "/wp-content/themes/Explorable/images/watermark.png";
            watermark.style.position = "absolute";
            watermark.style.right = "10px";
            watermark.style.bottom = "10px";
            watermark.style.width = "25%";

            objectDiv.appendChild(watermark);



            /*
             video.addEventListener('click',function(){
             video.play();
             },false);*/

        }
        else
        {
            var object = document.createElement("object");	                           //   flash player parameter setting

            object.className = "objectVideo";
            object.type = "application/x-shockwave-flash";
            object.name = "StrobeMediaPlayback_" + (progInfo.length);
            object.data = "<?php echo get_template_directory_uri()?>/StrobeMediaPlaybackWrapper.swf";
            //object.data = "<?php echo get_template_directory_uri()?>/StrobePlay.swf";
            /*object.style.width = "480px";
             object.style.height = "320px";*/
            object.id = "video_player";
            object.style.visibility = "visible";
            //objectDiv.appendChild(object);


            var param = document.createElement("param");

            param.name = "backgroundColor";
            param.value = "#FFF000";
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "streamType";
            param.value = "recorded";
            object.appendChild(param);


            var param = document.createElement("param");

            param.name = "autoPlay";
            param.value = "true";
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "controlBarAutoHide";
            param.value = true;
            object.appendChild(param);


            var param = document.createElement("param");

            param.name = "controlBarMode";
            param.value = "docked";
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "playButtonOverlay";
            param.value = true;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "width";
            param.value = 960;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "height";
            param.value = 540;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "showVideoInfoOverlayOnStartUp";
            param.value = false;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "useFullScreenSourceRectOnFullScreenWithStageVideo";
            param.value = true;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "enableStageVideo";
            param.value = false;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "optimizeBuffering";
            param.value = true;
            object.appendChild(param);

            var param = document.createElement("param");

            param.name = "allowFullScreen";
            param.value = true;
            object.appendChild(param);





            var param = document.createElement("param");
            param.name = "flashvars";

            getTimeAgo(startDate);

            if(g_isLive == "live")											//  check whether selected video is Mobile or not
            {
                /*var pid = $(obj).find("input#programId").val();
                 var cid = $(obj).find("input#channelId").val();
                 var uid = $(obj).find("input#userId").val();*/



                $('body').find('.video-info').attr('style','display: block; color: #efefef; background-color: #5f0000;');
                $('body').find('.video-title-live').attr('style','display: block');
                $('body').find('.video-name').attr('style','width: 48%;');

                $('body').find('.cross-multi').attr('style','display: block; background: url(/wp-content/themes/Explorable/images/cross-multi-white.png)');
                $('body').find('.cross-add').attr('style','display: none; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
                $('body').find('.cross-expand').attr('style','display:block; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');

                if(  bMobile == "0")
                {
                    param.value = "src=<?php echo RTMP_WOWZA?>livestream/" + channelNum + "_" + programNum + "_" + userNum + "&autoPlay=true&streamType=live&bgColor=#000000&enableStageVideo=false&optimizeBuffering=true&allowFullScreen=true&useFullScreenSourceRectOnFullScreenWithStageVideo=true";
                    //param.value = "URL=rtmp://ec2-54-245-113-191.us-west-2.compute.amazonaws.com:1935/livestream/" + channelNum + "_" + programNum + "_" + userNum + "&autoPlay=true&streamType=live&bgColor=#000000&status=livestream";
                }
                else
                {
                    param.value = "src=<?php echo RTMP_WOWZA?>live/" + channelNum + "_" + programNum + "_" + userNum + "&autoPlay=true&streamType=live&bgColor=#000000&enableStageVideo=false&optimizeBuffering=true&allowFullScreen=true&useFullScreenSourceRectOnFullScreenWithStageVideo=true";
                }
            }
            else
            {
                $('body').find('.video-title-live').attr('style','display: none');
                $('body').find('.video-name').attr('style','width: 60%;');

                param.value = "src=<?php echo RTMP_WOWZA?>vod/mp4:" + vodPath + "&autoPlay=true&streamType=recorded&bgColor=#000000&enableStageVideo=false&optimizeBuffering=true&allowFullScreen=true&useFullScreenSourceRectOnFullScreenWithStageVideo=true";
            }

            //$('body').find(".time-ago-span").text(moment(startDate, "YYYY-MM-DD hh:mm:ss").from(now.valueOf() + (now.getTimezoneOffset() * 60 * 1000)));

            object.appendChild(param);

            objectDiv.appendChild(object);
        }

        info_thread = setInterval("getProgramInfo(g_program_id);",10000);

        g_videoURL = encodeURIComponent(window.location.host.toString() + "/?programId=" + g_program_id);
        document.getElementById("facebookShareButton").href = "https://www.facebook.com/sharer.php?u=" + g_videoURL;
        document.getElementById("twitterShareButton").href = "https://twitter.com/intent/tweet?text=" + encodeURIComponent(program_info[20]) + "&url=http://" + g_videoURL;
        document.getElementById("gplusShareButton").href = "https://plus.google.com/share?url=" + g_videoURL;
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

    function getProgramInfo(programId)
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_video = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_video = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_video.onreadystatechange=function()
        {
            if(xmlhttp_video.readyState == 4 && xmlhttp_video.status == 200)
            {
                if (xmlhttp_video.responseText == "error") return;

                program_info = xmlhttp_video.responseText.split("/|/|/");
                if (program_info[0] == "") return;

                $('body').find(".videoTitleHeader").text(program_info[0]);
                $('body').find(".video-name").text(program_info[0]);
                $('body').find(".videoDescHeader").text(program_info[12]);
                $('body').find(".videoTagsHeader").text(program_info[20]);

                $('body').find(".locationText").text(program_info[8]);
                if ((program_info[1] == "") || (program_info[1] == undefined)) document.getElementById("videoThumbnailHeader").src = "/wp-content/themes/Explorable/images/myprofile.png";
                else document.getElementById("videoThumbnailHeader").src = program_info[1];
            }
        }

        xmlhttp_video.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetProgramInfoById.php?program_id=" + programId , true);
        xmlhttp_video.send();
    }
	
	
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


	function channelName (msg) {
		alert(msg);
		
	}
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
				mapTypeControl: false,
				mapTypeControlOptions: {
					position : google.maps.ControlPosition.RIGHT_TOP,
					style : google.maps.MapTypeControlStyle.DROPDOWN_MENU
				},
				navigationControl: false,
				scrollwheel: true,
				streetViewControl: false,
                panControl: true,
                panControlOptions: {
                    position: google.maps.ControlPosition.LEFT_CENTER
                },
				zoomControl: true,
				zoomControlOptions: {
			        style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.LEFT_CENTER
			    },
			    unitSystem: google.maps.UnitSystem.IMPERIAL
			}
		}
	});

	var isFirstClick = true;
	
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

						    if (isFirstClick)
						    {
						        isFirstClick = false;
						        return;
						    }
							//marker.setAnimation(0);
							//marker.setAnimation( google.maps.Animation.BOUNCE);
							//marker.setIcon( '" . get_template_directory_uri() . "/images/black-marker-middle.png' );


                            var overlay = $(this).gmap3({get:{id:\"overlay_single_list\"}});
                            if (overlay) {
                                overlay.hide();
                                $(this).gmap3({
                                    clear: {
                                        id: \"overlay_single_list\"
                                    }
                                });
                            }

                            var homeURL = \"" . home_url() . "\";

                            var clusterContent = \"<div id='multiple' style='width:294px; height:auto;'><img id='clusterClose' src='" .  get_template_directory_uri() . "/images/close_cate.png' style='float:right; width:19px;height:19px; cursor:pointer; margin-top: 10px; margin-right: 9px;'></img><div style='width:100%; height:30px; background-color: #F5F9FF;border-top-right-radius:14px; border-top-left-radius:14px; text-align:center;line-height:40px; font-size: 18px; font-weight: 700;'></div>\";
                            clusterContent += \"<div style='width:100%; height:9px; background-color: #F5F9FF;'></div>\";
                            clusterContent += \"<div style='width:100%; height:auto; overflow-x: hidden; max-height: 216px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px;'>\";

                            var author = \"\";
                            var imgURL = \"\";

                            var index = context.id.split(\"_\")[2];

                            if ( progInfo[index].username != \"\" || progInfo[index].username == undefined )
                                        author = progInfo[index].username;
                                    if ( progInfo[index].facebook_name != \"\" || progInfo[index].facebook_name == undefined )
                                        author = progInfo[index].facebook_name;
                                    if ( progInfo[index].twitter_name != \"\" || progInfo[index].twitter_name == undefined )
                                        author = progInfo[index].twitter_name;

                            if ( progInfo[index].imagePath )
                                imgURL = homeURL + progInfo[index].imagePath;
                            else
                                imgURL = \"" . get_template_directory_uri() . "/images/myprofile.png\";
                            clusterContent += \"<div id='selectVideo_\" + index + \"' class='selVideo'>\";

                            clusterContent += \"<img src='\" + imgURL + \"' style='width: 40px; height: 42px; float:left; margin: 5px 9px;'></img>\";
                            clusterContent += \"<input type='hidden' id='indexProg' value='\" + index + \"' >\";
                            clusterContent += \"<input type='hidden' id='indexMul' value='\" + index + \"' >\";
                            clusterContent += \"<div style='display: inline-block; width: 79%; height:auto;'><span id='Vtitle_\" + index + \"' class='category-video-name' >\" + progInfo[index].title + \"</span>\";
                            if (progInfo[index].totalTime == undefined || progInfo[index].totalTime == \"\")
                                clusterContent += \"<span id='Vtime_\" + index + \"' class='category-video-time' >\" + \"LIVE\" + \"</span>\" + \"</div>\";
                            else
                                clusterContent += \"<span id='Vtime_\" + index + \"' class='category-video-time' >\" + progInfo[index].totalTime + \"</span>\" + \"</div>\";
                            clusterContent += \"<div style='width: 92%; height: auto; line-height: 12px;'><span id='author_\" + index + \"' class='map-author-name'>\" + author + \"</span>\";
                            clusterContent += \"<span id='viewers_\" + index + \"' class='map-viewer'>\" + progInfo[index].connect_count + \" VIEWS\" + \"</span></div></div>\";
                            clusterContent += \"<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>\";


                            clusterContent += \"</div></div>\";

                             var overlay = $(this).gmap3({get:{id:\"overlay_single_list\"}});
                                if(overlay) {

                                    overlay.show();
                                } else {
                                    $(this).gmap3(
                                        {
                                            overlay:{
                                                id : 'overlay_single_list',
                                                latLng : [progInfo[index].lat, progInfo[index].lgt],
                                                options:{
                                                    content : clusterContent,
                                                    offset:{
                                                        y:40,
                                                        x:-145
                                                    }
                                                },
                                                events:{														//  Cluster multiple overlay click
                                                    click:function(overlay,event,context){
                                                        if( event[0].target.id == \"clusterClose\" ){
                                                            var myNode = document.getElementById(\"multiple\");
                                                            while (myNode.firstChild) {
                                                                myNode.removeChild(myNode.firstChild);
                                                            }
                                                            $(\"#multiple\").remove();

                                                        }
                                                        if (( event[0].target.id != '') &&  (event[0].target.id != \"clusterClose\" )) {
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

                                                            var myNode = document.getElementById(\"multiple\");
                                                            while (myNode.firstChild) {
                                                                myNode.removeChild(myNode.firstChild);
                                                            }
                                                            $(\"#multiple\").remove();

                                                        }
                                                    }
                                                }
                                            }
                                        })

                                    var overlay = $(this).gmap3({get:{id:\"overlay_single_list\"}});
                                    setTimeout(function(){
                                        overlay.show();

                                    },300);

                                }

                        },
						mouseover: function( marker, event, context ){

						},
						mouseout: function( marker, event, context ){




						}
					},
					options:{
						icon: '" . get_template_directory_uri() . "/images/red-marker-small.png'
					}
					
				}"; 
			}
		} 
	?>	
//	function playOfselectedVideo (markerId) {
//		var markId = parseInt(markerId.split("_")[2]);
//
//		var videoTitle = progInfo[markId].title;
//		var videoViewer = progInfo[markId].connect_count;
//		var programNum = progInfo[markId].pid;
//		var channelNum = progInfo[markId].cid;
//		var userNum = progInfo[markId].user_id;
//		var EndTime = progInfo[markId].end_time;
//		var bMobile = progInfo[markId].isMobile;
//		var vodPath = progInfo[markId].vod_path;
//
//		VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);
//	}

	
	function et_add_marker( /*marker_lat, marker_lng, marker_description, nRating, bPlayer, username */){
		//et_add_marker_update();
		//var marker_id = 'et_marker_' + marker_order;
		var oIcon = "<?php echo get_template_directory_uri(); ?>/images/red-marker.png";
		var oAnimation = google.maps.Animation.DROP;

        $et_main_map.gmap3("clear");

		$et_main_map.gmap3({
			marker : {
				values:[<?php echo implode(",", $values_array)?>],
				cluster:{
					radius:100,
					events:{
						mouseover:function(cluster, event, context){
							var d = cluster.main.getPosition().k;
                            var e;
                            var clusterPosition = cluster.main.getPosition();
                            //strange, but sometimes gmap3 returns object with different field's name (A, B, C...)
                            if (clusterPosition.A != undefined) e = clusterPosition.A;
                            else if (clusterPosition.B != undefined) e = clusterPosition.B;
                            else if (clusterPosition.C != undefined) e = clusterPosition.C;
                            else if (clusterPosition.D != undefined) e = clusterPosition.D;
                            else if (clusterPosition.E != undefined) e = clusterPosition.E;

							var overlay = $(this).gmap3({get:{id:"overlay_cluster"}});
							if(overlay){
								
								//overlay.show();
							} else {
								$(this).gmap3(
		          						{
		          							overlay:{
												id : 'overlay_cluster',
												latLng : [d, e],
												options:{
													content : "<div style='color: red; font-size:25px; font-weight: 700;'>" + context.data.markers.length + "</div>",
													offset:{
														y:-90,
														x:-13
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
														y:0,
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
							clusterContent += "<div id='videosListBoxWrapper' style='width:100%; height:auto; overflow: auto; background: #F5F9FF; max-height: 216px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px; -webkit-overflow-scrolling: touch;'>";
                            clusterContent += "<div id='scroller' style='transition: transform 0ms; -webkit-transition: transform 0ms; transform-origin: 0px 0px 0px; transform: translate(0px, 0px) scale(1) translateZ(0px); overflow: hidden; max-height: 216px; border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;'>";
                            clusterContent += "<ul id='thelist' style='list-style: none; transform: translate(0px, 0px); overflow: auto'>";
							for(i=0 ; i<object.length ; i++) {
                                clusterContent += "<li>";
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
                                    clusterContent += "<div id='categoryImageDiv' style='width: 40px;'>";
                                    clusterContent += "<img src='" + imgURL + "' class='category-video-image' onMouseOver='imageZoomIn(this);' onMouseOut=''></img>";
                                    if (progInfo[index].totalTime == undefined || progInfo[index].totalTime == "")
                                        clusterContent += "<span id='Vtime_" + index + "' class='category-video-time' >" + "LIVE" + "</span>" + "</div>";
                                    else
                                        clusterContent += "<span id='Vtime_" + index + "' class='category-video-time' >" + progInfo[index].totalTime + "</span>" + "</div>";

                                    clusterContent += "<input type='hidden' id='indexProg' value='" + i + "' >";
                                    clusterContent += "<input type='hidden' id='indexMul' value='" + i + "' >";
                                    clusterContent += "<div style='display: inline-block; width: 79%; height:auto; max-height: 45px;'><span id='Vtitle_" + index + "' class='category-video-name' >" + progInfo[index].title + "</span>";
                                    clusterContent += "<span style='float:right; max-width:30%; padding-right:5px; padding-top:8px; text-align:end; font-size:10pt; '>" + (moment(progInfo[index].start_time, "YYYY-MM-DD hh:mm:ss").from(serverTime)) + "</span>"
                                    clusterContent += "</div>";
                                    clusterContent += "<div style='width: 78%; height: auto; line-height: 12px;display: inline-block; padding-bottom:2px; position:absolute;bottom:2px;left:56px;'><span id='author_" + index + "' class='map-author-name'>" + author + "</span>";
                                    clusterContent += "<span id='viewers_" + index + "' class='map-viewer'>" + progInfo[index].connect_count + " VIEWS" + "</span></div></div>";
                                    if (i != object.length - 1) clusterContent += "<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>";

									if ( progInfo[index].vod_path == "" && progInfo[index].start_time == "" ) {
										
									}
								// }
                                clusterContent += "</li>";
							}
                            clusterContent += "</ul>";
                            clusterContent += "</div>";
                            clusterContent += "</div></div>";

	
							var d = cluster.main.getPosition().k;
                            var e;
                            var clusterPosition = cluster.main.getPosition();
                            //strange, but sometimes gmap3 returns object with different field's name (A, B, C...)
                            if (clusterPosition.A != undefined) e = clusterPosition.A;
                            else if (clusterPosition.B != undefined) e = clusterPosition.B;
                            else if (clusterPosition.C != undefined) e = clusterPosition.C;
                            else if (clusterPosition.D != undefined) e = clusterPosition.D;
                            else if (clusterPosition.E != undefined) e = clusterPosition.E;

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

                                                            program_info = "";
                                                            openVideoById(programNum);
															//VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);

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
                        offset: {
                            x: -25,
                            y: -51
                        }

				      },
			        5: {
			        	content: "<div class='cluster cluster-2' style='color:black;z-index:10000;font-size:23px; cursor: pointer;'></div>",
			        	width: 56,
			        	height: 57,
                        offset: {
                            x: -27,
                            y: -58
                        }

			        },
			        10: {
			        	content: "<div class='cluster cluster-3' style='color:blue;z-index:10000;font-size:23px; cursor: pointer;'></div>",
			        	width: 66,
			        	height: 65,
                        offset: {
                            x: -32,
                            y: -66
                        }
			        }
				}
		          	
			},
			events: {
				click:function(marker, event, context){
					//marker.setAnimation( google.maps.Animation.BOUNCE);
					//marker.setIcon( '" . get_template_directory_uri() . "/images/black-marker-middle.png' );
				}
			},
            options:{
                icon: "<?php get_template_directory_uri()?>/images/red-marker-small.png",
                iconAnchor: [18,48]
            }

		});
	}

	<?php
	if (sizeof($emap_items) > 0 ) {
		echo "et_add_marker();";
	}
	?>

    var movedElementsCount = 0;
    var elementsInList = 0;

    window.imageZoomIn = function(image) {
        var imageTextRectangle = image.getBoundingClientRect();
        //alert("top: " + imageTextRectangle.top + " left: " + imageTextRectangle.left);

        var bigImage = document.createElement("img");
        bigImage.id = "bigImage";
        bigImage.width = 100;
        bigImage.height = 100;
        bigImage.style.width = "100px";
        bigImage.style.height = "100px";
        var bigImageLeft = imageTextRectangle.right - image.width / 2 - bigImage.width / 2;
        bigImage.style.left = bigImageLeft + "px";
        var bigImageTop = imageTextRectangle.bottom - image.height / 2 - bigImage.height / 2;
        bigImage.style.top = bigImageTop + "px";

        bigImage.style.position = "absolute";
        bigImage.src = image.src;
        bigImage.onmouseout = function() {
            var bigImage = document.getElementById("bigImage");
            if (bigImage != null) document.body.removeChild(bigImage);
        };

        document.body.appendChild(bigImage);

    }

    window.imageZoomOut = function(image) {
        //var imageTextRectangle = image.getBoundingClientRect();
        //alert("bottom: " + imageTextRectangle.bottom + " right: " + imageTextRectangle.right);
        var bigImage = document.getElementById("bigImage");
        if (bigImage != null) document.body.removeChild(bigImage);
    }

	 function et_add_marker_update() {

		// $et_main_map.gmap3({ clear: {} });
         $et_main_map.gmap3("clear");
		 	var tempArr = new Array();
		 	
		 	for ( var i = 0 ; i < progInfo.length ; i++) {
			 	
		 		tempArr[ i ] = {
				 		id : 'et_marker_' + i,
				 		latLng : [progInfo[i].lat, progInfo[i].lgt],
				 		data: [progInfo[i].title , progInfo[i].username],
				 		events:{
							click:function(marker, event, context){
								//marker.setAnimation(0);
								//marker.setAnimation( google.maps.Animation.BOUNCE);
								//marker.setIcon( "<?php echo get_template_directory_uri()?>/images/black-marker-middle.png" );

								//playOfselectedVideo(context.id);

                                var overlay = $(this).gmap3({get:{id:"overlay_cluster_list"}});
                                if (overlay) {
                                    overlay.hide();
                                    $(this).gmap3({
                                        clear: {
                                            id: "overlay_cluster_list"
                                        }
                                    });
                                }

                                //var object = context.data.markers;
                                var homeURL = "<?php echo home_url();?>";

                                var clusterContent = "<div id='multiple' style='width:294px; height:auto;'><img id='clusterClose' src='<?php echo get_template_directory_uri()?>/images/close_cate.png' style='float:right; width:19px;height:19px; cursor:pointer; margin-top: 10px; margin-right: 9px;'></img><div style='width:100%; height:30px; background-color: #F5F9FF;border-top-right-radius:14px; border-top-left-radius:14px; text-align:center;line-height:40px; font-size: 18px; font-weight: 700;'/>";
                                clusterContent += "<div style='width:100%; height:9px; background-color: #F5F9FF;'></div>";
                                clusterContent += "<div style='width:100%; height:auto; overflow-x: hidden; max-height: 216px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px;'>";

                               // for(i=0 ; i<object.length ; i++) {

                                    var author = "";
                                    var imgURL = "";

                                    var index = context.id.split("_")[2];

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
                                    clusterContent += "<div id='categoryImageDiv' style='width: 40px;'>";
                                    clusterContent += "<img src='" + imgURL + "' class='category-video-image' onMouseOver='imageZoomIn(this);' onMouseOut=''></img>";
                                    if (progInfo[index].totalTime == undefined || progInfo[index].totalTime == "")
                                        clusterContent += "<span id='Vtime_" + index + "' class='category-video-time' >" + "LIVE" + "</span>" + "</div>";
                                    else
                                        clusterContent += "<span id='Vtime_" + index + "' class='category-video-time' >" + progInfo[index].totalTime + "</span>" + "</div>";

                                    clusterContent += "<input type='hidden' id='indexProg' value='" + i + "' >";
                                    clusterContent += "<input type='hidden' id='indexMul' value='" + i + "' >";
                                    clusterContent += "<div style='display: inline-block; width: 79%; height:auto; max-height: 45px;'><span id='Vtitle_" + index + "' class='category-video-name' >" + progInfo[index].title + "</span>";
                                    clusterContent += "<span style='float:right; max-width:30%; padding-right:5px; padding-top:8px; text-align:end; font-size:10pt; '>" + (moment(progInfo[index].start_time, "YYYY-MM-DD hh:mm:ss").from(serverTime)) + "</span>"
                                    clusterContent += "</div>";
                                    clusterContent += "<div style='width: 78%; height: auto; line-height: 12px;display: inline-block; padding-bottom:2px; position:absolute;bottom:2px;left:56px;'><span id='author_" + index + "' class='map-author-name'>" + author + "</span>";
                                    clusterContent += "<span id='viewers_" + index + "' class='map-viewer'>" + progInfo[index].connect_count + " VIEWS" + "</span></div></div>";
                                    //clusterContent += "<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>";
                                    //}
                             //   }

                                clusterContent += "</div></div>";



                                var overlay = $(this).gmap3({get:{id:"overlay_cluster_list"}});
                                if(overlay) {

                                    overlay.show();
                                } else {
                                    $(this).gmap3(
                                        {
                                            overlay:{
                                                id : 'overlay_cluster_list',
                                                latLng : [progInfo[index].lat, progInfo[index].lgt],
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
                                                        else if( (event[0].target.id == "clusterListDownButton") || ( event[0].target.parentElement.id == "clusterListDownButton" ) ){
                                                            var list = document.getElementById("thelist");
                                                            if (movedElementsCount + 1 > elementsInList - 4) return;
                                                            movedElementsCount++;
                                                            document.getElementById("clusterListDownButton").style["background-color"] = "#afafaf";
                                                            if (movedElementsCount + 1 > elementsInList - 4)
                                                                event[0].target.style["background-color"] = "#cfcfcf";
                                                            var move = movedElementsCount * 54;
                                                            list.style["transform"] = "translate(0px, -" + move + "px)";
                                                        }
                                                        else if( (event[0].target.id == "clusterListUpButton") || ( event[0].target.parentElement.id == "clusterListUpButton" ) ){
                                                            var list = document.getElementById("thelist");
                                                            if (movedElementsCount < 1) return;
                                                            movedElementsCount--;
                                                            document.getElementById("clusterListUpButton").style["background-color"] = "#afafaf";
                                                            if (movedElementsCount + 1 > elementsInList - 4)
                                                                event[0].target.style["background-color"] = "#cfcfcf";
                                                            var move = movedElementsCount * 54;
                                                            list.style["transform"] = "translate(0px, -" + move + "px)";
                                                        }
                                                        else if ( event[0].target.id != "" &&  event[0].target.id != "clusterClose" ) {
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

                                                            program_info = "";
                                                            openVideoById(programNum);
                                                            //VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);

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
						options:{
							icon: "<?php echo get_template_directory_uri()?>/images/red-marker-small.png",
                            iconAnchor: [18,48]
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
                                var e;
                                var clusterPosition = cluster.main.getPosition();
                                //strange, but sometimes gmap3 returns object with different field's name (A, B, C...)
                                if (clusterPosition.A != undefined) e = clusterPosition.A;
                                else if (clusterPosition.B != undefined) e = clusterPosition.B;
                                else if (clusterPosition.C != undefined) e = clusterPosition.C;
                                else if (clusterPosition.D != undefined) e = clusterPosition.D;
                                else if (clusterPosition.E != undefined) e = clusterPosition.E;

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
                                                            y:-90,
                                                            x:-13
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
                                var e;
                                var clusterPosition = cluster.main.getPosition();
                                //strange, but sometimes gmap3 returns object with different field's name (A, B, C...)
                                if (clusterPosition.A != undefined) e = clusterPosition.A;
                                else if (clusterPosition.B != undefined) e = clusterPosition.B;
                                else if (clusterPosition.C != undefined) e = clusterPosition.C;
                                else if (clusterPosition.D != undefined) e = clusterPosition.D;
                                else if (clusterPosition.E != undefined) e = clusterPosition.E;

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
								clusterContent += "<div id='videosListBoxWrapper' style='width:100%; height:auto; background: #F5F9FF; max-height: 256px; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px; -webkit-overflow-scrolling: touch;'>";

                                elementsInList = object.length;

                                if (mobilecheck() && (object.length > 4))
                                {
                                    clusterContent += "<div id='clusterListUpButton' style='height:20px; width: 100%; cursor: pointer; background-color: #dfdfdf;'>";
                                    clusterContent += "<img id='clusterListUpImage' src='<?php echo get_template_directory_uri()?>/images/et-up-arrow.png' style='width: 20px; height: 20px; margin-left: 138px;'/>";
                                    clusterContent += "</div>";
                                    clusterContent += "<div id='scroller' style=' overflow: hidden; transition: transform 0ms; -webkit-transition: transform 0ms; transform-origin: 0px 0px 0px; transform: translate(0px, 0px) scale(1) translateZ(0px); max-height: 216px; border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;'>";
                                    clusterContent += "<ul id='thelist' style='list-style: none; transform: translate(0px, 0px);'>";
                                }
                                else
                                {
                                    clusterContent += "<div id='scroller' style=' overflow: auto; transition: transform 0ms; -webkit-transition: transform 0ms; transform-origin: 0px 0px 0px; transform: translate(0px, 0px) scale(1) translateZ(0px); max-height: 216px; border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;'>";
                                    clusterContent += "<ul id='thelist' style='list-style: none; transform: translate(0px, 0px);'>";
                                }
                                for(i=0 ; i<object.length ; i++) {
                                    clusterContent += "<li>";
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
                                    clusterContent += "<div id='categoryImageDiv' style='width: 40px;'>";
                                    clusterContent += "<img src='" + imgURL + "' class='category-video-image' onMouseOver='imageZoomIn(this);' onMouseOut=''></img>";
                                    if (progInfo[index].totalTime == undefined || progInfo[index].totalTime == "")
                                        clusterContent += "<span id='Vtime_" + index + "' class='category-video-time' >" + "LIVE" + "</span>" + "</div>";
                                    else
                                        clusterContent += "<span id='Vtime_" + index + "' class='category-video-time' >" + progInfo[index].totalTime + "</span>" + "</div>";

                                    clusterContent += "<input type='hidden' id='indexProg' value='" + i + "' >";
                                    clusterContent += "<input type='hidden' id='indexMul' value='" + i + "' >";
                                    clusterContent += "<div style='display: inline-block; width: 79%; height:auto; max-height: 45px;'><span id='Vtitle_" + index + "' class='category-video-name' >" + progInfo[index].title + "</span>";
                                    clusterContent += "<span style='float:right; max-width:30%; padding-right:5px; padding-top:8px; text-align:end; font-size:10pt; '>" + (moment(progInfo[index].start_time, "YYYY-MM-DD hh:mm:ss").from(serverTime)) + "</span>"
                                    clusterContent += "</div>";
                                    clusterContent += "<div style='width: 78%; height: auto; line-height: 12px;display: inline-block; padding-bottom:2px; position:absolute;bottom:2px;left:56px;'><span id='author_" + index + "' class='map-author-name'>" + author + "</span>";
                                    clusterContent += "<span id='viewers_" + index + "' class='map-viewer'>" + progInfo[index].connect_count + " VIEWS" + "</span></div></div>";
                                    if (i != object.length - 1) clusterContent += "<div style='width: 87%; background-color: #000; height: 1px; margin-left: 19px;'></div>";
                                    clusterContent += "</li>";
									//}
								}
                                clusterContent += "</ul>";

                                clusterContent += "</div>";
                                if (mobilecheck() && (object.length > 4))
                                {
                                    clusterContent += "<div id='clusterListDownButton' style='height:20px; width: 100%; cursor: pointer; background-color: #afafaf; border-bottom-right-radius: 14px; border-bottom-left-radius: 14px'>";
                                    clusterContent += "<img id='clusterListDownImage' src='<?php echo get_template_directory_uri()?>/images/et-down-arrow.png' style='width: 20px; height: 20px; margin-left: 138px;'/>";
                                    clusterContent += "</div>";
                                }
                                //clusterContent += "<div style='position: absolute; z-index: 100; width: 7px; bottom: 2px; top: 2px; right: 1px; pointer-events: none; transition: opacity 0ms 0ms; -webkit-transition: opacity 0ms 0ms; overflow: hidden; opacity: 1;'><div style='position: absolute; z-index: 100; border: 1px solid rgba(255, 255, 255, 0.901961); box-sizing: border-box; width: 100%; border-top-left-radius: 3px; border-top-right-radius: 3px; border-bottom-right-radius: 3px; border-bottom-left-radius: 3px; pointer-events: none; transition: transform 0ms cubic-bezier(0.33, 0.66, 0.66, 1); -webkit-transition: transform 0ms cubic-bezier(0.33, 0.66, 0.66, 1); transform: translate(0px, 0px) translateZ(0px); height: 187px; background: padding-box padding-box rgba(0, 0, 0, 0.498039);'></div></div>";
                                clusterContent += "</div>";

		
								var d = cluster.main.getPosition().k;
                                var e;
                                var clusterPosition = cluster.main.getPosition();
                                //strange, but sometimes gmap3 returns object with different field's name (A, B, C...)
                                if (clusterPosition.A != undefined) e = clusterPosition.A;
                                else if (clusterPosition.B != undefined) e = clusterPosition.B;
                                else if (clusterPosition.C != undefined) e = clusterPosition.C;
                                else if (clusterPosition.D != undefined) e = clusterPosition.D;
                                else if (clusterPosition.E != undefined) e = clusterPosition.E;

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
                                                            else if( (event[0].target.id == "clusterListDownButton") || ( event[0].target.parentElement.id == "clusterListDownButton" ) ){
                                                                var list = document.getElementById("thelist");
                                                                if (movedElementsCount + 1 > elementsInList - 4) return;
                                                                movedElementsCount++;
                                                                document.getElementById("clusterListUpButton").style["background-color"] = "#afafaf";
                                                                if (movedElementsCount + 1 > elementsInList - 4)
                                                                    document.getElementById("clusterListDownButton").style["background-color"] = "#dfdfdf";
                                                                var move = movedElementsCount * 54;
                                                                list.style["transform"] = "translate(0px, -" + move + "px)";
                                                            }
                                                            else if( (event[0].target.id == "clusterListUpButton") || ( event[0].target.parentElement.id == "clusterListUpButton" ) ){
                                                                var list = document.getElementById("thelist");
                                                                if (movedElementsCount < 1) return;
                                                                movedElementsCount--;
                                                                document.getElementById("clusterListDownButton").style["background-color"] = "#afafaf";
                                                                if (movedElementsCount < 1)
                                                                    document.getElementById("clusterListUpButton").style["background-color"] = "#dfdfdf";
                                                                var move = movedElementsCount * 54;
                                                                list.style["transform"] = "translate(0px, -" + move + "px)";
                                                            }
															else if ( event[0].target.id != "" &&  event[0].target.id != "clusterClose" ) {
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

                                                                program_info = "";
                                                                openVideoById(programNum);
																//VideoPlayer(videoTitle , videoViewer , programNum, channelNum , userNum , bMobile , EndTime ,vodPath);

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
                            offset: {
                                x: -25,
                                y: -53
                            }
					      },
				        5: {
				        	content: "<div class='cluster cluster-2' style='color:black;z-index:10000;font-size:23px; cursor: pointer;'></div>",
				        	width: 56,
				        	height: 57,
                            offset: {
                                x: -27,
                                y: -58
                            }
				        },
				        10: {
				        	content: "<div class='cluster cluster-3' style='color:blue;z-index:10000;font-size:23px; cursor: pointer;'></div>",
				        	width: 66,
				        	height: 65,
                            offset: {
                                x: -32,
                                y: -66
                            }
				        }
					}

				},
				events: {
					click:function(marker, event, context){
						//marker.setAnimation( google.maps.Animation.BOUNCE);
						//marker.setIcon( "<?php get_template_directory_uri() ?>/images/black-marker-middle.png" );
					},
                    dblclick:function(target, event,context){
                        if( (event[0].target.id == "clusterListDownButton") || ( event[0].target.parentElement.id == "clusterListDownButton" ) ||
                            (event[0].target.id == "clusterListUpButton") || ( event[0].target.parentElement.id == "clusterListUpButton" ))
                                return;
                        }
				},
				options:{
					icon: "<?php get_template_directory_uri()?>/images/red-marker-small.png",
                    iconAnchor: [18,48]
				}

			});

            if ((tags != "")||(search_user_id != "")) $et_main_map.gmap3("autofit");
			
		}


    window.updateMarkers = function ()
    {

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
                getServerTime();
                if(xmlhttp.responseText == "") {
                    progInfo = [];
                    et_add_marker_update();
                    return;
                }


                var emapinfo = xmlhttp.responseText.split("///");
                if ( emapinfo.length == 1 ) {
                    progInfo = [];
                    et_add_marker_update();
                    return;
                }

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
                    var location = emapinfo[emapinfo.length - 2].split("---")[18];
                    var vote_neg = emapinfo[emapinfo.length - 2].split("---")[19];
                    var facebook_id = emapinfo[emapinfo.length - 2].split("---")[20];
                    var userpic = emapinfo[emapinfo.length - 2].split("---")[21];
                    var twitter_id = emapinfo[emapinfo.length - 2].split("---")[22];
                    var duration = emapinfo[emapinfo.length - 2].split("---")[23];

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
                        || progInfo[progInfo.length-1].connect_count != connect_count
                        || progInfo[progInfo.length-1].imagePath != imagePath
                        || progInfo[progInfo.length-1].location != location
                        || progInfo[progInfo.length-1].vote_neg != vote_neg
                        || progInfo[progInfo.length-1].facebook_id != facebook_id
                        || progInfo[progInfo.length-1].twitter_id != twitter_id
                        || progInfo[progInfo.length-1].userpic != userpic
                        || progInfo[progInfo.length-1].totalTime != duration
                        )
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
                        progInfo[progInfo.length-1].location = location;
                        progInfo[progInfo.length-1].vote_neg = vote_neg;
                        progInfo[progInfo.length-1].facebook_id = facebook_id;
                        progInfo[progInfo.length-1].twitter_id = twitter_id;
                        progInfo[progInfo.length-1].userpic = userpic;
                        progInfo[progInfo.length-1].totalTime = duration;

                        var total_time = dateCal(start_time , end_time);
                        //progInfo[progInfo.length-1].totalTime = duration;

                        et_add_marker_update();
                    }
                }
                else
                {
                    progInfo = [];
                    temp = new Array();
                    for ( var index = 0; index < emapinfo.length-1 ; index++ )
                    {

                        progInfo[index] =
                        {
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
                            imagePath: emapinfo[index].split("---")[17],
                            location: emapinfo[index].split("---")[18],
                            vote_neg: emapinfo[index].split("---")[19],
                            facebook_id: emapinfo[index].split("---")[20],
                            userpic: emapinfo[index].split("---")[21],
                            twitter_id: emapinfo[index].split("---")[22],
                            totalTime: emapinfo[index].split("---")[23]

                        };

                        if ( ( progInfo[index].start_time ) && ( progInfo[index].end_time ) )
                        {
                            var total_time = dateCal(progInfo[index].start_time , progInfo[index].end_time);
                            //progInfo[index].totalTime = duration;
                        }

                        // map reloading

//                        et_add_marker_update();

                    }
                    et_add_marker_update();
                }
                //tags = "";
                //search_user_id = ""
            }
        }

        if (search_user_id != "")
            xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxemapshow.php?program_id=" + program_id + "&user_id=" + search_user_id + "&filter=" + search_filter,true);
        else if (tags != "")
            xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxemapshow.php?program_id=" + program_id + "&tags=" + tags + "&filter=" + search_filter,true);
        else
            xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxemapshow.php?program_id=" + program_id + "&filter=" + search_filter,true);
        xmlhttp.send();

        //tags = "";
        //search_user_id = "";
    }

    window.setInterval(updateMarkers, 5000);

    window.getMapBounds = function()
    {
        var map = $(et_main_map).gmap3('get');
        var bounds =  map.getBounds();
        alert("NE: " + bounds.getNorthEast().toString() + "\nSW: " + bounds.getSouthWest().toString());
    }
	
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





<script>

    if ('<?php echo $get_program_id?>' != "")
    {
        document.getElementById('programId').value = '<?php echo $get_program_id?>';
        document.aform.action = "<?php echo home_url()?>".split("/")[0];
        document.aform.submit();
    }

    if ('<?php echo $get_user?>' != "")
    {
        document.getElementById('programerName').value = '<?php echo $get_user?>';
        document.aform.action = "<?php echo home_url()?>".split("/")[0];
        document.aform.submit();
    }



    function facebookShare()
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
            if(progInfo[i].pid == g_program_id)
            {
<!--                title = progInfo[i].title;-->
<!--                 var ch = document.getElementById("--><?php //echo $channel_id?><!--");-->
<!--             var inputTag = ch.getElementsByTagName("input");-->
<!--             channelUrlStr = inputTag[0].value;-->

                title = "Such an interesting video on YepLive!";
                channelUrlStr = window.location.host.toString();

                break;
            }
        }

        var liveVod = "live";
        for(i = 0; i < progInfo.length; i++)
        {
            if(progInfo[i].pid == g_program_id)
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


        channelUrlStr = channelUrlStr + "/people/?hidChannelId=" + g_channel_id + "&liveVod=" + liveVod + "&programId=" + g_program_id;

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

    function twitterShare()
    {
        <?php
                if(!isset($_SESSION['user_id']))
                {?>
        //alert("Please,do login!");
        fn_register();
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
            if(progInfo[i].pid == g_program_id)
            {
                //title = progInfo[i].title;
                /*var ch = document.getElementById("<?php echo $channel_id?>");
             var inputTag = ch.getElementsByTagName("input");
             channelUrlStr = inputTag[0].value;*/

                title = "Such an interesting video on YepLive!";
                channelUrlStr = window.location.host.toString();

                break;
            }
        }

        var liveVod = "live";
        for(i = 0; i < progInfo.length; i++)
        {

            if(progInfo[i].pid == g_program_id)
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

        channelUrlStr = channelUrlStr + "/people/?hidChannelId=" + g_channel_id + "&liveVod=" + liveVod + "&programId=" + g_program_id;

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


<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/swfobject.js"></script>

<div id="report_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <div onclick="fn_closePopup();" class="popClose"><img src="<?php echo get_template_directory_uri()?>/images/login-close.png" style="width:53px;"></div>
    <iframe id="reportFrame" frameborder="no" src="" scrolling="no" style="width:1024px;height:555px;z-index: 1000000;border-radius:10px;">
    </iframe>
</div>

<div id="suspend_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <div onclick="fn_closePopup();" class="popClose"><img src="<?php echo get_template_directory_uri()?>/images/login-close.png" style="width:53px;"></div>
    <iframe id="suspendFrame" frameborder="no" src="" scrolling="no" style="width:1024px;height:555px;z-index: 1000000;border-radius:10px;">
    </iframe>
</div>


<span id="copyright" style="position: absolute; left: 2px; bottom: 1px; color: #000000; font-size: 7.5pt; padding-bottom: 0px">© 2014 YepLive, LLC</span>

<div id="expandedVideo" class="expandedVideo" style="display: none; z-index: 500; height: 0px; width: 0px; position: fixed; right: 0px; bottom: 0px;">

    <div class="video-info" >
        <div id="crossMulti" class="cross-multi" onclick="videoClose();"></div>
        <div class="cross-add" onclick="videoOpen();"></div>
        <div class="cross-expand" onclick="videoExpand();"></div>
        <div id="video-title-view" style="cursor: default;">
            <div class="video-name" ></div>
            <div class="video-title-live">
                LIVE
            </div>
            <input type="hidden" id="hidden-video-title" value="">
            <div style="width: 25%; height: 35px; float: right; font-size: 12px; text-align: right;">
                <span class="time-ago-span"></span>
                <span class="visit-count"></span>
            </div>
        </div>
    </div>


    <div id="textchatDiv" class="video-textchat">
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
                    $is_live = true;
                    if($values->isMobile == "0")
                        echo 'src='.RTMP_WOWZA.'livestream/'.$values->cid.'_'.$values->pid.'_'.$values->userid.'&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000';
                    else
                        echo 'src='.RTMP_WOWZA.'live/'.$values->cid.'_'.$values->pid.'_'.$values->userid.'&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000';
                } else {
                    $is_live = false;
                    echo 'src='.RTMP_WOWZA.'vod/mp4:'.get_vod_title($values->vod_path).'&amp;autoPlay=true&amp;streamType=vod&amp;bgColor=#ff0000';
                }?>">
                            </object>
                <?php }?>

                </div>
        <?php
                //$i++;
            }
        }?>
    </div>
    </div>
    <?php } ?> -->
    </div>



    <div class="video-bottom">

        <div style="width: 12%; height:100%; float: left">
            <div class="timeAgoDiv" style="width: 100%; height:50%; float: left; padding-left: 1%; color: white; font-size: 1.3vh; line-height: 330%;">

            </div>

            <div class="stopRecordDiv" style="width: 100%; height:50%; float: left; padding-left: 1%; color: white; font-size: 1.3vh; line-height: 330%;">
                <img id="stopRecordImg" class="stopRecordImg" src="<?php echo get_template_directory_uri(); ?>/images/record-stop-red.png" title="Stop recording" onclick="stopRecord();">
            </div>
        </div>


        <div style="width: 25%; height: 95%; float:left; padding-top: 1%; padding-right: 1%; color: white; text-align: left; font-family: 'Myriad Pro';">
            <div class="bottomVideoViewersDiv" style=" width:100%; height: 30%; font-size: 2vh; line-height: 165%; overflow: hidden; text-align: center">0 Viewers</div>

            <hr align="center" width="100%" size="2" color="#ffffff" style="margin-bottom: 3%; margin-top: 3%;"/>

            <div class="bottomVideoVotesDiv" style=" width:100%; height: 50%; font-size: 1.7vh; line-height: 20px; padding-left: 5%">
                <div style="height: 100%; width: 50%; float: left;">
                    <div id="thumbUpDiv" class="thumbUpDiv">
                        <img id="thumbUpImg" src="<?php echo get_template_directory_uri(); ?>/images/thumb_up_without_rect.png" title="Like" onclick="onVoteClick(true)">
                        <img id="thumbUpClickedImg" style="display: none" src="<?php echo get_template_directory_uri(); ?>/images/thumb_up_clicked_without_rect.png">
                    </div>
                    <div class="votesDiv" style="float:left; width: 65%; padding-left: 5%; line-height: 3.5vh;">0</div>
                </div>

                <div style="height: 100%; width: 50%; float: left">
                    <div id="thumbDownDiv" class="thumbDownDiv">
                        <img id="thumbDownImg" src="<?php echo get_template_directory_uri(); ?>/images/thumb_down_without_rect.png" title="Dislike" onclick="onVoteClick(false)">
                        <img id="thumbDownClickedImg" style="display: none" src="<?php echo get_template_directory_uri(); ?>/images/thumb_down_clicked_without_rect.png">
                    </div>
                    <div class="negVotesDiv" style="float:left; width: 65%; padding-left: 5%; line-height: 3.5vh;">0</div>
                </div>

            </div>
        </div>






        <div style="width: 8.5%; height: 85%; padding-left: 1%; padding-top: 0.9%; float: left;">
            <a id="instagramShareButton" onclick="">
                <img src="<?php echo get_template_directory_uri(); ?>/images/instagram_logo_big.png" onclick="" alt="Share on Instagram" style="width: 100%; height: 100%; cursor:pointer; float:left;">
            </a>
        </div>

        <div style="width: 8.5%; height: 85%; padding-left: 1%; padding-top: 0.9%; float: left;">
            <a id="facebookShareButton" href="https://www.facebook.com/sharer.php?u=" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                <img src="<?php echo get_template_directory_uri(); ?>/images/facebook_logo_big.png" onclick="" alt="Share on Facebook" style="width: 100%; height: 100%; cursor:pointer; float:left;">
            </a>
        </div>

        <div style="width: 8.5%; height: 85%; padding-left: 1%; padding-top: 0.9%; float: left;">
            <a id="twitterShareButton" href="https://twitter.com/intent/tweet?text=Such an interesting video on YepLive&url=" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                <img src="<?php echo get_template_directory_uri(); ?>/images/twitter_logo_big.png" onclick="" alt="Share on Twitter" style="width: 100%; height: 100%; cursor:pointer; float:left;">
            </a>
        </div>

        <div style="width: 8.5%; height: 85%; padding-left: 1%; padding-top: 0.9%; float: left;">
            <a id="gplusShareButton" href="https://plus.google.com/share?url={URL}" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
                <img src="<?php echo get_template_directory_uri(); ?>/images/googleplus_logo_big.png" style="width: 100%; height: 100%; cursor:pointer; float:left;" alt="Share on Google+"/>
            </a>
        </div>


        <div class="reportDiv" style="<?php if ($_SESSION["user_group_id"] == '1') echo  "display: none;"?>">
            <a href="javascript:void(0);" onclick="reportVideo()" style="cursor:pointer;  font-size: 2.5vh; color: #fb9da4; font-family: 'Myriad Pro'">Report this video</a>
        </div>

        <div id="suspendUserDiv" class="reportDiv" style="<?php if ($_SESSION["user_group_id"] == '1') echo  "display: block;"; else echo "display: none;"?>">
            <a href="javascript:void(0);" onclick="suspendUser()" style="cursor:pointer;  font-size: 2.5vh; color: #fb9da4; font-family: 'Myriad Pro'">Suspend user</a>
        </div>

    </div>



    <div class="video-header-title" style="">
        <div class="cross-collapse" onclick="videoCollapse();"></div>
        <div style="width: 13%; height: 90%; float:left; text-align: left; padding-left: 1%; padding-top: 2.5%">
            <img id="videoThumbnailHeader" class="videoThumbnailHeader" style="width: 95%; height: 85%;">
        </div>
        <div style="width: 80%; height: 80%; padding-top: 2%; font-family:'Myriad Pro'; color: #545454; float:left; text-align: left;">
            <div class="videoTitleHeader" style="font-size: 4vh; line-height: 100%; height: 40%; overflow: hidden;"></div>
            <div class="videoTimeHeader" style="font-size: 1.7vh; line-height: 100%; height: 15%; padding-top: 1%"></div>
            <div class="videoDescHeader" style="font-size: 1.7vh; line-height: 1em; max-height: 2em; overflow: hidden"></div>
            <div class="videoTagsHeader" style="color:#af121e; font-size: 1.7vh; line-height: 1em; height: 1em; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;"></div>
        </div>

    </div>

    <div class="video-header-user">

        <div style="width: 30%; height: 100%; float:left; text-align: left;">
            <img id="videoUserpicHeader" class="videoUserpicHeader">
        </div>

        <div class="videoUserinfoHeader">

            <div class="videoUsernameHeader" style="font-size: 3vh; line-height: 100%; min-height: 24%; max-height: 40%; overflow: hidden; color: #545454; padding-top: 7%;"></div>

            <div class="videoFacebookHeader" onclick="javascript:window.open('https://facebook.com/profile.php?id=' + facebookAcc, '_blank');return false;">
                <div style="width: 25%; height: 90%; float: left; line-height: 100%">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/facebook_small_icon.png"/>
                </div>
                <a style="font-size: 75%; line-height: 110%; float: left; color: #5a7aca; width: 30%; height: 100%;">Facebook</a>
            </div>

            <div class="videoTwitterHeader" onclick="javascript:window.open('https://twitter.com/intent/user?user_id=' + twitterAcc, '_blank');return false;">
                <div style="width: 25%; height: 90%; float: left; line-height: 100%">
                    <img src="<?php echo get_template_directory_uri(); ?>/images/twitter_small_icon.png"/>
                </div>
                <a style="font-size: 75%; line-height: 110%; float: left; color: #5bbcec; width: 30%; height: 100%;">Twitter</a>
            </div>

            <div style="line-height: 100%">
                <a href="javascript:void(0);" onclick="onUserAccountHeader()" style="color: #af121e; font-size: 1.7vh;">More from this user</a>
            </div>


        </div>
        <div style="width:30%; height: 100%; display: table">
            <div class="followDivHeader" style="vertical-align: middle; display: table-cell;">
                <img src="<?php echo get_template_directory_uri(); ?>/images/follow_btn.png" onclick="onFollowBtn()"   style="cursor: pointer; float: left; height: 30%; width: 95%">

            </div>
        </div>
    </div>

    <div class="video-location" style="cursor: default; text-align: left; ">
        <div class="video-location-live">
            LIVE
        </div>
        <span class="locationText"></span>
    </div>

</div>

<script>

    if ('<?php echo $post_program_id?>' != "")
    {

        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_video = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_video = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_video.onreadystatechange=function()
        {
            if(xmlhttp_video.readyState == 4 && xmlhttp_video.status == 200)
            {
                if (xmlhttp_video.responseText == "error") return;

                program_info = xmlhttp_video.responseText.split("/|/|/");

                VideoPlayer(program_info[0], program_info[13], '<?php echo $post_program_id?>', 40, program_info[9], program_info[14], program_info[11], program_info[3])
            }
        }

        xmlhttp_video.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetProgramInfoById.php?program_id=" + '<?php echo $post_program_id?>' , true);
        xmlhttp_video.send();
    }


    if ('<?php echo $post_user?>' != "")
    {

        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_user = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_user = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_user.onreadystatechange=function()
        {
            if(xmlhttp_user.readyState == 4 && xmlhttp_user.status == 200)
            {
                if (xmlhttp_user.responseText == "error") return;


                g_user_id = xmlhttp_user.responseText;
                showProfile(g_user_id);

            }

        }


        xmlhttp_user.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetUserIdByName.php?username=" + '<?php echo $post_user?>' , true);
        xmlhttp_user.send();
    }

    function openVideoById(programId)
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_video = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_video = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_video.onreadystatechange=function()
        {
            if(xmlhttp_video.readyState == 4 && xmlhttp_video.status == 200)
            {
                if (xmlhttp_video.responseText == "error") return;

                program_info = xmlhttp_video.responseText.split("/|/|/");

                VideoPlayer(program_info[0], program_info[13], programId, 40, program_info[9], program_info[14], program_info[11], program_info[3])
            }
        }

        xmlhttp_video.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetProgramInfoById.php?program_id=" + programId , true);
        xmlhttp_video.send();
    }

    function showProfile(userId)
    {
        objCurrProfilePopup = $('#profile_popup').bPopup({
            onOpen: function() { document.getElementById("profileFrame").src = "<?php echo home_url()?>/profile/?programerID=" + userId },
            onClose: function() {  }
        });

        document.getElementById("profile_popup").style.width = "750px";
        document.getElementById("profile_popup").style.height = "550px";
        document.getElementById("profile_popup").style.borderRadius = "10px";

        window.parent.document.getElementById("profileFrame").style.height = "550px";
        window.parent.document.getElementById("profileFrame").style.width = "750px";
        window.parent.document.getElementById("profileFrame").style.borderRadius = "10px";
        window.parent.document.getElementById("profileFrame").style.backgroundColor = "#f5f5f5";
        var popup_left = screen.width / 2 - (850 / 2);
        var popup_top = screen.height / 2 - (500 / 2 ) - 100;
        window.parent.document.getElementById("profile_popup").style.left = popup_left + "px";
        window.parent.document.getElementById("profile_popup").style.top = popup_top + "px";

        $('body').find('.b-modal').attr('style','');
    }

	
	function videoClose()
    {

        if (g_isLive == "live")
        {
            $('body').find('.video-info').attr('style','bottom: 0px; display: block; color: #efefef; background-color: #5f0000;');
            $('body').find('.cross-multi').attr('style','display:none; background: url(/wp-content/themes/Explorable/images/cross-multi-white.png)');
            $('body').find('.cross-add').attr('style','display:block; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
            $('body').find('.cross-expand').attr('style','display:none; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
        }
        else
        {
            $('body').find('.video-info').attr('style','display: block; bottom: 0px;');
            $('body').find('.cross-multi').attr('style','display:none');
            $('body').find('.cross-add').attr('style','display:block');
            $('body').find('.cross-expand').attr('style','display:none');
        }

        $('body').find('.expandedVideo').attr('style','display: block; z-index: 500; height: 0px; width: 480px; position: fixed; right: 0px; bottom: 0px');


        $('body').find('.et-map-slide').each(function() {
            //$(this).removeClass( ' et-active-map-slide' );
            $(this).attr("style", "display:none; opacity: 0;");
        });

        if (isRecording)
        {
            $('body').find('.video-info').attr('style','display: none; bottom: 0px;');
            streamingStop();
        }

        clearInterval(info_thread);
        program_info = "";
	}
	
	function videoOpen() {
		$('body').find('.et-map-slide.et-active-map-slide').attr('style','display: block; opacity: 1;');

        if (g_isLive == "live")
        {
            $('body').find('.video-info').attr('style','bottom: 4vh7; display: block; color: #efefef; background-color: #5f0000;');
            $('body').find('.cross-multi').attr('style','display: block; background: url(/wp-content/themes/Explorable/images/cross-multi-white.png)');
            $('body').find('.cross-add').attr('style','display: none; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
            $('body').find('.cross-expand').attr('style','display:block; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
        }
        else
        {
            $('body').find('.video-info').attr('style','display: block; bottom: 47vh;');
            $('body').find('.cross-multi').attr('style','display: block;');
            $('body').find('.cross-add').attr('style','display: none;');
            $('body').find('.cross-expand').attr('style','display:block');
        }

        $('body').find('.cross-collapse').attr('style','display:none');

        $('body').find('.expandedVideo').attr('style','display: block; z-index: 500; height: 320px; width: 480px; position: fixed; right: 0px; bottom: 0px');
	}

    function videoExpand() {

        $('body').find('.expandedVideo').attr('style','display: block; z-index: 500; height: 94vh; width: 130vh; position: fixed; right: 0px; bottom: 0px;');


        $('body').find('.video-info').attr('style','display: none;');

        $('body').find('.cross-collapse').attr('style','display:block');

        /*
        $('body').find('.cross-multi').attr('style','display: block;');
      //  $('body').find('.cross-expand').attr('style','display:none');

*/
        $('body').find('.video-bottom').attr('style','display: block;');
        $('body').find('.video-textchat').attr('style','display: block;');
        $('body').find('.video-header-title').attr('style','display: block;');
        $('body').find('.video-header-user').attr('style','display: block;');
        if (g_isLive == "live")
        {
            $('body').find('.video-location-live').attr('style','display: block;');
            $('body').find('.video-location').attr('style','display: block; color: #efefef; background-color: #5f0000');
        }
        else $('body').find('.video-location').attr('style','display: block;');

//        if (isRecording)
//        {
//            var flashContent = document.getElementById("flashContent");
//            flashContent.style.height = "70%";
//            flashContent.style.width = "70%";
//            flashContent.style.right = "30%";
//            flashContent.style.bottom = "10%";
//
//            document.getElementById("flashContent").className = "objectDivExpanded";
//        }
//        else
//        {
            var et_slider_wrapper = document.getElementById("et-slider-wrapper");
            et_slider_wrapper.style.height = "70%";
            et_slider_wrapper.style.width = "70%";
            et_slider_wrapper.style.right = "30%";
            et_slider_wrapper.style.bottom = "10%";

           document.getElementById("objectDiv").className = "objectDivExpanded";


//        }


        var textchat = document.createElement("iframe");

        textchat.id = "textchat";
        textchat.style.width = "100%";
        textchat.style.height = "100%";

        textchat.src = "<?php echo DOMAIN_ROOT
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
                        ?>&roomId=" + g_program_id + "&isUploader=" + is_my_video + "&userId=" + <?php if (isset($_SESSION['user_id'])) echo $_SESSION['user_id']; else echo "\"\"" ?>;

        document.getElementById("textchatDiv").appendChild(textchat);
    }

    function videoCollapse() {
        $('body').find('.expandedVideo').attr('style','display: block; z-index: 500; height: 0px; width: 0px; position: fixed; right: 0px; bottom: 0px');

        if (g_isLive == "live")
        {
            $('body').find('.video-info').attr('style','display: block; color: #efefef; background-color: #5f0000;');
            $('body').find('.cross-multi').attr('style','display: block; background: url(/wp-content/themes/Explorable/images/cross-multi-white.png)');
            $('body').find('.cross-add').attr('style','display: none; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
            $('body').find('.cross-expand').attr('style','display:block; background: url(/wp-content/themes/Explorable/images/cross-plus-white.png)');
            $('body').find('.video-title-live').attr('style','display: block');
        }
        else
        {
            $('body').find('.video-info').attr('style','display: block;');
            $('body').find('.cross-multi').attr('style','display: block;');
            $('body').find('.cross-expand').attr('style','display: block');
            $('body').find('.video-title-live').attr('style','display: none');
        }

        $('body').find('.cross-add').attr('style','display:none');

        $('body').find('.et-map-post').attr('style','');

//        var et_slider_wrapper = document.getElementById("et-slider-wrapper");
//        et_slider_wrapper.style.height = "320px";
//        et_slider_wrapper.style.width = "480px";
//        et_slider_wrapper.style.right = "0";
//        et_slider_wrapper.style.bottom = "0";

        document.getElementById("objectDiv").className = "objectDiv";

        /*
        $('body').find('.cross-collapse').attr('style','display:none');
        $('body').find('.video-bottom').attr('style','display: none;');
*/
        $('body').find('.video-bottom').attr('style','display: none;');
        $('body').find('.video-textchat').attr('style','display: none;');
        $('body').find('.video-header-title').attr('style','display: none;');
        $('body').find('.video-header-user').attr('style','display: none;');
        $('body').find('.video-location').attr('style','display: none;');



        document.getElementById("textchatDiv").removeChild(document.getElementById("textchat"));
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

    function onFollowBtn()
    {
        <?php
            if(!isset($_SESSION['user_id']))
            {?>
            //alert("Please, do login!");
            fn_register();
            return;
        <?php } ?>
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
                if(xmlhttp.responseText.indexOf("exist.") > -1)
                {
                    alert("You are already follow this user");
                    return;
                }
                else if (xmlhttp.responseText.indexOf("mysql") > -1)
                {
                    alert("Error. Please, try again later");
                    return;
                }
                else
                {
                    alert("Followed!");
                    return;
                }
            }

        }

        xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxBecomeFan.php?user_id=" + g_user_id + "&loginUserId=" + "<?php echo $_SESSION['user_id']?>" + "&Need=" ,true);
        xmlhttp.send();
    }

    function onUserAccountHeader()
    {
        <?php
            if(!isset($_SESSION['user_id']))
            {?>
        //alert("Please, do login!");
        fn_register();
        return;
        <?php } ?>

        /*
        var programer = document.getElementById("programerID");
        programer.value = g_user_id;
        var program =  document.getElementById("programId");
        program.value = g_program_id;
        document.aform.action = "<?php echo home_url()?>/profile/";
        document.aform.submit();
        */
        showProfile(g_user_id);
    }

    function onVoteClick(vote)
    {
//        if (mutex) return;
//        else mutex = true;

        <?php
        if(!isset($_SESSION['user_id']))
        {?>
        //alert("Please,do login!");
        fn_register();
        return;
        <?php }
        ?>


        //
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
            {
                getMyVote(g_program_id, myUserId);
            }

//            mutex = false;

        }

        var voteDiff;
        if (vote) voteDiff = 1;
        else voteDiff = -1;

        xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxRegVote.php?program_id=" + g_program_id + "&user_id=" + myUserId + "&vote=" + voteDiff , true);
        xmlhttp.send();
    }

    function getIfVoted()
    {
        <?php
        if(!isset($_SESSION['user_id']))
        {?>
        return;
        <?php }
        ?>


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
                if(xmlhttp.responseText == "failed")
                {
                   return;
                }
                else if (xmlhttp.responseText == "1")
                {
                    document.getElementById("thumbUpClickedImg").style.display = "block";
                    document.getElementById("thumbUpImg").style.display = "none";
                }
                else if (xmlhttp.responseText == "-1")
                {
                    document.getElementById("thumbDownClickedImg").style.display = "block";
                    document.getElementById("thumbDownImg").style.display = "none";
                }
            }
        }


        xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetVote.php?program_id=" + g_program_id + "&user_id=" + myUserId, true);
        xmlhttp.send();
    }

    function getMyVote(programId, userId)
    {
       //
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            getMyVoteReq=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            getMyVoteReq=new ActiveXObject("Microsoft.XMLHTTP");
        }
        getMyVoteReq.onreadystatechange=function()
        {
            if(getMyVoteReq.readyState==4 && getMyVoteReq.status==200)
            {
                if(getMyVoteReq.responseText == "failed")
                {
                    document.getElementById("thumbDownClickedImg").style.display = "none";
                    document.getElementById("thumbDownImg").style.display = "block";
                    document.getElementById("thumbUpClickedImg").style.display = "none";
                    document.getElementById("thumbUpImg").style.display = "block";
                }
                else if (getMyVoteReq.responseText == "1")
                {
                    document.getElementById("thumbDownClickedImg").style.display = "none";
                    document.getElementById("thumbDownImg").style.display = "block";
                    document.getElementById("thumbUpClickedImg").style.display = "block";
                    document.getElementById("thumbUpImg").style.display = "none";
                }
                else if (getMyVoteReq.responseText == "-1")
                {
                    document.getElementById("thumbDownClickedImg").style.display = "block";
                    document.getElementById("thumbDownImg").style.display = "none";
                    document.getElementById("thumbUpClickedImg").style.display = "none";
                    document.getElementById("thumbUpImg").style.display = "block";
                }
                getProgramVotes(programId);
                getProgramNegVotes(programId);
            }
        }


        getMyVoteReq.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetVote.php?program_id=" + programId + "&user_id=" + userId, true);
        getMyVoteReq.send();
    }

    function getProgramVotes(programId)
    {
        //
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            getProgramVotesReq=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            getProgramVotesReq=new ActiveXObject("Microsoft.XMLHTTP");
        }
        getProgramVotesReq.onreadystatechange=function()
        {
            if(getProgramVotesReq.readyState==4 && getProgramVotesReq.status==200)
            {
                g_votes = getProgramVotesReq.responseText;
                $('body').find(".votesDiv").text(numeral(g_votes).format('0,0'));
            }
        }

        getProgramVotesReq.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetVotes.php?program_id=" + programId, true);
        getProgramVotesReq.send();
    }

    function getProgramNegVotes(programId)
    {
        //
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            getProgramNegVotesReq=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            getProgramNegVotesReq=new ActiveXObject("Microsoft.XMLHTTP");
        }
        getProgramNegVotesReq.onreadystatechange=function()
        {
            if(getProgramNegVotesReq.readyState==4 && getProgramNegVotesReq.status==200)
            {
                g_neg_votes = getProgramNegVotesReq.responseText;
                $('body').find(".negVotesDiv").text(numeral(g_neg_votes).format('0,0'));
            }
        }

        getProgramNegVotesReq.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetNegVotes.php?program_id=" + programId, true);
        getProgramNegVotesReq.send();
    }

    function reportVideo()
    {
        <?php
        if(!isset($_SESSION['user_id']))
        {?>
        //alert("Please, do login!");
        fn_register();
        return;
        <?php }
        ?>


        objCurrPopup = $('#report_popup').bPopup({
            onOpen: function() {
                document.getElementById("reportFrame").src = "<?php echo home_url()?>/report_frame?program_id=" + g_program_id + "&reporter=" + myUserId + "&reported=" + g_user_id + "&videoURL=" + g_videoURL ;
            },
            onClose: function() {  }
        });

        document.getElementById("report_popup").style.width = "250px";
        document.getElementById("report_popup").style.height = "350px";

        window.parent.document.getElementById("reportFrame").style.width = "250px";
        window.parent.document.getElementById("reportFrame").style.height = "350px";

        var popup_left = screen.width / 2 - (250 / 2);
        var popup_top = screen.height / 2 - (350 / 2 ) - 100 + 37.5;
        window.parent.document.getElementById("report_popup").style.left = popup_left + "px";
        window.parent.document.getElementById("report_popup").style.top = popup_top + "px";

        $('body').find('.b-modal').attr('style','');
    }

    function suspendUser()
    {
        <?php
        if(!isset($_SESSION['user_id']))
        {?>
        //alert("Please, do login!");
        fn_register();
        return;
        <?php }
        ?>


        objCurrPopup = $('#suspend_popup').bPopup({
            onOpen: function() { document.getElementById("suspendFrame").src = "<?php echo home_url()?>/suspend_frame?program_id=" + g_program_id + "&user_id=" + g_user_id},
            onClose: function() {  }
        });

        document.getElementById("suspend_popup").style.width = "380px";
        document.getElementById("suspend_popup").style.height = "240px";

        window.parent.document.getElementById("suspendFrame").style.width = "380px";
        window.parent.document.getElementById("suspendFrame").style.height = "240px";

        var popup_left = screen.width / 2 - (200 / 2);
        var popup_top = screen.height / 2 - (380 / 2 ) - 100 + 37.5;
        window.parent.document.getElementById("suspend_popup").style.left = popup_left + "px";
        window.parent.document.getElementById("suspend_popup").style.top = popup_top + "px";
    }

    function setLocation(latitude, longtitude)
    {
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
                if (xmlhttp.responseText)
                {
                    var json = JSON.parse(xmlhttp.responseText);
                    $('body').find(".locationText").text(json.results[0].formatted_address);
                }

            }
        }


        xmlhttp.open("GET","http://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," + longtitude + "&language=en", true);
        xmlhttp.send();
    }
</script>

<script type="text/javascript">

    var startDate;

    function showRecordingPage(channel_id, program_id, title, description, tags, imgurl, clientlocation)
    {
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


        document.getElementById("expandedVideo").appendChild(et_slider_wrapper);

        document.getElementById("expandedVideo").style.display = "block";

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

        objectDiv.id = "objectDiv";
        objectDiv.className = "objectDiv";             				// flash player loading
        //objectDiv.style.width = "100%";

        //objectDiv.style.height = "320px";
        objectDiv.style.bottom = 0 + "px";
        objectDiv.style.backgroundColor = "black";

        et_map_slide.appendChild(objectDiv);

        var liveUrl = channel_id + "_" + program_id + "_" + <?php if (isset($_SESSION['user_id'])) echo $_SESSION['user_id']; else echo "\"\"" ?>;
        document.getElementById("main-header").style.position = "relative";



        var object = document.createElement("object");

        object.className = "objectVideo";
        object.type = "application/x-shockwave-flash";
        object.name = "H264_Encoder";
        object.data = "<?php echo get_template_directory_uri()?>/H264_Encoder.swf";
        object.id = "H264_Encoder";
        object.style.visibility = "visible";
        objectDiv.appendChild(object);

        var param = document.createElement("param");

        param.name = "wmode";
        param.value = "opaque";
        object.appendChild(param);

        var param = document.createElement("param");

        param.name = "quality";
        param.value = "high";
        object.appendChild(param);

        var param = document.createElement("param");

        param.name = "bgcolor";
        param.value = "#000000";
        object.appendChild(param);

        var param = document.createElement("param");

        param.name = "allowscriptaccess";
        param.value = "sameDomain";
        object.appendChild(param);

        var param = document.createElement("param");

        param.name = "align";
        param.value = "middle";
        object.appendChild(param);

        var param = document.createElement("param");

        param.name = "flashvars";
        param.value = 'URL=' + '<?php echo RTMP_WOWZA?>' + 'livestream&CHNL=mp4:' + liveUrl + '&WDTH=320&HGHT=240&FPS=15&BW=90000&QLT=90&INV=30';
        object.appendChild(param);


        g_isLive = "live";


        isRecording = true;
        g_program_id = program_id;

        g_videoURL = encodeURIComponent(window.location.host.toString() + "/?programId=" + g_program_id);
        document.getElementById("facebookShareButton").href = "https://www.facebook.com/sharer.php?u=" + g_videoURL;
        document.getElementById("twitterShareButton").href = "https://twitter.com/intent/tweet?text=Such an interesting video on YepLive!" + "&url=http://" + g_videoURL;
        document.getElementById("gplusShareButton").href = "https://plus.google.com/share?url=" + g_videoURL;

        is_my_video = true;

        $('body').find(".time-ago-span").text("");
        $('body').find(".locationText").text(clientlocation);
        $('body').find(".videoTitleHeader").text(title);
        $('body').find(".videoDescHeader").text(description);
        $('body').find(".videoTagsHeader").text(tags);
        $('body').find(".video-name").text(title);
        if ((imgurl == "") || (imgurl == undefined)) document.getElementById("videoThumbnailHeader").src = "/wp-content/themes/Explorable/images/myprofile.png";
        else document.getElementById("videoThumbnailHeader").src = imgurl;


        startDate = new Date(0);
        var org_start_date = "<?php echo $start_time?>";

        if("<?php echo $start_time?>" != "")
        {
            startDate.setTime(fn_dateConvert("<?php echo $start_time?>"));
//            timeCount();
        }

        menuLoad_thread = setInterval("is_menu_load_func();",100);

        $('body').find(".timeAgoDiv").text("00:00");
        $('body').find(".negVotesDiv").text("0");
        $('body').find(".votesDiv").text("0");
        $('body').find(".bottomVideoViewersDiv").text("0 viewers");
        $('body').find(".videoTimeHeader").text("");
        $('body').find('.videoUserpicHeader').attr('style','display: none;');
        $('body').find('.videoUserinfoHeader').attr('style','display: none;');
        $('body').find('.followDivHeader').attr('style','display: none;');
        //$('body').find('.reportDiv').attr('style','display: none;');
        $('body').find('.stopRecordImg').attr('style','display: block;');

        videoExpand();
    }


        function setSuccess (msg)
        {

            if ( msg == "success" ) {

                var flash = document.getElementById("H264_Encoder");
                var camNo = document.getElementById('camNo').value;
                var micNo = document.getElementById('micNo').value;
                flash.selectedCam(camNo);
                flash.selectedMic(micNo);
                flash.streamRecordingStart();
                isRecordingStarted = true;
                stop_record_timer_thread = setInterval("stopRecord()", 242000);
            }
        }

        function selectedMicRetMsg (msg)
        {
            console.log(msg);
        }

        function recordingtest ()
        {
            var flash = document.getElementById("H264_Encoder");
            flash.cameraMirror();
        }



        function streamingStop ()
        {
            if (typeof timer_thread != 'undefined') clearInterval(timer_thread);
            if (typeof stop_record_timer_thread != 'undefined') clearInterval(stop_record_timer_thread);
            if (typeof info_thread != 'undefined') clearInterval(info_thread);
            if (typeof getStartTime_Thread != 'undefined') clearInterval(getStartTime_Thread);
            if (typeof menuLoad_thread != 'undefined') clearInterval(menuLoad_thread);

            isRecording = false;
            period = 0;
            if (isRecordingStarted)
            {
                isRecordingStarted = false;
                alert("You have finished your live stream. You may view it below");
            }
            return;
        }
</script>

    <script type="text/javascript">
        var period = 0;

        function timeCount()
        {

// code to limit the recording time
//            if(nowTime.getTime() - startTime >= 5 * 3600 * 1000)
//            {
//                var obj = document.getElementById("H264_Encoder");
//
//                if(obj)
//                {
//
//                    clearInterval(polling_thread);
//                }
//                return;
//            }
<!---->
<!--            if (window.XMLHttpRequest)-->
<!--            {-->
<!--                xmlhttpServerTime=new XMLHttpRequest();-->
<!--            }-->
<!--            else-->
<!--            {-->
<!--                xmlhttpServerTime=new ActiveXObject("Microsoft.XMLHTTP");-->
<!--            }-->
<!--            xmlhttpServerTime.onreadystatechange=function()-->
<!--            {-->
<!--                if(xmlhttpServerTime.readyState==4 && xmlhttpServerTime.status==200)-->
<!--                {-->
<!--                    $('body').find(".timeAgoDiv").text(moment(startDate, "YYYY-MM-DD hh:mm:ss").from(xmlhttpServerTime.responseText, "YYYY-MM-DD hh:mm:ss"));-->
<!--                    $('body').find(".time-ago-span").text(moment(startDate, "YYYY-MM-DD hh:mm:ss").from(xmlhttpServerTime.responseText, "YYYY-MM-DD hh:mm:ss"));-->
<!--                }-->
<!--            }-->
<!--            xmlhttpServerTime.open("GET","--><?php //echo get_template_directory_uri() ?><!--/ajaxGetServerTime.php",true);-->
<!--            xmlhttpServerTime.send();-->
<!---->
<!---->
<!--            setTimeout("timeCount();",1000);-->

            var nowTime = new Date();
//            var startTime = startDate.getTime();
                //code to limit the recording time
//            if(nowTime.getTime() - startTime >= 5 * 3600 * 1000)
//            {
//                var obj = document.getElementById("H264_Encoder");
//
//                if(obj)
//                {
//                    document.getElementById("timeduration").innerHTML = "00:00:00";
//                    obj.parentNode.removeChild(obj);
//                    clearInterval(polling_thread);
//                }
//                return;
//            }

//            var period = (nowTime.getTime() + (nowTime.getTimezoneOffset() * 60 * 1000) - moment(startDate).valueOf()) / 1000;

            period ++;

          //var hours = parseInt(period  /  3600);
            var minutes = period  % 3600;
            seconds = parseInt(minutes % 60);
            minutes = parseInt(minutes / 60);

       /*     if(hours.toString().length == 1)
                hours = "0" + hours;*/
            if(minutes.toString().length == 1)
                minutes = "0" + minutes;
            if(seconds.toString().length == 1)
                seconds = "0" + seconds;
            $('body').find(".timeAgoDiv").text(minutes + ":" + seconds);
            $('body').find(".time-ago-span").text(minutes + ":" + seconds);

            if (period > 240) stopRecord();


            delete nowTime;
            nowTime = null;
            //setTimeout("timeCount();",1000);
        }

        var menuLoad_thread;
        var polling_thread;
        var votes_thread;
        var timer_thread;
        var stop_record_timer_thread;
        var getStartTime_Thread;

        function is_menu_load_func()
        {
            if(menuLoadFlag == true)
            {
                clearInterval(menuLoad_thread);
                polling_thread = setInterval("polling();",10000);
                votes_thread = setInterval("getProgramAllVotes(g_program_id);",10000);


                if("<?php echo $start_time?>" == "")
                {
                    getStartTime_Thread = setInterval("get_start_time();",1000);

                }
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
                    if(xmlhttp.responseText != "")
                    {
                        clearInterval(getStartTime_Thread);

                        startDate = xmlhttp.responseText;
                        $('body').find(".videoTimeHeader").text(moment(xmlhttp.responseText).format('h:mmA - MMMM Do, YYYY'));

                        timer_thread = setInterval("timeCount();",1000);
                    }

                }
            }

            xmlhttp.open("GET","<?php echo get_template_directory_uri() ?>/get_start_time.php?program_id=" + g_program_id,true);
            xmlhttp.send();
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
                    $('body').find(".bottomVideoViewersDiv").text(numeral(xmlhttp.responseText).format('0,0') + " viewers");
                    $('body').find(".visit-count").text(numeral(xmlhttp.responseText).format('0,0') + " viewers");
                }
            }



            xmlhttp.open("GET","<?php echo get_template_directory_uri() ?>/connect_count_polling.php?program_id=" + g_program_id,true);
            xmlhttp.send();
        }

    function getProgramAllVotes(programId)
    {
        getProgramVotes(programId);
        getProgramNegVotes(programId);
    }

    function getTimeAgo(startDate)
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttpServerTime=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttpServerTime=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttpServerTime.onreadystatechange=function()
        {
            if(xmlhttpServerTime.readyState==4 && xmlhttpServerTime.status==200)
            {
                $('body').find(".timeAgoDiv").text(moment(startDate, "YYYY-MM-DD hh:mm:ss").from(xmlhttpServerTime.responseText));
                $('body').find(".time-ago-span").text(moment(startDate, "YYYY-MM-DD hh:mm:ss").from(xmlhttpServerTime.responseText));
            }
        }
        xmlhttpServerTime.open("GET","<?php echo get_template_directory_uri() ?>/ajaxGetServerTime.php",true);
        xmlhttpServerTime.send();
    }

        function getServerTime()
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttpGetServerTime=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttpGetServerTime=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttpGetServerTime.onreadystatechange=function()
            {
                if(xmlhttpGetServerTime.readyState==4 && xmlhttpGetServerTime.status==200)
                {
                    serverTime = xmlhttpGetServerTime.responseText;
                }
            }
            xmlhttpGetServerTime.open("GET","<?php echo get_template_directory_uri() ?>/ajaxGetServerTime.php",true);
            xmlhttpGetServerTime.send();
        }

        function stopRecord()
        {
            var flash = document.getElementById("H264_Encoder");
            flash.streamRecordingEnd();
            if (document.getElementById("textchatDiv").contains(document.getElementById("textchat"))) videoCollapse();
            videoClose();
        }

    </script>


<script type="text/javascript">

    function selectVideosOnMap(search_tags, filter)
    {
        if ((search_tags.indexOf(' ') != -1)||(search_tags.indexOf(',,') != -1))
        {
            //alert("Please, input tags in the following format: tag1,tag2,tag3 (without spaces)");
            return;
        }
        search_user_id = "";
        tags = search_tags;
        search_filter = filter;

        (function($){

            updateMarkers();

        })(jQuery)
    }

    function selectVideosOnMapByUserId(user_id, filter)
    {
        tags = "";
        search_user_id = user_id;
        search_filter = filter;

        (function($){

            updateMarkers();

        })(jQuery)
    }

    function selectVideosOnMapByFilter(filter)
    {
        tags = "";
        search_user_id = "";
        search_filter = filter;

        (function($){

            updateMarkers();

        })(jQuery)
    }

    function testFunction()
    {
        (function($){

            getMapBounds();

        })(jQuery)
    }



</script>


<?php get_footer();?>

