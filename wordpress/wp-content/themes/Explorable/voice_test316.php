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
?>
	<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/swfobject.js"></script>
	<script type="text/javascript" src='<?php echo get_template_directory_uri()?>/js/swfobjectPlayer.js'></script>
		<script type="text/javascript">
            // For version detection, set to min. required Flash Player version, or 0 (or 0.0.0), for no version detection.
            
            var swfVersionStr = "11.1.0";
            // To use express install, set to playerProductInstall.swf, otherwise the empty string. 
            var xiSwfUrlStr = "<?php echo get_template_directory_uri()?>/playerProductInstall.swf";
            var flashvars = {"URL": "rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream","CHNL": "mp4:<?php echo $randValue?>","WDTH": 320,"HGHT": 240,"FPS": 15,"BW":90000,"QLT":90,"INV":30};
            var params = {};
            params.quality = "high";
            params.bgcolor = "#000000";
            params.allowscriptaccess = "sameDomain";
            params.allowfullscreen = "true";
            var attributes = {};
            attributes.id = "H264_Encoder";
            attributes.name = "H264_Encoder";
            attributes.align = "middle";
            swfobject.embedSWF(
                "<?php echo get_template_directory_uri()?>/H264_Encoder.swf", "flashContent", 
                "400", "300", 
                swfVersionStr, xiSwfUrlStr, 
                flashvars, params, attributes);
            // JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.
            swfobject.createCSS("#flashContent", "display:block;text-align:left;");
        </script>
        <script type="text/javascript"> 
		function onInfo(v){
			H264_Encoder.doPublish(v);
		}
		</script>		
<br>
<form name="aform" method="post" >
<input type="hidden" value="" id="emapInfo" name="emapInfo">
<input type="hidden" value="" id="focusValue" name="focusValue">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">

<div align="center" id="rootDiv">
	<table>
		<tr>
			<td>
				<div style="border-radius:20px; width:400px; height:300px; padding-top:30px; padding-left:40px; padding-right:40px; padding-bottom:30px; background-color: #000000;">
					<div id="flashContent" >
				            <p>
				                To view this page ensure that Adobe Flash Player version 
				                11.1.0 or greater is installed. 
				            </p>
				            <script type="text/javascript"> 
				                var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
				                document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
				                                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
				            </script> 
			       	</div>	
				</div>	
			</td>
			<td>
				<div style="border-radius:20px; width:400px; height:300px; padding-top:30px; padding-left:40px; padding-right:40px; padding-bottom:30px; background-color: #000000;">
					<div id="video_player">
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<br>
				<input type="button" value="Click here to test." onclick="onPlayTest();" style="letter-spacing:3px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png'); color: #ffffff; border-radius:10px; width:300px; height:50px;">
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
			<br>
			<input type="button" value="Continue" onclick="location.href='<?php echo home_url()?>/add-program/'" style="letter-spacing:3px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png'); color: #ffffff; border-radius:10px; width:200px; height:50px;">
			</td>
		</tr>
	</table>
	
	
</div>
<br>

</form>
<script>
function onPlayTest()
{
					var parameters = {
						    src: "rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream/<?php echo $randValue?>"
						    , autoPlay: "true"
						    , streamType: "live"
					        , bgColor: "#000000"
						};
						swfobject.embedSWF(
					        "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback.swf"
					        , "video_player"
					        , 400
					        , 300
					        , "10.1.0"
					        , "<?php echo get_template_directory_uri()?>/expressInstall.swf"
					        , parameters
					        , {
					            allowFullScreen: "true",            
					            wmode: "direct"
					        }
					        , {
					            name: "StrobeMediaPlayback"
					        }
					    );
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
get_footer(); 
?>