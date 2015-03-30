<?php
/*

Template name: profile

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
	
	$sql_query = "select panTbl.user_nicename,
						 panTbl.facebook_name,
						 panTbl.facebook_id,
						 panTbl.twitter_name,
						 panTbl.twitter_img,
						 panTbl.user_email,
						 panTbl.user_id,
						 panTbl.pan_id,
						 panTbl.picture_path,
						 userTbl.pan_id loginUserId 
				 from (select  			wu.user_nicename ,
										lu.facebook_name,
										lu.facebook_id,
										lu.twitter_name, 
										lu.twitter_img,
										wu.user_email ,
										lup.user_id,
										lup.pan_id , 
										lu.picture_path 
									from 
										wp_user_yep lu 
									inner join 
										wp_user_pans lup 
									on 
										lu.user_id = lup.pan_id
									left join 
										wp_users wu 
									on 
										lu.wp_user_id = wu.ID   
								  where 
										 lup.user_id = '" . $programerID . "') panTbl
				left join 
						(select user_id,pan_id from wp_user_pans where pan_id = '" . $_SESSION['user_id'] . "') userTbl
				on 
							panTbl.pan_id = userTbl.user_id
				where 
						panTbl.pan_id != '" . $programerID . "'
								";
	$result = mysql_query($sql_query);
	
	$panInfo = array("pan_id" => array(),"picture_path" => array(),"user_name" => array(),"loginUserId" => array(), "facebook_id" => array(),
			"facebook_name" => array() , "twitter_name" => array() , "twitter_img" => array());
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		$panInfo['pan_id'][$i] = $row['pan_id'];
		
		if($row['picture_path'] != "")
			$panInfo['picture_path'][$i] = home_url() . $row['picture_path'];
		else
			$panInfo['picture_path'][$i] = "";
		
		$panInfo['loginUserId'][$i] = $row['loginUserId'];
		$panInfo['user_name'][$i] = $row['user_nicename'];
		$panInfo['facebook_name'][$i] = $row['facebook_name'];
		$panInfo['facebook_id'][$i] = $row['facebook_id'];
		$panInfo['twitter_name'][$i] = $row['twitter_name'];
		$panInfo['twitter_img'][$i] = $row['twitter_img'];
		$i++;
	}
	mysql_free_result($result);
	
	mysql_select_db(DB_NAME,$link);
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "select_db error";
		exit;
	}
	
	$sql_query = "select	favoriteTbl.user_nicename,
							 favoriteTbl.facebook_name,
							 favoriteTbl.facebook_id,
							 favoriteTbl.twitter_name,
							 favoriteTbl.twitter_img,
							 favoriteTbl.user_email,
							 favoriteTbl.user_id,
							 favoriteTbl.pan_id,
							 favoriteTbl.picture_path,
							 userTbl.pan_id loginUserId  
					from (select wu.user_nicename,
									lu.facebook_name,
									lu.facebook_id,
									lu.twitter_name,
									lu.twitter_img,
								 	wu.user_email,
								 	lup.user_id, 
									lup.pan_id,
									lu.picture_path  
						  from
									wp_user_pans lup
						  inner join   
									wp_user_yep lu 
						  on 
									lup.user_id = lu.user_id
						  left join   
									wp_users wu 
						  on 
									lu.wp_user_id = wu.ID
					   	  where 
								lup.pan_id = '" . $programerID . "') favoriteTbl
					left join
						(select user_id,pan_id from wp_user_pans where pan_id = '" . $_SESSION['user_id'] . "') userTbl
						on 
									favoriteTbl.user_id = userTbl.user_id
						where favoriteTbl.user_id != '" . $programerID . "'
								";
	$result = mysql_query($sql_query);
	
	$favoriteInfo = array("user_id" => array(),"picture_path" => array(),"user_name" => array(),"loginUserId" => array(), "facebook_id" => array(),
			"facebook_name" => array(), "twitter_name" => array() , "twitter_img" => array());
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		$favoriteInfo["user_id"][$i] = $row['user_id'];
		if($row['picture_path'] != "")
			$favoriteInfo["picture_path"][$i] = home_url() . $row['picture_path'];
		else
			$favoriteInfo["picture_path"][$i] = "";
		$favoriteInfo["loginUserId"][$i] = $row['loginUserId'];
		$favoriteInfo["user_name"][$i] = $row['user_nicename'];
		$favoriteInfo["facebook_name"][$i] = $row['facebook_name'];
		$favoriteInfo["facebook_id"][$i] = $row['facebook_id'];
		$favoriteInfo["twitter_name"][$i] = $row['twitter_name'];
		$favoriteInfo["twitter_img"][$i] = $row['twitter_img'];
		$i++;
	}
	mysql_free_result($result);
	
	$sql_query = "select 
						wu.user_login,
						lu.facebook_name,
						lu.facebook_id,
						lu.twitter_name,
						lu.twitter_img,
						lu.picture_path ,
						lp.description,
						lp.user_id,
						lp.program_id
					from 
						wp_user_yep lu 
					left join 
						wp_users wu 
					on 
						lu.wp_user_id = wu.ID 
					inner join
						wp_program lp
					on
						lp.user_id = lu.user_id
					where
						lp.user_id = '" . $programerID ."'";
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "select_db error";
		exit;
	}
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}
	$row = mysql_fetch_array($result);
	
	$user_login = $row['user_login'];
	$facebook_name = $row['facebook_name'];
	$facebook_id = $row['facebook_id'];
	$twitter_name = $row['twitter_name'];
	$twitter_img = $row['twitter_img'];
	$description = $row['description'];
	if($row['picture_path'] != "")
        $row['picture_path'] = home_url() . $row['picture_path'];
	else if($facebook_name != "")
        $row['picture_path'] =  "https://graph.facebook.com/" . $facebook_id . "/picture/?type=large";
	else if ($twitter_img != "")
        $row['picture_path'] = $twitter_img;
    else $row['picture_path']  = "/wp-content/themes/Explorable/images/profile-thumb.png";

	mysql_free_result($result);




    $sql_query = "SELECT uy.picture_path, uy.facebook_id, uy.twitter_img,
                     CASE WHEN uy.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = uy.wp_user_id)
                                        WHEN uy.facebook_name IS NOT NULL THEN uy.facebook_name
                                        ELSE uy.twitter_name END user_login
                                        FROM wp_user_yep uy WHERE uy.user_id = " . $programerID;

    if(!mysql_select_db(DB_NAME,$link))
    {
       // echo "select_db error";
       // exit;
    }
    $result = mysql_query($sql_query);
    if(!$result)
    {
       // echo "mysql_query error";
       // exit;
    }
    $row = mysql_fetch_array($result);

    $username = $row['user_login'];
    $facebook_id = $row['facebook_id'];
    $twitter_img = $row['twitter_img'];
    if($row['picture_path'] != "")
        $userpic = home_url() . $row['picture_path'];
    else if($facebook_id != "")
        $userpic =  "https://graph.facebook.com/" . $facebook_id . "/picture/?type=large";
    else if ($twitter_img != "")
        $userpic = $twitter_img;
    else
        $userpic = "/wp-content/themes/Explorable/images/profile-thumb.png";

    mysql_free_result($result);



	
	$current_time = date("Y-m-d H:i:s");
	$sql_query = "select  lp.program_id,
	                      lp.title,
						  lp.image_path,
						  lp.description,
						  lp.start_time,
						  lp.connect_count,
						  lp.duration
				  from
						  wp_program lp
				  inner join
						  wp_user_yep lu
				  on
						  lp.user_id = lu.user_id
				  left join
						  wp_users wu
				  on
						  lu.wp_user_id = wu.ID
			where
				  lp.end_time is not null and lp.start_time is not null
		    and
				  lp.user_id = '" . $programerID . "'

            ORDER BY start_time DESC";
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	$result = mysql_query($sql_query);
	if(!$result)
	{
		echo "mysql_query error";
		exit;
	}
	
	$broadcastInfo = array("program_id" => array() , "title" => array() , "image_path" => array(), "description" => array() , "time_ago" => array(), "connect_count" => array() , "duration" => array() );

	$i = 0;
	while($row = mysql_fetch_array($result))
	{
        $broadcastInfo["program_id"][$i] = $row["program_id"];
		$broadcastInfo["title"][$i] = $row["title"];
		if($row["image_path"] != "")
			$broadcastInfo["image_path"][$i] = home_url() . $row["image_path"];
		else 
			$broadcastInfo["image_path"][$i] = home_url() . "/wp-content/themes/Explorable/images/thumbnail_stub.png";
		$broadcastInfo["description"][$i] = $row["description"];
		$broadcastInfo["time_ago"][$i] = nicetime($row["start_time"]);
        $broadcastInfo["connect_count"][$i] = $row["connect_count"];
        $broadcastInfo["duration"][$i] = $row["duration"];
		
		$i++;
	}

	
	mysql_free_result($result);
	
	if(!mysql_select_db(DB_NAME,$link))
	{
		echo "mysql_select_db error";
		exit;
	}
	$sql_query = "select * from wp_user_pans  where user_id ='" . $programerID . "' and pan_id = '" . $_SESSION['user_id'] . "'";
	$result = mysql_query($sql_query);
	
	$row = mysql_fetch_array($result);
	
	$loginPanId = $row["pan_id"];
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

    $(document).ready(getIsFollow);

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

var panInfo = new Array();
var favoriteInfo = new Array();
var broadcastInfo = new Array();


<?php
	for($i = 0; $i < count($panInfo["pan_id"]); $i++)
	{
		
			
	?>
		panInfo[ panInfo.length ] = {
					pan_id : "<?php echo $panInfo["pan_id"][$i]?>",
					picture_path : "<?php echo $panInfo["picture_path"][$i]?>",
					loginUserId : "<?php echo $panInfo["loginUserId"][$i]?>",
					user_name : "<?php echo $panInfo["user_name"][$i]?>",
					facebook_name : "<?php echo $panInfo["facebook_name"][$i]?>",
					facebook_id : "<?php echo $panInfo["facebook_id"][$i]?>",
					twitter_name : "<?php echo $panInfo["twitter_name"][$i]?>",
					twitter_img : "<?php echo $panInfo["twitter_img"][$i]?>"
				};
<?php }?>
	

<?php
		for($i = 0; $i < count($favoriteInfo["user_id"]); $i++)
		{
			
		?>
			favoriteInfo[ favoriteInfo.length ] = {
						user_id : "<?php echo $favoriteInfo["user_id"][$i]?>",
						picture_path : "<?php echo $favoriteInfo["picture_path"][$i]?>",
						loginUserId : "<?php echo $favoriteInfo["loginUserId"][$i]?>",
						user_name : "<?php echo $favoriteInfo["user_name"][$i]?>",
						facebook_name : "<?php echo $favoriteInfo["facebook_name"][$i]?>",
						facebook_id : "<?php echo $favoriteInfo["facebook_id"][$i]?>",
						twitter_name : "<?php echo $favoriteInfo["twitter_name"][$i]?>",
						twitter_img : "<?php echo $favoriteInfo["twitter_img"][$i]?>"
					};
	<?php }?>
	
	<?php
			for($i = 0; $i < count($broadcastInfo["title"]); $i++)
			{?>
				broadcastInfo[ broadcastInfo.length ] = {
                            program_id : "<?php echo $broadcastInfo["program_id"][$i]?>",
							title : "<?php echo $broadcastInfo["title"][$i]?>",
							image_path : "<?php echo $broadcastInfo["image_path"][$i]?>",
							description : "<?php echo $broadcastInfo["description"][$i]?>",
                            time_ago : "<?php echo $broadcastInfo["time_ago"][$i]?>",
                            connect_count : "<?php echo $broadcastInfo["connect_count"][$i]?>",
                            duration : "<?php echo substr($broadcastInfo["duration"][$i], 0, 5)?>"
						};
		<?php }?>
		
function onBtnBroadInfo(obj)
{
//	if(obj.style.backgroundImage.indexOf("broadcast1.png") > -1)
//		return;
	document.getElementById("button1").style.backgroundImage = 'url(\'<?php echo get_template_directory_uri()?>/images/profile_tab_pressed.png\')'
	document.getElementById("button2").style.backgroundImage = "";
	document.getElementById("button3").style.backgroundImage = "";

	var contentTbl = document.getElementById("contentTbl");
	   while(contentTbl.rows.length > 0)
	     contentTbl.deleteRow(0);

	  var tempStartTime = new Date(0);
	  var tempEndTime = new Date(0);
	for(k = 0; k <broadcastInfo.length; k++)
	  {
				  
		  var row = contentTbl.insertRow(contentTbl.rows.length);
		  row.style.borderBottom = "1px solid #e0e0e0";
          row.style.cursor = "pointer";
          row.onmouseover = function()
          {
              this.style.backgroundColor = "#e9e9e7";
          };
          row.onmouseout = function()
          {
              this.style.backgroundColor = "";
          };
          row.onclick = function()
          {
              parent.openVideoById(broadcastInfo[this.rowIndex].program_id);
              parent.fn_closeProfilePopup();
          };

		  var firstCell = row.insertCell(row.cells.length);
		  var secondCell = row.insertCell(row.cells.length);
		  var thirdCell = row.insertCell(row.cells.length);
		  
		  firstCell.align = "left";
          firstCell.style.paddingLeft = "39px";
		  var img = document.createElement("img");
		  img.style.width = "60px";
		  img.style.height = "60px";

		  img.style.borderRadius = '30px'; // standard
		  img.style.MozBorderRadius = '30px'; // Mozilla
		  img.style.WebkitBorderRadius = '30px'; // WebKit
		  
		  img.style.margin = "10px";
		  img.style.marginLeft = "0px";
          img.style.marginBottom = "5px";

		  img.src = broadcastInfo[k].image_path;
		  
		  firstCell.appendChild(img);

          var durationSpan = document.createElement("span");
          durationSpan.style.paddingLeft = "13px";
          durationSpan.appendChild( document.createTextNode(broadcastInfo[k].duration));
          firstCell.appendChild(durationSpan);

		  secondCell.colspan = "2";
		  secondCell.valign = "middle";
		  secondCell.align = "left";

		  
		  var div = document.createElement("div");
		  div.style.display = "block";
		  
		  div.style.wordWrap = "break-word";
		  
		  div.style.width = "90%";
		  div.style.padding = "20px";
		  div.style.overflow = "hidden";


		  div.innerHTML += "<span style='line-height:25px; font-size: 14pt; text-overflow: ellipsis; overflow: hidden; display: block; width: 350px; white-space: nowrap; padding-bottom: 10px;'>" + broadcastInfo[k].title + "</span>";
		  div.innerHTML += "<span style='line-height:25px; word-break:break-all'>" + broadcastInfo[k].description + "</span><br>";
		  
		  secondCell.appendChild(div);


          thirdCell.style.paddingRight = "39px";

          var div3 = document.createElement("div");

          div3.style.float = "right";

          div3.innerHTML += "<span style='line-height:25px; padding-bottom: 6px;'>" + broadcastInfo[k].time_ago + "</span><br>";
          div3.innerHTML += "<span style='line-height:25px; float: right;'>" + broadcastInfo[k].connect_count + " VIEWS</span><br>";

          thirdCell.appendChild(div3);


	  }
    $(document.body).perfectScrollbar('update');
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

function onBtnFanofInfo(obj)
{
//	if(obj.style.backgroundImage.indexOf("broadcast1.png") > -1)
//		return;
    document.getElementById("button1").style.backgroundImage = "";
    document.getElementById("button2").style.backgroundImage = 'url(\'<?php echo get_template_directory_uri()?>/images/profile_tab_pressed.png\')'
    document.getElementById("button3").style.backgroundImage = "";
	

	var contentTbl = document.getElementById("contentTbl");
	   while(contentTbl.rows.length > 0)
	     contentTbl.deleteRow(0);

	  for(k = 0; k <favoriteInfo.length; k++)
	  {
				  
		  var row = contentTbl.insertRow(contentTbl.rows.length);
		  row.style.borderBottom = "1px solid #e0e0e0";
          row.style.cursor = "pointer";
          row.onmouseover = function()
          {
              this.style.backgroundColor = "#e9e9e7";
          };
          row.onmouseout = function()
          {
              this.style.backgroundColor = "";
          };
          row.onclick = function()
          {
              parent.fn_closeProfilePopup();
              parent.showProfile(favoriteInfo[this.rowIndex].user_id);
          };

		  var firstCell = row.insertCell(row.cells.length);
		  var secondCell = row.insertCell(row.cells.length);
		  var thirdCell = row.insertCell(row.cells.length);

		  firstCell.align = "left";
          firstCell.style.paddingLeft = "39px";
		  var img = document.createElement("img");
		  img.style.width = "60px";
		  img.style.height = "60px";

		  img.style.borderRadius = '30px'; // standard
		  img.style.MozBorderRadius = '30px'; // Mozilla
		  img.style.WebkitBorderRadius = '30px'; // WebKit
		  
		  img.style.margin = "10px";
		  img.style.marginLeft = "0px";

		  if(favoriteInfo[k].picture_path != "")
		  	  img.src = favoriteInfo[k].picture_path;
		  else if(favoriteInfo[k].facebook_id != "")
			  img.src = "https://graph.facebook.com/" + favoriteInfo[k].facebook_id + "/picture/?type=large";
		  else  
			  img.src = favoriteInfo[k].twitter_img;
		  
		  firstCell.appendChild(img);
			
		  secondCell.valign = "middle";
		  secondCell.align = "left";
		  
		  var div = document.createElement("div");
		  div.style.display = "block";
		  
		  div.style.wordWrap = "break-word";
		  
		  div.style.width = "120px";

          div.style.fontSize = "14pt";

          div.style.fontWeight = "500";

          div.style.fontFamily = "Roboto Condensed";

          div.style.paddingLeft = "25px";

		  if(favoriteInfo[k].user_name != "")
		  	div.innerHTML = favoriteInfo[k].user_name;
		  else if(favoriteInfo[k].facebook_name != "")
			  	div.innerHTML = favoriteInfo[k].facebook_name;
		  else if(favoriteInfo[k].twitter_name != "")
			  	div.innerHTML = favoriteInfo[k].twitter_name;
		  
		  secondCell.appendChild(div);
		  secondCell.align= "left";

          thirdCell.style.paddingRight = "39px";
		  
	  }
    $(document.body).perfectScrollbar('update');
}
function onBtnsFanInfo(obj)
{
//	if(obj.style.backgroundImage.indexOf("broadcast1.png") > -1)
//		return;
    document.getElementById("button1").style.backgroundImage = "";
    document.getElementById("button2").style.backgroundImage = "";
    document.getElementById("button3").style.backgroundImage = 'url(\'<?php echo get_template_directory_uri()?>/images/profile_tab_pressed.png\')'
	

	var contentTbl = document.getElementById("contentTbl");
	   while(contentTbl.rows.length > 0)
	     contentTbl.deleteRow(0);

	  for(k = 0; k <panInfo.length; k++)
	  {
				  
		  var row = contentTbl.insertRow(contentTbl.rows.length);
		  row.style.borderBottom = "1px solid #e0e0e0";
          row.style.cursor = "pointer";
          row.onmouseover = function()
          {
              this.style.backgroundColor = "#e9e9e7";
          };
          row.onmouseout = function()
          {
              this.style.backgroundColor = "";
          };
          row.onclick = function(event)
          {
              var event = event || window.event;
              if (event.target.name != "becomebutton")
              {
                  parent.fn_closeProfilePopup();
                  parent.showProfile(panInfo[this.rowIndex].pan_id);
              }
          };

		  var firstCell = row.insertCell(row.cells.length);
		  var secondCell = row.insertCell(row.cells.length);
		  var thirdCell = row.insertCell(row.cells.length);
		  
		  firstCell.align = "left";
          firstCell.style.paddingLeft = "39px";
		  var img = document.createElement("img");
		  img.style.width = "60px";
		  img.style.height = "60px";

		  img.style.borderRadius = '30px'; // standard
		  img.style.MozBorderRadius = '30px'; // Mozilla
		  img.style.WebkitBorderRadius = '30px'; // WebKit
		  
		  img.style.margin = "10px";
		  img.style.marginLeft = "0px";
		  //img.src = panInfo[k].picture_path;

		  if(panInfo[k].picture_path != "")
		  	  img.src = panInfo[k].picture_path;
		  else if(panInfo[k].facebook_id != "")
			  img.src = "https://graph.facebook.com/" + panInfo[k].facebook_id + "/picture/?type=large";
		  else  
			  img.src = panInfo[k].twitter_img;
		  
		  firstCell.appendChild(img);
			
		  secondCell.valign = "middle";
		  secondCell.align = "left";
		  
		  var div = document.createElement("div");
		  div.style.display = "block";
		  
		  div.style.wordWrap = "break-word";
		  
		  div.style.width = "120px";

          div.style.fontSize = "14pt";

          div.style.fontWeight = "500";

          div.style.fontFamily = "Roboto Condensed";

          div.style.paddingLeft = "25px";

		  if(panInfo[k].user_name != "")
		  	div.innerHTML = panInfo[k].user_name;
		  else if(panInfo[k].facebook_name != "")
			  	div.innerHTML = panInfo[k].facebook_name;
		  else if(panInfo[k].twitter_name != "")
			  	div.innerHTML = panInfo[k].twitter_name;
		  secondCell.appendChild(div);

          if("<?php echo $programerID?>" == "<?php echo $_SESSION['user_id']?>")
          {
              var button = document.createElement("input");
              button.name="becomebutton";
              button.type = "button";

              //alert(panInfo[k].loginUserId + "," + "<?php echo $_SESSION['user_id']?>");
              /*if(panInfo[k].pan_id == "<?php echo $_SESSION['user_id']?>")
              {

                  button.value = "Become a Fan";
                  button.style.opacity = "0.7";
                  button.disabled = true;
              }
              else*/
              if(panInfo[k].pan_id == "<?php echo $_SESSION['user_id']?>")
              {
                  button.value = "Become a Fan";
                  button.style.opacity = "0.7";
                  button.disabled = true;
              }
              if(panInfo[k].loginUserId == "")
                button.value = "";
              else
                  button.value = "";
              button.style.borderRadius = '10px'; // standard
              button.style.MozBorderRadius = '10px'; // Mozilla
              button.style.WebkitBorderRadius = '10px'; // WebKit

              button.style.color = "white";
              button.style.width = "100px";
              button.style.height = "30px";
              button.style.backgroundRepeat = "round";
              button.style.border = "none";

              button.style.float = "right";

              button.style.backgroundImage = "url('<?php echo get_template_directory_uri()?>/images/profile_unfollow_button.png')";
              button.style.backgroundSize = "cover";
              button.style.backgroundPosition = "center";

              button.onclick = function()
              {
                  var index = this.parentNode.parentNode.rowIndex;
                  onBtnUnBecome(this,panInfo[index].pan_id,'');
                  contentTbl.deleteRow(index);
                  panInfo.splice(index, 1)
                  document.getElementById("panCount").innerHTML = panInfo.length;
              };
              thirdCell.appendChild(button);
          }

          thirdCell.style.paddingRight = "39px";
	  }
    $(document.body).perfectScrollbar('update');
}
function onBtnUnBecome(obj,user_id,str)
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
		  
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
		    
		    if(xmlhttp.responseText == "success")
		    {
            }
        }
      }
	 
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxUnBecomeFan.php?user_id=" + <?php echo $_SESSION['user_id']?> + "&loginUserId=" + user_id + "&Need=" + str,true);
	xmlhttp.send();
}

function onBtnBecome(obj,user_id,str)
{
	
	//obj.disabled = true;
	//obj.style.opacity = 0.7;
	document.getElementById("loadImg").style.display = "inline";

	var inputTags = document.getElementsByTagName("input");
	for(i = 0; i < inputTags.length; i++)
	{
		if(inputTags[i].type == "button")
		{
			inputTags[i].disabled = true;
			inputTags[i].style.opacity = 0.7;
		}
	}
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
		    for(i = 0; i < inputTags.length; i++)
			{
				if(inputTags[i].type == "button")
				{
					inputTags[i].disabled = false;
					inputTags[i].style.opacity = 1;
				}
			}
			
		    document.getElementById("loadImg").style.display = "none";
			//alert(xmlhttp.responseText);
		    
		    var becomebuttons = document.getElementsByName("becomebutton");
		    var index = -1;
		    for(m = 0; m < becomebuttons.length; m++)
		    {
			    if(becomebuttons[m] == obj)
			    {
				    index = m;
				    break;
			    }
		    }
		    if(index != -1)
		    {
			    
		    	var button3 = document.getElementById("button3");
		    	var button2 = document.getElementById("button2");
		    	
			    if(button3.style.backgroundImage.indexOf("broadcast1.png") > -1)
			    {
					panInfo[index].loginUserId = "<?php echo $_SESSION['user_id']?>";
					for(u = 0; u < favoriteInfo.length; u++)
					{
						if(favoriteInfo[u].user_id == panInfo[index].pan_id)
						{
							favoriteInfo[u].loginUserId = "<?php echo $_SESSION['user_id']?>";
							break;
						}
					}
			    }
				else if(button2.style.backgroundImage.indexOf("broadcast1.png") > -1)
				{
					favoriteInfo[index].loginUserId = "<?php echo $_SESSION['user_id']?>";
					for(u = 0; u < panInfo.length; u++)
					{
						if(panInfo[u].pan_id == favoriteInfo[index].user_id)
						{
							panInfo[u].loginUserId = "<?php echo $_SESSION['user_id']?>";
							break;
						}
					}
				}
				obj.value = "Unbecome a Fan";

				
				   
				obj.onclick= function(){
					var bcbuttons = document.getElementsByName("becomebutton");
				  	 for(i = 0; i < bcbuttons.length; i++)
				  	 {
					  	 if(this == bcbuttons[i])
					  	 {
					  		var button3 = document.getElementById("button3");
					    	var button2 = document.getElementById("button2");
					    	if(button3.style.backgroundImage.indexOf("broadcast1.png") > -1)
						    {
					    		onBtnUnBecome(this,panInfo[i].pan_id,'');
						  		break;
						    }
							else if(button2.style.backgroundImage.indexOf("broadcast1.png") > -1)
							{
								onBtnUnBecome(this,favoriteInfo[i].user_id,'');
						  		break;
							}
					     }
				  	 }
					};
		    }
		    if(xmlhttp.responseText.indexOf("&&") > -1)
		    {
			    
		    	obj.value = "Unbecome a Fan";
			    var returnValue = xmlhttp.responseText.split("&&");
			    var temp_picture_path = "";
			     if(returnValue[3] != "")
			    	 temp_picture_path = "<?php echo home_url()?>" + returnValue[3];
			   
			    panInfo[ panInfo.length ] = {
			    		pan_id : returnValue[2],
						picture_path : temp_picture_path,
						user_name : returnValue[0],
						facebook_name : returnValue[4],
						facebook_id : returnValue[6],
						twitter_name : returnValue[5],
						twitter_img : returnValue[7],
						loginUserId : ""
					    };

			  
			    document.getElementById("panCount").innerHTML = panInfo.length;
			    obj.onclick = function(){
					   onBtnUnBecome(this,'<?php echo $programerID?>','Need');
				   }			    
			   if(document.getElementById("button3").style.backgroundImage.indexOf("broadcast1.png") > -1)
			   {
				   var contentTbl = document.getElementById("contentTbl");
				   while(contentTbl.rows.length > 0)
				     contentTbl.deleteRow(0);

				  for(k = 0; k <panInfo.length; k++)
				  {
							  
					  var row = contentTbl.insertRow(contentTbl.rows.length);
					  row.style.borderBottom = "1px solid #e0e0e0";


					  var firstCell = row.insertCell(row.cells.length);
					  var secondCell = row.insertCell(row.cells.length);
					  var thirdCell = row.insertCell(row.cells.length);
					  
					  firstCell.align = "left";
					  var img = document.createElement("img");
					  img.style.width = "60px";
					  img.style.height = "60px";
	
					  img.style.borderRadius = '30px'; // standard
					  img.style.MozBorderRadius = '30px'; // Mozilla
					  img.style.WebkitBorderRadius = '30px'; // WebKit
					  
					  img.style.margin = "10px";
					  img.style.marginLeft = "0px";
					  //img.src = panInfo[k].picture_path;
					  if(panInfo[k].picture_path != "")
					  	  img.src = panInfo[k].picture_path;
					  else if(panInfo[k].facebook_id != "")
						  img.src = "https://graph.facebook.com/" + panInfo[k].facebook_id + "/picture/?type=large";
					  else  
						  img.src = panInfo[k].twitter_img;
					  
					  firstCell.appendChild(img);
						
					  secondCell.valign = "middle";
					  
					  var div = document.createElement("div");
					  div.style.display = "block";
					  
					  div.style.wordWrap = "break-word";
					  
					  div.style.width = "120px";

                      div.fontFamily = "Roboto Condensed";

					  if(panInfo[k].user_name != "")
					  	div.innerHTML = panInfo[k].user_name;
					  else if(panInfo[k].facebook_name != "")
						  div.innerHTML = panInfo[k].facebook_name;
					  else if(panInfo[k].twitter_name != "")
						  div.innerHTML = panInfo[k].twitter_name;
					  secondCell.appendChild(div);
					  
					  var button = document.createElement("input");
					  button.name="becomebutton";
					  button.type = "button";
					  if(panInfo[k].pan_id == "<?php echo $_SESSION['user_id']?>")
					  {
						  button.value = "Become a Fan";
						  button.disabled = true;
						  button.style.opacity = "0.7";
					  }
					  else if(panInfo[k].loginUserId == "")
                      {
					  	button.value = "";
                        button.style.backgroundImage = "url('<?php echo get_template_directory_uri()?>/images/profile_unfollow_button.png')";
                      }
					  else
                      {
						  button.value = "";
                          button.style.backgroundImage = "url('<?php echo get_template_directory_uri()?>/images/profile_follow_button.png')";
                      }

					  /*if(k == panInfo.length - 1)
					  {
						  button.style.opacity = "0.7";
						  button.disabled = true;
					  }*/
					  button.style.borderRadius = '10px'; // standard
					  button.style.MozBorderRadius = '10px'; // Mozilla
					  button.style.WebkitBorderRadius = '10px'; // WebKit
	
					  button.style.color = "white";
					  button.style.width = "110px";
					  button.style.height = "35px";

                      button.style.backgroundRepeat = "round";

                      button.style.float = "right";
					  


					  temp = panInfo[k].pan_id;
					  if(panInfo[k].loginUserId == "")
					  {
					  	button.onclick = function(){ 
						  	var bcbuttons = document.getElementsByName("becomebutton");
						  	 for(i = 0; i < bcbuttons.length; i++)
						  	 {
							  	 if(this == bcbuttons[i])
							  	 {
							  		onBtnBecome(this,panInfo[i].pan_id,'');
							  		break;
							  	 }
						  	 }
					  	};
					  }
					  else
					  {  	
						  button.onclick = function(){ 
						  var bcbuttons = document.getElementsByName("becomebutton");
						  	 for(i = 0; i < bcbuttons.length; i++)
						  	 {
							  	 if(this == bcbuttons[i])
							  	 {
							  		onBtnUnBecome(this,panInfo[i].pan_id,'');
							  		break;
							  	 }
						  	 }
						  };
					  }
					  
					  
					  					  
					  thirdCell.appendChild(button);
					  
				  }
				  
			   }
			   else if(document.getElementById("button2").style.backgroundImage.indexOf("broadcast1.png") > -1)
			   {
				   
				   for(i = 0; i < favoriteInfo.length; i++)
				   {
					   
					   
					   if(favoriteInfo[i].user_id == "<?php echo $_SESSION['user_id']?>")
					   {
						   var becomebuttons = document.getElementsByName("becomebutton");
						   becomebuttons[i].disabled = true;
						   becomebuttons[i].style.opacity = "0.7";
						   becomeButtons[i].value = "Become a Fan";
						   break;
					   }
				   }
			   }
		    }
		    if(document.getElementById("button2").style.backgroundImage.indexOf("broadcast1.png") > -1)
		    {
			   for(i = 0; i < favoriteInfo.length; i++)
			   {
				   if(favoriteInfo[i].user_id == "<?php echo $_SESSION['user_id']?>")
				   {
					   var becomebuttons = document.getElementsByName("becomebutton");
					   becomebuttons[i].disabled = true;
					   becomebuttons[i].style.opacity = "0.7";
					   becomeButtons[i].value = "Become a Fan";
					   break;
				   }
			   }
		    }
		    else if(document.getElementById("button3").style.backgroundImage.indexOf("broadcast1.png") > -1)
		    {
			   for(i = 0; i < panInfo.length; i++)
			   {
				   if(panInfo[i].pan_id == "<?php echo $_SESSION['user_id']?>")
				   {
					   var becomebuttons = document.getElementsByName("becomebutton");
					   becomebuttons[i].disabled = true;
					   becomebuttons[i].style.opacity = "0.7";
					   becomeButtons[i].value = "Become a Fan";
					   break;
				   }
			   }
		    }
		    if("<?php echo $programerID?>" == "<?php echo $_SESSION['user_id']?>")
			{
				
				document.getElementById("mainButton").style.opacity = "0.7";
				document.getElementById("mainButton").disabled = true;
			}
		}
	  }
	  
	//alert(user_id + "," + "<?php echo $user_ID?>");
	//return;
	xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxBecomeFan.php?user_id=" + user_id + "&loginUserId=" + "<?php echo $_SESSION['user_id']?>" + "&Need=" + str,true);
	xmlhttp.send();
}

    function onFollowButtonClick(button)
    {
        button.enabled = false;
        button.style.opacity = "0.7";

        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_follow=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_follow=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_follow.onreadystatechange=function()
        {
            if(xmlhttp_follow.readyState==4 && xmlhttp_follow.status==200)
            {
                button.style.backgroundImage = "url(\"<?php echo get_template_directory_uri()?>/images/profile_unfollow_button.png\")";
                button.onclick = function(){ onUnfollowButtonClick(button);};
                button.enabled = true;
                button.style.opacity = "1";
            }
        }
        xmlhttp_follow.open("GET","<?php echo get_template_directory_uri()?>/ajaxBecomeFan.php?user_id=" + <?php echo $_SESSION['user_id']?> + "&loginUserId=" + <?php echo $programerID ?> ,true);
        xmlhttp_follow.send();
    }

    function onUnfollowButtonClick(button)
    {
        button.enabled = false;
        button.style.opacity = "0.7";

        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_unfollow=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_unfollow=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_unfollow.onreadystatechange=function()
        {
            if(xmlhttp_unfollow.readyState==4 && xmlhttp_unfollow.status==200)
            {
                button.style.backgroundImage = "url(\"<?php echo get_template_directory_uri()?>/images/profile_follow_button.png\")";
                button.onclick = function(){ onFollowButtonClick(button);};
                button.enabled = true;
                button.style.opacity = "1";
            }
        }
        xmlhttp_unfollow.open("GET","<?php echo get_template_directory_uri()?>/ajaxUnBecomeFan.php?user_id=" + <?php echo $_SESSION['user_id']?> + "&loginUserId=" + <?php echo $programerID ?>,true);
        xmlhttp_unfollow.send();
    }

    function getIsFollow()
    {
        if("<?php echo $programerID?>" == "<?php echo $_SESSION['user_id']?>")
            return;

        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp_is_follow=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp_is_follow=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp_is_follow.onreadystatechange=function()
        {
            if(xmlhttp_is_follow.readyState==4 && xmlhttp_is_follow.status==200)
            {
                var button = document.getElementById("followProfileButton");

                if (xmlhttp_is_follow.responseText == "true")
                {
                    button.style.backgroundImage = "url(\"<?php echo get_template_directory_uri()?>/images/profile_unfollow_button.png\")";
                    button.onclick = function(){ onUnfollowButtonClick(button);};
                    button.enabled = true;
                    button.style.opacity = "1";
                    button.style.display = "block";
                    button.style.backgroundSize = "cover";
                    button.style.backgroundPosition = "center";
                }
                else if (xmlhttp_is_follow.responseText == "false")
                {
                    button.style.backgroundImage = "url(\"<?php echo get_template_directory_uri()?>/images/profile_follow_button.png\")";
                    button.onclick = function(){ onFollowButtonClick(button);};
                    button.enabled = true;
                    button.style.opacity = "1";
                    button.style.display = "block";
                    button.style.backgroundSize = "cover";
                    button.style.backgroundPosition = "center";
                }
            }
        }
        xmlhttp_is_follow.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetIsFollow.php?user_id=" + <?php echo $_SESSION['user_id']?> + "&pan_id=" + <?php echo $programerID ?>,true);
        xmlhttp_is_follow.send();
    }


</script>
<br>
<form name="aform" method="post">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="liveVod" name="liveVod" value="" >
<input type="hidden" id="programId" name="programId" value="" >

<div id="container" align="center" style="overflow: hidden; position:relative;">
    <div style="height:50px; width:100%">
        <h1 style="font-size:27pt; font-family:'Myriad Pro'; color:black; cursor:default;">
            Profile
        </h1>
    </div>
	<div align="center" >
		<table style="width:100%;
		-moz-box-shadow: 0 10px 25px rgba(0,0,0,0.5);
		 background-color: red;
		 background: -moz-linear-gradient( top, red, #c10000, #ae0000 );
         background: -webkit-linear-gradient( top, red, #c10000, #ae0000 );
         background: -o-linear-gradient( top, red, #c10000, #ae0000 );
         background: -khtml-linear-gradient( top, red, #c10000, #ae0000 );
         background: -ms-linear-gradient( top, red, #c10000, #ae0000 );
         background: linear-gradient( top, red, #c10000, #ae0000 );
		 height: 210px" cellpadding="10" cellspacing="10" border=0 >
			<colgroup>
				<col width="25%">
				<col width="*">
                <col width="20%">
			</colgroup>
			<tr>
				<td  align="center" style="vertical-align: middle; border-radius:10px; width: 33%" >

					<img id="avatarImage" style=" width:150px; height:150px; background-color: #dddddd; border-radius:75px" src="<?php echo $userpic?>">
                    <div style="width: 120px; padding-top: 5px; word-wrap: break-word; display: block;">
                    <?php
                    if($programerID == $_SESSION['user_id'])
                    {?>
             <!--           <iframe frameborder="no" marginwidth="0px" marginheight="0px"  src="<?php echo home_url()?>/avatarUpload/" style="float: left;  width: 120px; height:30px; " scrolling="no">
                        </iframe>
                        <input type="button" id="removeAvatarButton" onclick="removeAvatar(this);" onmouseover="this.style.opacity=0.7;" onmouseout="this.style.opacity=1;" style="display: <?php if ($userpic == "/wp-content/themes/Explorable/images/profile-thumb.png") echo "none"; else echo "block"?>;border-top-left-radius: 10px; border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-bottom-left-radius: 10px; width: 120px; height: 30px; font-size: 13px; color: rgb(255, 255, 255); opacity: 1; background-image: url('<?php echo get_template_directory_uri()?>/images/login1.png');" value="Remove image" >
                -->
                    <?php }?>
                    </div>
				</td>
				<td align="left">
					<h1 style="color:white; font-family:'Myriad Pro'"><?php
						echo $username;
					?>
					</h1>
					<!--
					<?php
					if($programerID == $_SESSION['user_id'])
					{?>
						<input type="button" id="mainButton" disabled="disabled"  onclick="onBtnBecome(this,'<?php echo $programerID?>','Need');" onmouseover="this.style.opacity=0.7;" onmouseout="this.style.opacity=1;" value="Become a Fan" 
					style="opacity:0.7; margin-top:10px; border-radius:10px; width:130px;; height:40px; color:#ffffff; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');">
					<?php }
					else if($loginPanId == "")
					{?>
					<input type="button" id="mainButton" onclick="onBtnBecome(this,'<?php echo $programerID?>','Need');" onmouseover="this.style.opacity=0.7;" onmouseout="this.style.opacity=1;" value="Become a Fan" 
					style="margin-top:10px; border-radius:10px; width:130px;; height:40px; color:#ffffff; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');">
					<?php }
					else{?>
					<input type="button" id="mainButton" onclick="onBtnUnBecome(this,'<?php echo $programerID?>','Need');" onmouseover="this.style.opacity=0.7;" onmouseout="this.style.opacity=1;" value="Unbecome a Fan" 
					style="margin-top:10px; border-radius:10px; width:130px;; height:40px; color:#ffffff; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');">
					<?php }?>
                    -->
				</td>
                <td align="right">
                    <input id="followProfileButton" type="button"  style="display:none; border:none; border-radius:10px; width:100px; height:30px; float:left; background-repeat:round;">
                </td>
			</tr>
			
		</table>
	</div>

	<div style="height:60px; " align="center">
		<table style="width:100%; height:100%;  background-color: #f5f5f5; font-family:'Myriad Pro';" border=0>
			<colgroup>
				<col width="*">
				<col width="*">
				<col width="*">
				
			</colgroup>
			<tr style="height:100%;">
				<td id="button1" align="center"
                    onclick="onBtnBroadInfo(this);"
                    onmouseover="if (this.style.backgroundImage=='') this.style.backgroundImage='url(\'<?php echo get_template_directory_uri()?>/images/profile_tab_over.png\')'"
                    onmouseout="if (this.style.backgroundImage.indexOf('profile_tab_over.png') != -1) this.style.backgroundImage='';"
                    style="width:33%; background-repeat: round; line-height: 20px; cursor: pointer; background-size: contain;">
					<span style=" width:100%; height:40px; color:black; border-style:none; background-color:#F5F5F5">BROADCASTS</span>
                    <br>
                    <span id="broadcastCount" style="width:140px; height: 20px; font-weight: 800; font-size: 14pt;"><?php echo count($broadcastInfo["title"])?></span>
                </td>
				<td id="button3" align="center"
                    onclick="onBtnsFanInfo(this);"
                    onmouseover="if (this.style.backgroundImage=='') this.style.backgroundImage='url(\'<?php echo get_template_directory_uri()?>/images/profile_tab_over.png\')'"
                    onmouseout="if (this.style.backgroundImage.indexOf('profile_tab_over.png') != -1) this.style.backgroundImage='';"
                    style="width:33%; background-repeat: round; line-height: 20px; cursor: pointer; background-size: contain;">

                    <span style="width:100%; height:40px; color:black; border-style:none; background-color:#F5F5F5">FOLLOWING</span>
                    <br>
                    <span id="panCount" style="width:140px; height: 20px; font-weight: 800; font-size: 14pt;"><?php echo count($panInfo["pan_id"])?></span>
                </td>
				<td id="button2" align="center"
                    onclick="onBtnFanofInfo(this);"
                    onmouseover="if (this.style.backgroundImage=='') this.style.backgroundImage='url(\'<?php echo get_template_directory_uri()?>/images/profile_tab_over.png\')'"
                    onmouseout="if (this.style.backgroundImage.indexOf('profile_tab_over.png') != -1) this.style.backgroundImage=''"
                    style="width:33%; background-repeat: round; line-height: 20px; cursor: pointer; background-size: contain;">
					<span style="width:100%; height:40px; color:black; border-style:none; background-color:#F5F5F5">FOLLOWERS</span>
                    <br>
                    <span id="favoriteCount" style="width:140px; height: 20px; font-weight: 800; font-size: 14pt;"><?php echo count($favoriteInfo["user_id"])?></span>
                </td>
			</tr>
		</table>
	</div>

	<div id="tableDiv" style="overflow:hidden; width:100%; height:100%; position:relative;" align="center" >
		<table id="contentTbl" style=" width:100%;" border="0">
			<colgroup>
				<col width="100px;">
				<col width="*">
				<col width="200px;">
			</colgroup>

			
		</table>
	</div>
</div>
</form>

<script>
    $(document.body).perfectScrollbar();
<!--    if ("--><?php //echo $userpic ?><!--" != "")-->
<!--    {-->
<!--        document.getElementById("uploadAvatarButton").style.display = "none";-->
<!--        document.getElementById("removeAvatarButton").style.display = "block";-->
<!--    }-->
<!--    else-->
<!--    {-->
<!--        document.getElementById("uploadAvatarButton").style.display = "block";-->
<!--        document.getElementById("removeAvatarButton").style.display = "none";-->
<!--    }-->
    onBtnBroadInfo(document.getElementById("button1"));
</script>