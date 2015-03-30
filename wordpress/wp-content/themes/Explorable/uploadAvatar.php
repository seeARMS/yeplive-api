<?php

/*

Template name: avatarUpload

*/

?>


<html>
<style>
input[type="text"]:hover, input[type="password"]:hover, input[type="email"]:hover,input[type="file"]:hover , textarea:hover, select:hover {
		border: 1px solid #91bde5;
}
input[type="text"], input[type="password"], input[type="email"], input[type="file"] ,textarea {
		font-weight: 300;
}
input[type="text"], input[type="password"],input[type="file"] ,input[type="email"], textarea, select {
		-webkit-background-clip: padding;
		-moz-background-clip: padding;
		background-clip: padding-box;
		-webkit-transition: box-shadow ease-out;
		-webkit-transition-delay: 0.3s;
		-moz-transition: box-shadow ease-out 0.3s;
		-o-transition: box-shadow ease-out 0.3s;
		transition: box-shadow ease-out 0.3s;
		padding: 10px;
		height: auto;
		border: 1px solid #b6b6b6;
		border-radius: 3px;
		color: #404040;
		font-size: 16px;
		line-height: 16px;
		-webkit-transition: border-color ease-out;
		-webkit-transition-delay: 0.3s;
		-moz-transition: border-color ease-out 0.3s;
		-o-transition: border-color ease-out 0.3s;
		transition: border-color ease-out 0.3s;
		}
button, input, select, textarea {
		font-size: 100%;
		vertical-align: middle;
		margin: 0;
}
body, button, input, select, textarea {
		font-family: 'Helvetica Neue',Arial,Helvetica, Verdana, sans-serif;
}

input[type="text"], textarea, keygen, select, button, isindex {
		margin: 0em;
		font: -webkit-small-control;
		color: initial;
		letter-spacing: normal;
		word-spacing: normal;
		text-transform: none;
		text-indent: 0px;
		text-shadow: none;
		display: inline-block;
		text-align: start;
}
input[type="text"], textarea, keygen, select, button, isindex, meter, progress {
		-webkit-writing-mode: horizontal-tb;
}
input[type="file"] {
	/*float: left;*/
	/*margin-bottom: 5px;*/
	width: 50%;
	opacity: 0;
	/*-webkit-box-align: baseline;*/
	color: inherit;
	/*text-align: start;*/
	-webkit-appearance: initial;
	/*padding-top: 8px;*/
	/*padding-left: 266px;*/
	background-color: initial;
	/*border: initial;*/
	/*border-image: initial;*/
	/*-webkit-rtl-ordering: logical;*/
	/*-webkit-user-select: text;*/
	cursor: pointer;
}
.image_uploader {
	/*position: relative;*/
	/*margin-left: 3px;*/
	/*height: 30px;*/
	/*text-align: left;*/
	float: left;
	display: inline-block;
	color: white;
	/*margin-right: 2.5%;*/
	/*width: 22%;*/
	/*font-weight: bold;*/
	line-height: 16px;
}
.image_uploader:hover {
	opacity: 0.6;
}

</style>
<script>
function oninit()
{
	//onchange="document.getElementById('charImg').src = this.value; document.getElementById('charPath').value = this.value;"
}
function onlabeldown(obj)
{
	//obj.style.background = "url('<?php echo get_template_directory_uri()?>/images/upload-thumnail.png') no-repeat";
}
 function onlabelup(obj)
 {
	 //obj.style.background = "url('<?php echo get_template_directory_uri()?>/images/upload-thumnail.png') no-repeat";
 }

    function onMouseOver(obj)
    {
        obj.style.opacity = "0.5";
        document.getElementById("changePictureSpan").style.visibility = "visible";
    }
    function onMouseOut(obj)
    {
        obj.style.opacity = "0";
        document.getElementById("changePictureSpan").style.visibility = "hidden";
    }
</script>
<body onload="oninit();">
<form name="aform" method="post" action="<?php echo home_url()?>/avatarUpload/" enctype="multipart/form-data">
	<div>
		<label class="image_uploader" style=" width:100%; height:100%; opacity:0; background:black; border-radius:75px; cursor:pointer;" onmouseover="onMouseOver(this);" onmouseout="onMouseOut(this);" >
		    <input type="file" name="file" id="file" style="width:100%; height:100%; border-radius: 50%;" onchange="document.aform.submit();">
            <span style="
                position: absolute;
                top: 70px;
                left: 5px;
                color: white;"
                id="changePictureSpan">CHANGE PICTURE</span>
		</label> 
		<span id="charPath" name="charPath" style="margin-left:15px;margin-top:5px;"></span> 
		<span id="fileSize" name="fileSize" style="margin-top:5px;"></span>
	</div>
</form>
</body>
<?php

if(!isset($_SESSION)) session_start();

include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/ImageManipulator/ImageManipulator.php');

	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$extension = end(explode(".", $_FILES["file"]["name"]));
	if ((($_FILES["file"]["type"] == "image/gif")
			|| ($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/jpg")
			|| ($_FILES["file"]["type"] == "image/pjpeg")
			|| ($_FILES["file"]["type"] == "image/x-png")
			|| ($_FILES["file"]["type"] == "image/png")
			|| ($_FILES["file"]["type"] == "image/gif"))
			&& in_array($extension, $allowedExts))
	{

        $manipulator = new ImageManipulator( $_FILES["file"]["tmp_name"]);

        $width  = $manipulator->getWidth();
        $height = $manipulator->getHeight();
        if ($width > $height)
        {
            $x1 = ($width - $height) / 2;
            $y1 = 0;
            $x2 = $height + $x1;
            $y2 = $height;
        }
        else
        {
            $x1 = 0;
            $y1 = ($height - $width) / 2;
            $x2 = $width;
            $y2 = $width + $y1;
        }
        $new_image = $manipulator->crop($x1, $y1, $x2, $y2);
        $new_image = $manipulator->resample(200, 200);

        $unique_name = GUID() . ".jpg";
        $imgurl = "/wp-content/uploads/thumbnails/" . $unique_name;

        $manipulator->save(THUMBNAILS_PATH . $unique_name);

        $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
        if(!$link)
        {
            echo "mysql_connect error";
            exit;
        }
        if(!mysql_select_db(DB_NAME,$link))
        {
            echo "mysql_select_db error";
            exit;
        }

        $sql_query = "UPDATE wp_user_yep SET picture_path = '" . $imgurl . "' WHERE user_id='" . $_SESSION['user_id'] . "'";
        $result = mysql_query($sql_query);
        $row = mysql_fetch_array($result);

        if ($result)
        {
            echo "<script>parent.window.document.getElementById('avatarImage').src = '" . home_url() . $imgurl . "'</script>";
            //echo "<script>parent.window.document.getElementById('removeAvatarButton').style.display = 'block'</script>";
            //echo "<script>parent.window.document.getElementById('imgUrl').value = '" . $movefile['url'] . "'</script>";
            echo "<script>parent.parent.window.document.getElementById('myProfileImage').src = '" . home_url() . $imgurl . "'</script>";
        }
        else echo "failed";
	}

    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
	
?>
</html>