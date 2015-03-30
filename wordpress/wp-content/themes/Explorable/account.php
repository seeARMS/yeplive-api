<?php
/*

Template name: account

*/
    if(!isset($_SESSION)) session_start();

    if($_SESSION['user_id'] == "")
    {
        echo "please login!";
        exit();
    }
    get_header();

    $programerID = $_GET['programerID'];

    require_once(ABSPATH . WPINC . '/registration.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
    include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
    global $wpdb, $user_ID;

	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		echo "connect error";
		exit;
	}
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "select_db error";
		exit;
	}

    $sql_query = "SELECT uy.user_id, uy.picture_path, uy.facebook_id, uy.twitter_img,
                    CASE WHEN uy.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = uy.wp_user_id)
                        WHEN uy.facebook_name IS NOT NULL THEN uy.facebook_name
                        ELSE uy.twitter_name END user_login FROM wp_user_yep uy WHERE uy.user_id = '" . $programerID . "'";

    $result = mysql_query($sql_query);

    $userInfo = array("user_id", "picture_path", "user_name");

    $row = mysql_fetch_array($result);


    $userInfo['user_id'] = $row['user_id'];
    $facebook_id = $row['facebook_id'];
    $twitter_img = $row['twitter_img'];
    if($row['picture_path'] != "")
        $userInfo['picture_path'] = home_url() . $row['picture_path'];
    else if($facebook_id != "")
        $userInfo['picture_path'] =  "https://graph.facebook.com/" . $facebook_id . "/picture/?type=large";
    else if ($twitter_img != "")
        $userInfo['picture_path'] = $twitter_img;
    else
        $userInfo['picture_path']  = "/wp-content/themes/Explorable/images/profile-thumb.png";

    $userInfo['user_name'] = $row['user_login'];

    mysql_free_result($result);
	
	mysql_close($link);


	
	$serverCurrGmtTime = getdate(); // server gmt time
	$serverCurrLocalTime = time() * 1000; // server current time
?>

<script src='<?php echo get_template_directory_uri()?>/js/perfect-scrollbar.jquery.min.js'></script>
<link rel='stylesheet' href='<?php echo get_template_directory_uri()?>/css/perfect-scrollbar.min.css' />

<script>
    document.body.style.overflow = "hidden";
    document.body.style.position = "relative";


    document.getElementById("main-header").style.display = "none";
    document.getElementById("headerShadowDiv").style.display = "none";

    document.body.style.background = "";
    document.body.style.backgroundImage = "";

    function removeAvatar(obj)
    {
        if ('<?php echo $_SESSION['user_id']?>' != "")
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
                    if (xmlhttp_video.responseText == "failed") return;
                    else
                    {
                        obj.style.display = "none";
                        document.getElementById("avatarImage").src = "/wp-content/themes/Explorable/images/profile-thumb.png";
                        parent.window.document.getElementById("myProfileImage").src = "/wp-content/themes/Explorable/images/profile-thumb.png";
                    }
                }
            }
            xmlhttp_video.open("GET","<?php echo get_template_directory_uri()?>/ajaxRemoveAvatar.php?user_id=" + '<?php echo $_SESSION['user_id']?>' , true);
            xmlhttp_video.send();
        }
    }



</script>
<br>
<form name="aform" method="post" style="height:100%">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="liveVod" name="liveVod" value="" >
<input type="hidden" id="programId" name="programId" value="" >

<div id="container" align="center" style="overflow: hidden; position:relative; height:100%;">
    <div style="height:50px; width:100%">
        <h1 style="font-size:27pt; font-family:'Myriad Pro'; color:black; cursor:default;">
            My Account
        </h1>
    </div>

    <div  align="center" style="width: 100%; height:210px;
    		 background-color: red;
		 background: -moz-linear-gradient( top, red, #c10000, #ae0000 );
         background: -webkit-linear-gradient( top, red, #c10000, #ae0000 );
         background: -o-linear-gradient( top, red, #c10000, #ae0000 );
         background: -khtml-linear-gradient( top, red, #c10000, #ae0000 );
         background: -ms-linear-gradient( top, red, #c10000, #ae0000 );
         background: linear-gradient( top, red, #c10000, #ae0000 );" >
        <div style="width:150px; height:150px; background-color: #dddddd; border-radius:75px; position:relative; top:30px; cursor:pointer;">
            <img id="avatarImage" style=" width:150px; height:150px; background-color: #dddddd; border-radius:75px; position:relative; " src="<?php echo $userInfo['picture_path']?>">
            <iframe frameborder="no" marginwidth="0px" marginheight="0px"  src="<?php echo home_url()?>/avatarUpload/" style="width:100%; height:100%; position:absolute; top:0;left:0;" scrolling="no"></iframe>
        </div>
        <img style="width: 120px; position:absolute; right:10px; top:220px; opacity:0.6;" src="<?php echo get_template_directory_uri()?>/images/player_watermark.png"/>
    </div>

	<div id="tableDiv" style="overflow:hidden; width:100%; height:100%; position:relative;" align="center" >
		<table id="contentTbl" style=" width:80%; margin-top:20px;" border="0">
			<colgroup>
				<col width="50%">
				<col width="50%">
			</colgroup>
            <tr>
                <td style="padding-right:20px;">
                    <img style="float:left;" src="<?php echo get_template_directory_uri()?>/images/account_nick_icon.png"/>
                    <div style="width:80%; height:100%; float:right;">
                        <span style="font-size:13pt; line-height:30px; text-overflow: ellipsis; overflow: hidden; display: block; white-space:nowrap;"><?php echo $userInfo['user_name']?></span>
                        <hr align="left" width="100%" size="1" color="black" />
                    </div>

                </td>

                <td style="padding-left:20px;">
                    <img style="float:left;" src="<?php echo get_template_directory_uri()?>/images/account_password_icon.png"/>
                    <div style="width:80%; height:100%; float:right;">
                        <span style="font-size:13pt; line-height:30px;">*********</span>
                        <hr align="left" width="100%" size="1" color="black" />
                    </div>
                </td>
            </tr>
		</table>
	</div>
</div>
</form>