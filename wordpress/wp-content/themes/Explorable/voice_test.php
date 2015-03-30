<?php
/*

Template name: voice_test

*/
if(!isset($_SESSION)) session_start();

if($_SESSION['user_id'] == "")
{
	echo "please login!";
	exit();
}	
get_header();
	$randValue = rand(1000, 9000);
	//$randValue = 1000;
?>
	<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/swfobject.js"></script>
	<script type="text/javascript" src='<?php echo get_template_directory_uri()?>/js/swfobjectPlayer.js'></script>
	
		
		<script type="text/javascript">
            document.getElementById("main-header").style.display = "none";
            document.getElementById("headerShadowDiv").style.display = "none";

            // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
            var swfVersionStr = "11.1.0";
            // To use express install, set to playerProductInstall.swf, otherwise the empty string. 
            var xiSwfUrlStr = "<?php echo get_template_directory_uri()?>/playerProductInstall.swf";
            var flashvars = {"URL": "<?php echo RTMP_WOWZA?>livestream","CHNL": "mp4:<?php echo $randValue?>","WDTH": 418,"HGHT": 290,"FPS": 15,"BW":90000,"QLT":90,"INV":30};
            var params = {};
            params.quality = "high";
            params.bgcolor = "#000000";
            params.wmode = "opaque";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            var attributes = {};
            attributes.id = "H264_Encoder";
            attributes.name = "H264_Encoder";
            attributes.align = "center";
            swfobject.embedSWF(
                "<?php echo get_template_directory_uri()?>/H264_Encoder.swf", "flashContent", 
                "418", "290", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes);
            // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
            swfobject.createCSS("#flashContent", "display:block;text-align:center;");
        </script>
         <script type="text/javascript"> 
			function onInfo(v){
				H264_Encoder.doPublish(v);
			}
		</script>
        <script type="text/javascript"> 

/*********************************************************   initialization  *********************************************/
		var _fcameraFail = false;
		var fCameraExist = false;
		var fRecordingAvailable = false;
		var fRecordingAgain = true;
		var selectedCamNumber = 0;
		
		$(document).ready(function(){
			document.getElementById("mirrorStatus").value = "non-mirror";
			if(document.getElementById("next-page").href.indexOf("?") > -1){
				document.getElementById("next-page").href = document.getElementById("next-page").href.split("?")[1];
			}
			document.getElementById("next-page").href += "?mirrorStatus=" + document.getElementById("mirrorStatus").value + "_" + selectedCamNumber; 
			document.getElementById("micVolume").value = 40;
			
			
			document.getElementById("camera-detect-form").style.opacity = 0.2;
			document.getElementById("mic-detect-form").style.opacity  = 0.2;
			document.getElementById("mic-drop-list").style.opacity  = 0;
			document.getElementById("camera-drop-list").style.opacity  = 0;
			document.getElementById("mirror-button").style.opacity  = 0.2;
			document.getElementById("test-recording-button").style.opacity  = 0.2;
//			document.getElementById("mic-level-volume").style.opacity  = 0.2; 
			document.getElementById("next-page").style.opacity  = 0; 

			var browserWidth = $(window).width();
			document.getElementById("main-header").style.minWidth = browserWidth + "px";

			document.getElementById("main-header").style.position = "relative";

		})

		
		function onInfo(v){
			H264_Encoder.doPublish(v);
		}

		function cameraAllow () {
			var flash = document.getElementById("H264_Encoder");
			var msg = "detectOK";
			flash.detectSuccess(msg);
		}

		function volumeValue (volume) {
			var temp = 0;
			if ( volume > 350 )
				volume = 350;
			else if ( volume < 100 )
				volume = 100;
				 
			temp = parseInt(volume / 5 );
			var flash = document.getElementById("H264_Encoder");
			flash.volValue(temp);
			
		}
		
		function retDetectConfirm ( msg ) {
			if ( msg == "ok" ) {
				/*var video = document.querySelector("#videoElement");
			    
			    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;
			     
			    if (navigator.getUserMedia) {       
			        navigator.getUserMedia({video: true}, handleVideo, videoError);
			    }

			    function handleVideo(stream) {
			        video.src = window.URL.createObjectURL(stream);
			    }
			     
			    function videoError(e) {
			        // do something
			    } */

			    //volumeValue(120);	
			}
		}

		var micActivityLevel = 0;
		
		function micActivityStart ( msg ) {
																// if mic Activity is exist , event sent from flash
			micActivityLevel = parseInt(msg * 3.5);
			if ( micActivityLevel > 350 ) 
				micActivityLevel = 350;
			//micActivityLevel = msg;
			//console.log(micActivityLevel);
			if (micActivityLevel < 20 ) {
				micActivityLevel = 20;
			}
			document.getElementById("mic-level-box").style.width = micActivityLevel + "px";
		}

		/* window.setInterval(function() {
			document.getElementById("mic-level-box").style.width = micActivityLevel + "px";
			//console.log(micActivityLevel);
		} , 400); */	
		
		function setSuccess (msg) {
			
//			var browserWidth = $(window).width() + 20 ;
//			document.getElementById("main-header").style.minWidth = browserWidth + "px";
			
			$('body').find("#micList").attr("style","display:none");
			$('body').find("#cameraList").attr("style","display:none");
			if ( msg == "success" ) {
				document.getElementById("camera-detect-form").style.opacity = 1;
				document.getElementById("mic-detect-form").style.opacity  = 1;
				document.getElementById("mic-drop-list").style.opacity  = 1;
				document.getElementById("camera-drop-list").style.opacity  = 1;
				document.getElementById("mirror-button").style.opacity  = 1;
				document.getElementById("test-recording-button").style.opacity  = 1;
				document.getElementById("mic-level-bar").style.opacity  = 1; 
				document.getElementById("next-page").style.opacity  = 1; 

				fRecordingAvailable = true;
			}
		}
		function micInfo (msg) {															// message after camera is accept
			console.log(msg);
			//alert(msg);
		}

		 
		function selectedCamera (obj) {															// if camera is choosen in camera list 
			var selectedCamNo = $(obj).find("#selectedCamera").val();
			var selectedCamName = $(obj).find("#camName").val();
			document.getElementById("detected-camera-name").innerHTML = selectedCamName;

			var flash = document.getElementById("H264_Encoder");								// if camera is choosen , event sending from javascrit to flash
			flash.selectedCam(selectedCamNo);
            window.parent.document.getElementById("camNo").value = selectedCamNo;

			selectedCamNumber = selectedCamNo;
			if(document.getElementById("next-page").href.indexOf("?") > -1){
				document.getElementById("next-page").href +=  document.getElementById("next-page").href.split("?")[0] + selectedCamNumber; 
			}
			
			cameraList_show();
		}

		function selectedCamRetMsg (msg) {
			//console.log(msg);
		}
		
		function selectedMicRetMsg (msg) {
			console.log(msg);
		}
		
		function cameraDetecting (cameraNames) {												// if camera exist , event sent from flash.
			
			document.getElementById("detected-camera-no").style.display = "none";
			if ( cameraNames.length > 1 ) {
				for(var i=0 ; i<cameraNames.length ; i++) {
					Cloneobj = $('<div class="detected-cameras">' + cameraNames[i] + '<input type="hidden" id="selectedCamera" value="' + i + '"><input type="hidden" id="camName" value="' + cameraNames[i] + '"></div>').clone();
					Cloneobj.attr("id" , "name" + i);
					$("#cameraList").append(Cloneobj);

					Cloneobj.click( function(event){
						selectedCamera(this);
					});
				}
				document.getElementById("detected-camera-info").style.display = "block";
				document.getElementById("detected-camera-name").innerHTML = cameraNames[0];
				document.getElementById("camera-drop-list").style.display = "block";
				
			}
			else if ( cameraNames.length == 1 ) {
				document.getElementById("detected-camera-info").style.display = "block";
				document.getElementById("detected-camera-name").innerHTML = cameraNames[0];
				document.getElementById("camera-drop-list").style.display = "none";
			}
			cameraAllow();
			returnMsg = "ok";
			return returnMsg;
		}
		
		function cameraNo (msg) {																	// if camera is no , event from flash.
			document.getElementById("detected-camera-no").style.display = "block";
			document.getElementById("camera-drop-list").style.display = "none";
			fCameraExist = true;
			alert("There is no a connected camera");
		}
		
		function micDetectingNo (msg) {													// if mic is no , event from flash.  *** now , non-running in flash.
			//alert(20);
		}

		function selectedMic (obj) {
			var micNo = $(obj).find("#micNo").val();
			var micName = $(obj).find("#micName").val();
			document.getElementById("detected-mic-name").innerHTML = micName;

			var flash = document.getElementById("H264_Encoder");								// if camera is choosen , event sending from javascrit to flash
			flash.selectedMic(micNo);

            window.parent.document.getElementById("micNo").value = micNo;

			micList_show();
		}
		
		function micDetecting (micNames) {												// if mic is exist , event sent from flash
			//alert(micNames);

			if ( micNames == "no" ) {
				document.getElementById("detected-mic-no").style.display = "block";
			}
			if ( micNames.length > 1 ) {
				
				for(var i=0 ; i<micNames.length ; i++) {
						Cloneobj = $('<div class="detected-mics">' + micNames[i] + '<input type="hidden" id="micNo" value="' + i + '"><input type="hidden" id="micName" value="' + micNames[i] + '"></div>').clone();
						Cloneobj.attr("id","name" + i);
						$("#micList").append(Cloneobj);
						
						Cloneobj.click(function (event){
						  	selectedMic(this);			  	
					  	});
				}
				document.getElementById("detected-mic-info").style.display = "block";
				document.getElementById("detected-mic-name").innerHTML = micNames[1];
				document.getElementById("mic-drop-list").style.display = "block";
			}
			else if ( micNames.length == 1 ) {
				document.getElementById("detected-mic-info").style.display = "block";
				document.getElementById("detected-mic-name").innerHTML = micNames[0];
				document.getElementById("mic-drop-list").style.display = "none";
			}
			
			returnMsg = "ok";
			return returnMsg;
		} 
		
		function cameraList_show () {
			$('body').find("#cameraList").slideToggle("show");
		}
		
		function micList_show () {
			$('body').find("#micList").slideToggle("show");
		}
		function micList_hide () {
			$('body').find("#micList").slideToggle("hide");
		}
		
		function cameraList_hide () {
			$('body').find("#cameraList").slideToggle("hide");
		}

		$(window).resize(function(){
			
			/*var browserWidth = parseInt($(window).width()) + 30;
			document.getElementById("main-header").style.minWidth = browserWidth + "px";
			console.log(document.getElementById("main-header").style.minWidth);*/
		});
</script>
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>		 
		<script src="<?php echo get_template_directory_uri()?>/js/simple-slider.min.js"></script>		
		<link href="<?php echo get_template_directory_uri()?>/css/simple-slider.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo get_template_directory_uri()?>/css/simple-slider-volume.css" rel="stylesheet" type="text/css" /> 
		<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/voice_test.css" />

<form name="aform" method="post" >
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="mirrorStatus" name="mirrorStatus" value="">
<input type="hidden" id="micVolume" name="micVolume" value="">

<div id="voice-test-page" class="voice-test-page" style="min-width: 1024px; max-width: 1366px; margin-left: auto; margin-right: auto;">
<div align="center" id="rootDiv" class="voice-test-form">
	<div style="margin: 0px 45px; background-color:#fff; height:555px; position: relative;">
		<div>
			<img class="voice-test-title" src="<?php echo get_template_directory_uri() ?>/images/voice-test-title.png">
		</div>
		<div class="player-screen">
			<div id="flash-player-screen" class="player-position">
				<img src="<?php echo get_template_directory_uri()?>/images/player-screen.png" class="player-screen-img">
				<div id="player-form" class="player-form">
					<div id="flashContent" >
<!-- 		            <p>
			                To view this page ensure that Adobe Flash Player version 
			                11.1.0 or greater is installed. 
			            </p>
			            <script type="text/javascript"> 
			                var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
			                document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
			                                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
			            </script>  -->	 
			       	</div>
			     </div>
			     <span id="recording-status" class="recording-status">REC</span>
			     <img src="<?php echo get_template_directory_uri()?>/images/voice_test/recording-status.png" id="recording-status-icon" class="recording-status-icon">
		    </div>

	       	<div id="recording-bar" class="recording-bar">
	       		<div id="recording-progress-bar" class="recording-progress-bar"></div>
	       	</div>
	       	<div id="player-button" class="player-button" onclick="buttonPlayer();">
 	     		<img src="<?php echo get_template_directory_uri()?>/images/voice_test/player-button.png" id="player-button-img" class="player-button-img" >  
	       	</div>
	       	
		</div>

		<div style="width: 50%; float: right;">
			<div id="camera-detect-form" class="camera-detect">
				<img src="<?php echo get_template_directory_uri()?>/images/camera-detect.png" class="camera-detect-form-img">
				<div class="cemara-info">
					
					<div id="detected-camera-info" class="detected-camera-info">
<!-- 			<div class="detected-player"> 
							<video autoplay="true" id="videoElement" class="videoElement" ></video>
						</div>  -->						
						<div id="detected-camera-name" class="detected-camera-name" align="center"></div>
						<div class="detected-camera-checked">
							<img src="<?php echo get_template_directory_uri()?>/images/voice_test/detect-check.png">
						</div>
					</div>
					<img src="<?php echo get_template_directory_uri()?>/images/voice_test/drop-arrow.png" class="camera-drop-list" id="camera-drop-list" onclick="cameraList_show();">
					<div id="detected-camera-no" class="detected-camera-no">
						<img src="<?php echo get_template_directory_uri()?>/images/voice_test/detect-cancel.png" style="width: 30px;">
					</div>
				</div>
				<div id="cameraList" class="detected-camera-list">
				</div>
			</div>
			
			<div id="mic-detect-form" class="mic-detect">
				<img src="<?php echo get_template_directory_uri()?>/images/mic-detect.png" class="mic-detect-form-img">
				<div class="mic-info">
					<div id="detected-mic-info" class="detected-mic-info"> 
						<div id="detected-mic-name" class="detected-mic-name"></div>
						<div id="detected-mic-checked" class="detected-mic-checked">
							<img src="<?php echo get_template_directory_uri()?>/images/voice_test/detect-check.png">
						</div>
					</div>
					<img src="<?php echo get_template_directory_uri()?>/images/voice_test/drop-arrow.png" class="mic-drop-list" id="mic-drop-list" onclick="micList_show();">
					<div id="detected-mic-no" class="detected-mic-no">
						<img src="<?php echo get_template_directory_uri()?>/images/voice_test/detect-cancel.png" style="width: 30px;">
					</div>
				</div>
				<div id="micList" class="detected-mic-list">
				</div>
			</div>
			
			<div id="mirror-button" class="mirror-button" >
			 	<img src="<?php echo get_template_directory_uri() ?>/images/mirror-button.png" class="mirror-button-img" style="width: 100%; height: 38px; cursor: pointer;" onclick="onMirror();">  		
			</div>
			<div id="test-recording-button" class="test-recording-button">
				<img src="<?php echo get_template_directory_uri() ?>/images/test-recording-button.png" class="test-recording-button-img" style="width: 97%; height:38px; cursor: pointer;" onclick="onRecording();">
			</div>
			
		</div>
		
		
		<div id="mic-level-bar" class="mic-level-bar" >
			<img src="<?php echo get_template_directory_uri()?>/images/voice_test/mic-level-volume.png" class="mic-level-img">          
	    	<div class="mic-level-form">
	    		<div id="mic-level-box" class="mic-level-box"></div>																						<!--  AF121E -->
	    	</div>
	    	<div class="mic-volume-form">
	    		<input type="text" data-slider="true" value="0.3" data-slider-highlight="true" data-slider-theme="volume">			<!--  D9D5D5 -->
	    		<input type="hidden" id="volumeValue" name="volumeValue" value="" >
	    	</div>
		</div>
		
		<a href="<?php echo home_url() ?>/add-program/" id="next-page" class="next-page">
			<img src="<?php echo get_template_directory_uri()?>/images/next-button.png" class="next-page-button" align="right" > 
		</a>
		
		<div id="stream-player" style="position: absolute; top: 99px; left: 27px;">
			<div id="recording-reform" style="position: absolute; width: 100%; height: 37px; top: 253px; left: 0px; ">
				<input type="button" id="recording-close" name="recording-close" value="CLOSE" class="recording-close" onclick="recordingClose();">
				<input type="button" id="recording-again" name="recording-again" value="TEST RECORDING AGAIN" class="recording-again" onclick="recordingAgain();">
			</div>
		</div>
	</div>
</div>
</div>
<br>

</form>


<script>

	var mirrorFlag = false;
	var recordingMiddle = false;
	
	function onPlayback()
	{
		var playerBack = document.getElementById("stream-player");
		var strobePlayer = document.createElement("div");
		strobePlayer.id = "video_player";
		playerBack.appendChild(strobePlayer);
		

			var swfVersionStr = "11.1.0";
            // To use express install, set to playerProductInstall.swf, otherwise the empty string. 
            var xiSwfUrlStr = "<?php echo get_template_directory_uri()?>/playerProductInstall1.swf";

            var params = {};

            if ( mirrorFlag == true )  // if mirror 
                params.flashvars = "src=<?php echo RTMP_WOWZA?>vod/mp4:<?php echo $randValue?>_aac.mp4&autoPlay=true&streamType=vod&bgColor=#000000&enableStageVideo=false&controlBarMode=none";

            else 						// if mirror not
                params.flashvars = "src=<?php echo RTMP_WOWZA?>vod/mp4:<?php echo $randValue?>_aac.mp4&autoPlay=true&streamType=vod&bgColor=#000000&enableStageVideo=false&controlBarMode=none";
        	

            params.quality = "high";
            params.bgcolor = "#000000";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            params.controlBarMode = "none";
            params.wmode = "opaque";

            var attributes = {};
            attributes.id = "Strobemedia";
            attributes.name = "Strobemedia";
            attributes.align = "center";
            
            swfobject.embedSWF(
                "<?php echo get_template_directory_uri()?>/StrobeMediaPlaybackWrapper.swf", "video_player",
                "418", "290", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes);
            
            swfobject.createCSS("#video_player", "display:block;text-align:center;");

//            var StrobeMediaPlay = document.getElementById("Strobemedia");
//            StrobeMediaPlay.strobeStart("strobeplayerback"); 	
			
	}

	function strobeUrl (msg) {
		console.log(msg);
	}
	function channelName(msg) {
		
		if ( msg == "stop" ) {
			document.getElementById("player-button").style.display = "block";
			document.getElementById("player-button").style.zIndex = 1;

			recordingMiddle = true;
			
		}
		console.log(msg);
	}
	
/*	function onJavaScriptBridgeCreated(playerId)
    {
        player = document.getElementById(playerId);
        var state = player.getState();
        console.log(state)
                  
    } */

	function onRecording () { 
		if ( fRecordingAvailable == false || fRecordingAgain == false || recordingMiddle == true)	
			return;

		fRecordingAgain = false;							// Recording flag setting;  if recording is start
		
		var flash = document.getElementById("H264_Encoder");								// if camera is choosen , event sending from javascrit to flash
		flash.streamRecordingStart();

		document.getElementById("camera-detect-form").style.opacity = 0.2;
		document.getElementById("mic-detect-form").style.opacity  = 0.2;
		document.getElementById("mic-drop-list").style.opacity  = 0;
		document.getElementById("camera-drop-list").style.opacity  = 0;
		document.getElementById("mirror-button").style.opacity  = 0.2;
		document.getElementById("test-recording-button").style.opacity  = 0.2;
		document.getElementById("mic-level-bar").style.opacity  = 0.2; 	
		document.getElementById("next-page").style.opacity  = 0; 	
		
		document.getElementById("recording-bar").style.display = "block";
		document.getElementById("recording-progress-bar").style.display = "block";
		
		timerId = setInterval(progressBar,80);
		document.getElementById("recording-status").style.display = "block";
		//setInterval(RTimerId,2000);
	}
	
	var timeId;
	var timeInterval = 0;
	
	function progressBar ( ) {
		
	     if(timeInterval <= 99) {
	    	 timeInterval++;
	         document.getElementById("recording-progress-bar").style.width = timeInterval + "%";	
	     } else {
	     		clearInterval(timerId);
	     		timerId = null;
	     		timeInterval = 0;
	     		
	     		document.getElementById("recording-status").style.display = "none";
	     		
	          	var flash = document.getElementById("H264_Encoder");								// if camera is choosen , event sending from javascrit to flash
	  			flash.streamRecordingEnd();

	  			setTimeout(	function() {
	  				recordingPlay(); 
	  			}, 1000);

	  			fRecordingAgain = true; 									// recording flag setting : if recording is end 
	     }
	}
	
	function recordingPlay () {

		document.getElementById("recording-progress-bar").style.width = "0%";						// progress bar is disable
		document.getElementById("recording-bar").style.display = "none";
		
		document.getElementById("recording-close").style.display = "block";   //  close and recording again button showing
		document.getElementById("recording-again").style.display = "block"; 
		
		document.getElementById("player-button").style.display = "block";
		document.getElementById("player-button").style.zIndex = 0;
	}
	function buttonPlayer() {
		document.getElementById("player-button").style.display = "none";
		document.getElementById("player-button").style.zIndex = 0;

		$('body').find("#stream-player").find("object").remove();
		onPlayback();
	}

	function recordingClose () {
		$('body').find("#stream-player").find("object").remove();									// strobe player is disable

		document.getElementById("player-button").style.display = "none";
		document.getElementById("player-button").style.zIndex = 0;
		
		document.getElementById("camera-detect-form").style.opacity = 1;
		document.getElementById("mic-detect-form").style.opacity  = 1;
		document.getElementById("mic-drop-list").style.opacity  = 1;
		document.getElementById("camera-drop-list").style.opacity  = 1;
		document.getElementById("mirror-button").style.opacity  = 1;
		document.getElementById("test-recording-button").style.opacity  = 1;
		document.getElementById("mic-level-bar").style.opacity  = 1; 
		document.getElementById("next-page").style.opacity  = 1;

//		document.getElementById("recording-progress-bar").style.width = "0%";						// progress bar is disable
//		document.getElementById("recording-bar").style.display = "none";
		document.getElementById("recording-progress-bar").style.display = "none"; 

		document.getElementById("recording-close").style.display = "none";     						// Close and AGAIN button is disable
		document.getElementById("recording-again").style.display = "none";

		fRecordingAgain = true;
		recordingMiddle = false;
	}
	
	function recordingAgain() {

		if ( fRecordingAgain == false )
			return;

		document.getElementById("player-button").style.display = "none";
		document.getElementById("player-button").style.zIndex = 0;
		 
		recordingClose();

		onRecording();
	}
	
	function onMirror () {
		if ( mirrorFlag == false ) {
			mirrorFlag = true;
			document.getElementById("mirrorStatus").value = "mirror";
			if(document.getElementById("next-page").href.indexOf("?") > -1){
				document.getElementById("next-page").href = document.getElementById("next-page").href.split("?")[0];
			}
			document.getElementById("next-page").href += "?mirrorStatus=" + document.getElementById("mirrorStatus").value + "_" + selectedCamNumber;  
		}
		else {
			mirrorFlag = false;
			document.getElementById("mirrorStatus").value = "non-mirror";
		}
		var flash = document.getElementById("H264_Encoder");								// if camera is choosen , event sending from javascrit to flash
		flash.cameraMirror(selectedCamNumber);

//        var StrobeMediaPlay = document.getElementById("Strobemedia");
//        StrobeMediaPlay.mirrorRecording("strobeplayerback");
	}

</script>

<script type="text/javascript">

/*    var video = document.querySelector("#vid");
    

    var onCameraFail = function (e) {
        console.log('Camera did not work.', e);
    };

    

    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    window.URL = window.URL || window.webkitURL;
    navigator.getUserMedia({video:true,audio:true}, function (stream) {
        video.src = window.URL.createObjectURL(stream);
    
    }, onCameraFail);

  */  

</script>



<?php
//get_footer(); 
?>