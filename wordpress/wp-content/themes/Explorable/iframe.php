<?php
/*

Template name: fileUpload

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
	float: left;
	margin-bottom: 5px;
	width: 50%;
	opacity: 0;
	-webkit-box-align: baseline;
	color: inherit;
	text-align: start;
	-webkit-appearance: initial;
	padding-top: 8px;
	padding-left: 266px;
	background-color: initial;
	border: initial;
	border-image: initial;
	-webkit-rtl-ordering: logical;
	-webkit-user-select: text;
	cursor: pointer;
}
.image_uploader {
	position: relative;
	margin-left: 3px;
	height: 30px;
	text-align: left;
	float: left;
	display: inline-block;
	color: #cc6666;
	margin-right: 2.5%;
	width: 22%;
	font-weight: bold;
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
</script>
<body onload="oninit();">
<form name="aform" method="post" action="<?php echo home_url()?>/fileupload/" enctype="multipart/form-data">
	<div style="height:40px;">
		<label class="image_uploader" style="width:269px; height:41px; border: 2px solid #dddddd; background: url('<?php echo get_template_directory_uri()?>/images/upload-thumnail.png');" >
		
		<input type="file" name="file" id="file" onchange="document.aform.submit();">
		
		</label> 
		<span id="charPath" name="charPath" style="margin-left:15px;margin-top:5px;"></span> 
		<span id="fileSize" name="fileSize" style="margin-top:5px;"></span>
	</div>
</form>
</body>
<?php
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
		$uploadedfile = $_FILES['file'];
		
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		if($movefile)
		{
			//echo $movefile['file'] . "<br>";
			//echo $movefile['url'] . "<br>";
			//echo $movefile['type'] . "<br>";
			//echo $_FILES["file"]["name"];
			//echo "<script>document.getElementById('charPath').innerHTML = '" . $_FILES["file"]["name"] . "'</script>";
			//echo "<script>document.getElementById('fileSize').innerHTML = '(" . round(($_FILES["file"]["size"] / 1024),2) . "KB)'</script>";
			echo "<script>parent.window.document.getElementById('charImg').src = '" . $movefile['url'] . "'</script>";
			echo "<script>parent.window.document.getElementById('imgUrl').value = '" . $movefile['url'] . "'</script>"; 
		}
		else
		{
			echo "failed";
		}
		/*
		if ( $movefile ) {
			echo "File is valid, and was successfully uploaded.\n";
			var_dump( $movefile);
		} else {
			echo "Possible file upload attack!\n";
		}*/	
	}
	else
	{
		
	}
	
?>
</html>