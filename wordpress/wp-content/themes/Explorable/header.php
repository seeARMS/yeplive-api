<?php 
	if( !session_start() )
	{
	session_start();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> >
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php elegant_titles(); ?></title>



	<?php elegant_description(); ?>
	<?php elegant_keywords(); ?>
	<?php elegant_canonical(); ?>

	<?php do_action( 'et_head_meta' ); ?>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php $template_directory_uri = get_template_directory_uri(); ?>
	<!--[if lt IE 9]>
		<script src="<?php echo esc_url( $template_directory_uri . '/js/html5.js"' ); ?>" type="text/javascript"></script>
	<![endif]-->

	<script type="text/javascript">
		document.documentElement.className = 'js';
		

	</script>

    <meta property="og:title" content="YepLive" />
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/images/logo.png"/>

	<?php wp_head(); ?>
		
		<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/rcarousel.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/jquery-ui-1.8.16.custom.css" />
        <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/lightbox.css" />
        <link type="text/css" rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/datepicker.css" />
        		
        <!-- <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery-1.4.4.min.js"></script> -->
        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.ui.core.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.ui.rcarousel.js"></script>
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/oms.min.js"></script>
		 
		<!--  <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/datepicker.js"></script> --> 
		
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/gmap3.min.js"></script>
		<!-- <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/markerwithlabel.js"></script> -->
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/custom.js"></script>
		
		<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.ui.rlightbox.min.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/jquery.bpopup.js"></script>
		<!-- <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/oms.min.js"></script> -->

        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/moment.js"></script>
        <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/js/numeral.min.js"></script>
		
		<style type="text/css">
        
			#divcontainer {
				width: 930px;
				height: 265px;
				position: relative;
			}
			
			#divcarousel {
				margin: 0 auto;
			}

			#divcarousel img {
				border: 0;
			}

			#ui-carousel-next, #ui-carousel-prev {
				width: 30px;
				height: 42px;
				
				background: url(<?php echo get_template_directory_uri()?>/images/et-left-arrow.png)  center center no-repeat;
				display: block;
				position: absolute;
				top: 0;
				z-index: 100;
				
				border-top-left-radius: 3px;
				border-bottom-left-radius: 3px;
				
				
				display: block;
				box-shadow: 0 1px 0 0 #fff inset;
				border-left: 1px solid #c7c7c7;
				
				
			}

			#ui-carousel-next {
				right: 0;
				background-image: url(<?php echo get_template_directory_uri()?>/images/et-right-arrow.png);
			}

			#ui-carousel-prev {
				left: 0;
			}
			
			#ui-carousel-next > span, #ui-carousel-prev > span {
				display: none;
			}
          	
		</style>
		<script type="text/javascript">
			jQuery(function( $ ) {
				$( "#divcarousel" ).rcarousel({
					visible: 4,
					step: 4,
					margin:20,
					width:195,
					height:240,
					speed: 300
				});
				
				$( ".lb_gallery" ).rlightbox();
				
				$( "#ui-carousel-next" )
					.add( "#ui-carousel-prev" )
					.hover(
						function() {
							$( this ).css( "opacity", 0.7 );
						},
						function() {
							$( this ).css( "opacity", 1.0 );
						}
					);				
			});
			</script>
			
		
</head>
<script language="javascript">

	

	var menuLoadFlag = false;

    var searchInputOnKeyDownValue = "";
    var wasInput = false;

	function oninit()
	{
		if( document.getElementsByName("hidMenus") == null || document.getElementsByName("hidMenus") == undefined)
			return;
		var menuNames = document.getElementsByName("menuNames");
		var hidMenus = document.getElementsByName("hidMenus");
		var hidFlags = document.getElementsByName("hidFlags");
		hidFlags.value = "";
		hidMenus.value = "";
		for(i = 0; i < menuNames.length; i++)
		{
			if(menuNames[i].parentNode.parentNode.className == "sub-menu")
			{
				hidFlags.value = hidFlags.value + "1,";
				hidMenus.value = hidMenus.value + menuNames[i].id + ",^/,";
			}
			else
			{
				hidFlags.value = hidFlags.value + "0,";
				hidMenus.value = hidMenus.value + menuNames[i].id + ",^/,";
			}
		}
		
		while(hidMenus.value.indexOf("&") > -1)
		{
			var andCharPos = hidMenus.value.indexOf("&");
			hidMenus.value = hidMenus.value.replace("&","^^^");
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
			  
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
			    
			  
			  var channel_ids = xmlhttp.responseText.split("\\//\\//")[1];
			  realChannel_ids = channel_ids.split(",");
			  var menuNames = document.getElementsByName("menuNames");
			  var counter = 0;
			  for(i = 1; i < menuNames.length; i++)
				{
				  
				  	/*if(menuNames[i].parentNode.parentNode.className == "sub-menu")
					{
				  		var hiddenValue = document.getElementsByName(menuNames[i].id);
				  		menuNames[i].id = realChannel_ids[counter];
				  		hiddenValue[0].name = realChannel_ids[counter];
				  		
				  		try{
				  			menuNames[i].href = hiddenValue[0].value + "?hidChannelId=" + realChannel_ids[counter];
				  	
				  		}
				  		catch(e)
				  		{
				  			var tp = document.getElementsByName(menuNames[i].id);
				  			menuNames[i].href = tp[0].value + "?hidChannelId=" + realChannel_ids[counter];
				  		}
				  		menuNames[i].onclick = function(){};
				  		
						counter++;
					}*/
					var hiddenValue = document.getElementsByName(menuNames[i].id);
					menuNames[i].href = hiddenValue[0].value + "?hidChannelId=" + realChannel_ids[i-1];
				}
			  	
			  	menuLoadFlag = true;
				document.getElementById("loadImg").style.display = "none";
				clearInterval(getMenu_thread); 
		    }
		  }
		  //
		  
		 menuGetFlag = 0;
		xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxCateSubReg.php?menuNames="+hidMenus.value + "&isCateOrSub=" + hidFlags.value,true);
		xmlhttp.send();
	}
	var menuGetFlag = -1;
	var realChannel_ids = "";
	var getMenu_thread = setInterval("fn_getMenuInfo();",10000);
	function fn_getMenuInfo()
	{
		if(menuGetFlag == 0)
		{
			oninit();
		}
		if(menuGetFlag == 1)
			clearInterval(getMenu_thread);
	}
	function onMenuClick(e,urlstr)
	{
		
		if(realChannel_ids == "")
			return;
		evt = e || window.event;

		if(window.event)
		{
			/*if(evt.srcElement.parentNode.parentNode.className != "sub-menu" 
				&& evt.srcElement.innerText != "Home" && evt.srcElement.innerText != "E-Map")
				return;*/

			var id = evt.srcElement.id;
			
			if(document.getElementById(id) != null && document.getElementById(id) != undefined)
			{
				if(location.href.indexOf("broadcast") == -1)
				{
					document.getElementById("hidChannelId").value = id;
					document.aform.action = urlstr;
					document.aform.submit();
					return;
				}
				else
				{
					if(startDate.getTime() == 0 || org_start_date.trim() == "")
					{
						
						document.getElementById("hidChannelId").value = id;
						document.aform.action = urlstr;
						document.aform.submit();
						return;
					}
					var pprogid = "<?php echo $_POST['broadProg_id']?>";
					
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
						  //var channel_ids = xmlhttp.responseText.split("\\//\\//")[1];
						 
						  	document.getElementById("hidChannelId").value = id;
							
							document.aform.action = urlstr;
							document.aform.submit();
						}
					  }
					
					xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxSetEndTime.php?prog_id=" + pprogid,true);
					xmlhttp.send();
				}
			}
		}
		else
		{
			
			
			var id = evt.target.id;
			
			if(document.getElementById(id) != null && document.getElementById(id) != undefined)
			{
				if(location.href.indexOf("broadcast") == -1)
				{
					document.getElementById("hidChannelId").value = id;
					document.aform.action = urlstr;
					document.aform.submit();
					return;
				}
				else
				{
					if(startDate.getTime() == 0 || org_start_date.trim() == "")
					{
						
						document.getElementById("hidChannelId").value = id;
						document.aform.action = urlstr;
						document.aform.submit();
						return;
					}
					var pprogid = "<?php echo $_POST['broadProg_id']?>";
					
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
						  //var channel_ids = xmlhttp.responseText.split("\\//\\//")[1];
						 
						  	document.getElementById("hidChannelId").value = id;
							
							document.aform.action = urlstr;
							document.aform.submit();
						}
					  }
					
					xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxSetEndTime.php?prog_id=" + pprogid,true);
					xmlhttp.send();
				}
				//evt.href = urlstr + "?hidChannelId=" + id;
			}
		}
		
		//location.href = urlstr + "?channel_id=" + id;// + "?menuTitle=" + id + urlstr;
		
	}		

	function getWindowPosition(x_size,y_size)
	{
		var xpos = ( window.screen.width -  x_size  ) / 2;
		var ypos = ( window.screen.height - y_size )  / 2;
		var props = 'width='+x_size;
		props += (',height=' + y_size);
		props += (',top='+ypos);
		props += (',left='+xpos);
		return props;
	} 

	var myFacebookWindow = null;
	var myFacebookInterval = null;

	function fn_facebookLogin()
	{
		if (myFacebookWindow != null)
		{
			myFacebookWindow.close();	
		}
		if (myFacebookInterval != null)
		{
			clearInterval(myFacebookInterval);
		}
		
		var URL = "<?php echo home_url()?>/facebook_login/";
		var props = getWindowPosition(450, 250);
		myFacebookWindow = window.open(URL, "", props);

		myFacebookInterval = setInterval("fn_checkFacebookSession()", 3000);
		
	}

	function fn_checkFacebookSession()
	{
		$.ajax({
			type: "POST",
		  	url: "<?php echo home_url()?>/facebook_checklogin/",
		  	success: function(msg) {
			  	if (msg != '0')
				{
					
					clearInterval(myFacebookInterval);
					myFacebookWindow.close();	
					/*
					$('#imgFacebook').css('display', 'none');
					$('#imgFacebookVerified').css('display', 'inline');
					
					if (msg == '3')
					{
						$('#imgTwitter').css('display', 'none');
						$('#imgTwitterVerified').css('display', 'inline');
					}*/
					location.href = "<?php echo home_url()?>";
				}
		  	}
		});	
	}

	var myTwitterWindow = null;
	var myTwitterInterval = null;
	
	function fn_twitterLogin()
	{
		if (myTwitterWindow != null)
		{
			myTwitterWindow.close();	
		}
		if (myTwitterInterval != null)
		{
			clearInterval(myTwitterInterval);
		}
		
		var URL = "<?php echo home_url()?>/twitter_login/";
		var props = getWindowPosition(800, 500);
		myTwitterWindow = window.open(URL, "", props);

		myTwitterInterval = setInterval("fn_checkTwitterSession()", 3000);
		
	}

	function fn_checkTwitterSession()
	{
		$.ajax({
			type: "POST",
		  	url: "<?php echo home_url()?>/twitter_checklogin/",
		  	success: function(msg) {
			  	if (msg != '0')
				{
					
					clearInterval(myTwitterInterval);
					myTwitterWindow.close();	
					/*
					$('#imgTwitter').css('display', 'none');
					$('#imgTwitterVerified').css('display', 'inline');
					
					if (msg == '3')
					{
						$('#imgFacebook').css('display', 'none');
						$('#imgFacebookVerified').css('display', 'inline');
					}*/
					location.href = "<?php echo home_url()?>";
					
				}
		  	}
		});	
	}
</script>
<style >

.event_poster {
float:left;
position: relative;
margin: 0 0 20px 10px;
padding: 2px;
width: 161px;
height: 274px;
border-radius: 5px;
background-color: #ffffff;
box-shadow: 0 1px 1px 0 rgba(0,0,0,0.15);
cursor: pointer;
}
.poster_wrapper {
position: relative;
width: auto;
height: 242px;
}
.description{
position: absolute;
bottom: 0;
overflow: hidden;
padding: 8px;
width: 145px;
background-color: rgba(0,0,0,0.7);
color: #cccccc;
-webkit-font-smoothing: antialiased;
}
.event_title{
	padding-bottom: 8px;
	background: none;
	color: #fff;
	word-wrap: break-word;
	font-size: 14px;
}
.event_blurb{
font-weight: 300;
font-size: 11px;
line-height: 16px;
}
.account_name{
color: #fff;
font-weight: normal;
font-style: normal;
}
.event_time{
	padding-left: 8px;
	line-height: 34px;
}
.popup {
	display:none;
	border-radius:10px; 
	box-shadow:0 0 25px 5px #999; 
	padding:25px;
}

</style>
<script language="javascript">
    var objCurrPopup;
	var objCurrAccountPopup;
    var objCurrProfilePopup;
    var filterSearchInputHasFocus = false;
	function fn_register()
	{
        objCurrPopup = $('#register_popup').bPopup({
			onOpen: function() { document.getElementById("loginFrame").src = "<?php echo home_url()?>/login/" }, 
			onClose: function() {  }
		});
		
		document.getElementById("register_popup").style.width = "690px";
		document.getElementById("register_popup").style.height = "410px";

		window.parent.document.getElementById("loginFrame").style.height = "420px";
		window.parent.document.getElementById("loginFrame").style.width = "690px";
		var popup_left = screen.width / 2 - (690 / 2);
		var popup_top = screen.height / 2 - (420 / 2 ) - 100;
		window.parent.document.getElementById("register_popup").style.left = popup_left + "px";
		window.parent.document.getElementById("register_popup").style.top = popup_top + "px";
	}
	
	function fn_closePopup()
	{
		objCurrPopup.close();
	}
    function fn_closeAccountPopup()
    {
        objCurrAccountPopup.close();
    }
    function fn_closeProfilePopup()
    {
        objCurrProfilePopup.close();
    }
	function onPostClick()
	{
		//twitter_id
		//facebook_id
		<?php 

		//if($user_ID || isset($_SESSION['facebook_id']) || isset($_SESSION['twitter_id']))
		if($_SESSION['user_id'])
		{?>
		//location.href = "<?php //echo home_url( '/' )?>/add-program/";
		location.href = "<?php echo home_url( '/' )?>/voice_test/";
		<?php }
		else{
		?>
		//alert("Please do login!");
		fn_register();
		<?php }?>
	}
	function onImgLogin()
	{
		
				
		var loginFrame = document.getElementById("loginFrame");
		
		loginFrame.style.left = parseInt(screen.width / 2 - loginFrame.offsetWidth / 2 - 200) + "px";
		loginFrame.style.top = "150px";
		loginFrame.style.display = "inline";
		loginFrame.src = "<?php echo home_url( '/' )?>/login/";
		 
	}
	function fn_logout()
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
			 
			  document.location.href = "<?php echo home_url() ?>";
			}
		  }
		  
		  
		  
		xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxLogout.php",true);
		
		xmlhttp.send();
			
		
	}

    function onRecordVideoClick()
    {
        <?php

		//if($user_ID || isset($_SESSION['facebook_id']) || isset($_SESSION['twitter_id']))
		if($_SESSION['user_id'])
		{?>

        objCurrPopup = $('#voice_test_popup').bPopup({
            onOpen: function() { document.getElementById("voiceTestFrame").src = "<?php echo home_url()?>/voice_test/" },
            onClose: function() {  }
        });

        document.getElementById("voice_test_popup").style.width = "1024px";
        document.getElementById("voice_test_popup").style.height = "555px";

        window.parent.document.getElementById("voiceTestFrame").style.width = "1024px";
        window.parent.document.getElementById("voiceTestFrame").style.height = "555px";

        var popup_left = screen.width / 2 - (1024 / 2);
        var popup_top = screen.height / 2 - (555 / 2 ) - 100 + 37.5;
        window.parent.document.getElementById("voice_test_popup").style.left = popup_left + "px";
        window.parent.document.getElementById("voice_test_popup").style.top = popup_top + "px";

        <?php }
        else{
        ?>
        //alert("Please do login!");
        fn_register();
        <?php }?>

    }

    var selectedFilter;


    function showListBox(filter)
    {
        filterSearchInputHasFocus = true;
        //parent.testFunction(this.value);

        var map = $(et_main_map).gmap3('get');
        var bounds =  map.getBounds();

        var NElng = bounds.getNorthEast().lng();
        var SWlng = bounds.getSouthWest().lng();
        var NElat = bounds.getNorthEast().lat();
        var SWlat = bounds.getSouthWest().lat();


        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttpfilter=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttpfilter=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttpfilter.onreadystatechange=function()
        {

            if (xmlhttpfilter.readyState==4 && xmlhttpfilter.status==200)
            {
                if (!filterSearchInputHasFocus) return;

                var listBox = document.createElement("div");
                listBox.id = "listbox";
                listBox.className = "listbox";

                var logo = document.getElementById("logo");
                var searchForm = document.getElementById("et-search-form");

                var hasVideosInTheArea = true;

                if(xmlhttpfilter.responseText == "/|/|/|/|")
                {
                    var titleParagraph = document.createElement("span");
                    titleParagraph.className = "titleParagraph";
                    if (pattern != "")
                    {
                        titleParagraph.innerHTML = "There are no suggestions";
                        document.getElementById("filterSearchInput").style.color = "red";
                    }
                    else
                        titleParagraph.innerHTML = "There are no videos in the area";

                    listBox.appendChild(titleParagraph);

                    if (document.getElementById("listBoxWrapper").contains(document.getElementById("listbox")))
                        document.getElementById("listBoxWrapper").removeChild(document.getElementById("listbox"));

                    document.getElementById("listBoxWrapper").appendChild(listBox);
                    hasVideosInTheArea = false;
                }

                document.getElementById("filterSearchInput").style.color = "black";

                if (hasVideosInTheArea)
                {
                    var result = xmlhttpfilter.responseText.split("/|/|");
                    var tags;
                    var followed_users;
                    var most_followed_users;
                    if (result[0] != undefined) tags = result[0].split(",");
                    if (result[1] != undefined) followed_users= result[1].split("||");
                    if (result[2] != undefined) most_followed_users = result[2].split("||");


                    if ((tags != undefined) && (tags.length > 1))
                    {
                        var titleParagraph = document.createElement("span");
                        titleParagraph.className = "titleParagraph";
                        if (pattern != "")
                            titleParagraph.innerHTML = "Most popular tags:";
                        else
                            titleParagraph.innerHTML = "Most popular tags in the area:";

                        listBox.appendChild(titleParagraph);


                        for ( var index = 0; index < tags.length-1 ; index++ )
                        {
                            //alert(tags[index]);
                            var tagParagraph = document.createElement("a");
                            tagParagraph.className = "tagParagraph";
                            tagParagraph.style.textDecoration = "none";
                            tagParagraph.innerHTML = tags[index];
                            tagParagraph.onmouseover = function()
                            {
                                this.style.backgroundColor = "#af121e";
                                this.style.color = "white";
                                document.getElementById("filterSearchInput").onblur = function(){};
                            };
                            tagParagraph.onmouseout = function()
                            {
                                this.style.backgroundColor = "white";
                                this.style.color = "black";
                                document.getElementById("filterSearchInput").onblur = function(){onSearchInputBlur()};
                            }
                            tagParagraph.onclick = function()
                            {
                                var new_value = this.innerHTML;
                                document.getElementById("filterSearchInput").value = new_value;
    //                                            document.getElementById("filterSearchInput").blur();
                                hideListBox();
                                parent.selectVideosOnMap(document.getElementById("filterSearchInput").value, filter);
                            }
                            tagParagraph.href = "javascript:void(0);";

                            listBox.appendChild(tagParagraph);
                        }
                    }

                    if ((followed_users != undefined) && (followed_users.length > 1))
                    {
                        var titleParagraph2 = document.createElement("span");
                        titleParagraph2.className = "titleParagraph";
                        if (pattern != "")
                            titleParagraph2.innerHTML = "People you follow:";
                        else
                            titleParagraph2.innerHTML = "People you follow in the area:";

                        listBox.appendChild(titleParagraph2);


                        for ( var index = 0; index < followed_users.length-1 ; index++ )
                        {
                            var user_data = followed_users[index].split(",");

                            var followedUserParagraph = document.createElement("a");
                            followedUserParagraph.className = "userParagraph";
                            followedUserParagraph.style.textDecoration = "none";

                            var picture_path;
                            if (user_data[2] != "") picture_path = user_data[2];
                            else picture_path = "/wp-content/themes/Explorable/images/profile-thumb.png";
                            followedUserParagraph.innerHTML = "<div style='width: 12%; height: 100%; float: left; padding-right: 8px; padding-top: 2px;'><img src='" + picture_path + "' style='height: 100%; width: 100%;'/></div>" + user_data[1];
                            followedUserParagraph.data = user_data;

                            followedUserParagraph.onmouseover = function()
                            {
                                this.style.backgroundColor = "#af121e";
                                this.style.color = "white";
                                document.getElementById("filterSearchInput").onblur = function(){};
                            };
                            followedUserParagraph.onmouseout = function()
                            {
                                this.style.backgroundColor = "white";
                                this.style.color = "black";
                                document.getElementById("filterSearchInput").onblur = function(){onSearchInputBlur()};
                            }
                            followedUserParagraph.onclick = function()
                            {
                                document.getElementById("filterSearchInput").value = this.data[1];
                                //document.getElementById("filterSearchInput").blur();
                                hideListBox();
                                parent.selectVideosOnMapByUserId(this.data[0], filter);
                            }
                            followedUserParagraph.href = "javascript:void(0);";

                            listBox.appendChild(followedUserParagraph);

                        }
                    }

                    if ((most_followed_users != undefined) && (most_followed_users.length > 1))
                    {
                        var titleParagraph3 = document.createElement("span");
                        titleParagraph3.className = "titleParagraph";

                        if (pattern != "")
                            titleParagraph3.innerHTML = "Most followed people:";
                        else
                        {
                            titleParagraph3.innerHTML = "Most followed people in the area:";
                            titleParagraph3.style.paddingBottom = "2vh";
                        }

                        listBox.appendChild(titleParagraph3);


                        for ( var index = 0; index < most_followed_users.length-1 ; index++ )
                        {
                            var user_data = most_followed_users[index].split(",");

                            var mostFollowedUserParagraph = document.createElement("a");
                            mostFollowedUserParagraph.className = "userParagraph";
                            mostFollowedUserParagraph.style.textDecoration = "none";

                            var picture_path;
                            if (user_data[2] != "") picture_path = user_data[2];
                            else picture_path = "/wp-content/themes/Explorable/images/profile-thumb.png";
                            mostFollowedUserParagraph.innerHTML = "<div style='width: 12%; height: 100%; float: left; padding-right: 8px; padding-top: 2px;'><img src='" + picture_path + "' style='height: 100%; width: 100%;'/></div>" + user_data[1];
                            mostFollowedUserParagraph.data = user_data;

                            mostFollowedUserParagraph.onmouseover = function()
                            {
                                this.style.backgroundColor = "#af121e";
                                this.style.color = "white";
                                document.getElementById("filterSearchInput").onblur = function(){};
                            };
                            mostFollowedUserParagraph.onmouseout = function()
                            {
                                this.style.backgroundColor = "white";
                                this.style.color = "black";
                                document.getElementById("filterSearchInput").onblur = function(){onSearchInputBlur()};
                            }
                            mostFollowedUserParagraph.onclick = function()
                            {
                                document.getElementById("filterSearchInput").value = this.data[1];
                                //document.getElementById("filterSearchInput").blur();
                                hideListBox();
                                parent.selectVideosOnMapByUserId(this.data[0], filter);
                            }
                            mostFollowedUserParagraph.href = "javascript:void(0);";

                            listBox.appendChild(mostFollowedUserParagraph);

                        }
                    }
                }

                var uploadDateFilterPara = document.createElement("p");
                uploadDateFilterPara.className = "titleParagraph";

                var uploadDateSpan = document.createElement("span");
                uploadDateSpan.innerHTML = "Upload date: ";
                //uploadDateFilterPara.appendChild(uploadDateSpan);


                var filterSelectBox = document.createElement("select");
                filterSelectBox.style.padding = "0.1vh";
                filterSelectBox.style.margin = "1vh";
                filterSelectBox.style.fontSize = "1.5vh";
                filterSelectBox.style.width = "91%";
                filterSelectBox.style.height = "3vh";
                filterSelectBox.id = "filterSelectBox";

                filterSelectBox.onmouseover = function(){
                    document.getElementById("filterSearchInput").onblur = function(){};
                };
                filterSelectBox.onmouseout = function(){
                    document.getElementById("filterSearchInput").onblur = function(){onSearchInputBlur()};

                };
                filterSelectBox.onblur = function(){
                    document.getElementById("filterSearchInput").focus();
                }

                var optionDisabled = document.createElement("option");
                optionDisabled.innerHTML = "No filters";
                optionDisabled.value = "nofilter";
                //optionDisabled.disabled = "disabled";
                if (selectedFilter == undefined) optionDisabled.selected = "selected";

                var optionLive = document.createElement("option");
                optionLive.value = "live";
                optionLive.innerHTML = "Live now";
                if (selectedFilter == optionLive.value) optionLive.selected = "selected";

                var optionMin = document.createElement("option");
                optionMin.value = "5min";
                optionMin.innerHTML = "Last 5 min";
                if (selectedFilter == optionMin.value) optionMin.selected = "selected";

                var optionHour = document.createElement("option");
                optionHour.value = "1hour";
                optionHour.innerHTML = "Last hour";
                if (selectedFilter == optionHour.value) optionHour.selected = "selected";

                var optionArchived = document.createElement("option");
                optionArchived.value = "archived";
                optionArchived.innerHTML = "Archived";
                if (selectedFilter == optionArchived.value) optionArchived.selected = "selected";

                filterSelectBox.appendChild(optionDisabled);
                filterSelectBox.appendChild(optionLive);
                filterSelectBox.appendChild(optionMin);
                filterSelectBox.appendChild(optionHour);
                filterSelectBox.appendChild(optionArchived);

                filterSelectBox.onchange = function(){
                    hideListBox();
                    selectedFilter = this.options[this.selectedIndex].value;
                    showListBox(selectedFilter);
                    parent.selectVideosOnMapByFilter(selectedFilter);
                };

                //uploadDateFilterPara.appendChild(filterSelectBox);
                listBox.appendChild(filterSelectBox);
                //listBox.appendChild(uploadDateFilterPara);


                var showArchivedButton = document.createElement("input");

                showArchivedButton.type = "button";
                showArchivedButton.value = "Show archived";

                showArchivedButton.style.margin = "1vh";
                showArchivedButton.style.fontSize = "1.5vh";
                showArchivedButton.style.width = "91%";
                showArchivedButton.style.height = "3vh";

                showArchivedButton.onclick = function(){
                    hideListBox();
                    parent.selectVideosOnMapByFilter("archived");
                };
                showArchivedButton.onmouseover = function(){
                    document.getElementById("filterSearchInput").onblur = function(){};
                };
                showArchivedButton.onmouseout = function(){
                    document.getElementById("filterSearchInput").onblur = function(){onSearchInputBlur()};

                };

                //listBox.appendChild(showArchivedButton);


                if (document.getElementById("listBoxWrapper").contains(document.getElementById("listbox")))
                    document.getElementById("listBoxWrapper").removeChild(document.getElementById("listbox"));

                document.getElementById("listBoxWrapper").appendChild(listBox);
            }
        }

        var pattern = document.getElementById("filterSearchInput").value.split(",");
        pattern = pattern[pattern.length - 1];
        if (document.getElementById("filterSearchInput").value == "FILTER BY TAGS") pattern = "";

        xmlhttpfilter.open("GET","<?php echo get_template_directory_uri()?>/ajaxGetPopularTagsInArea.php?user_id="+ <?php if (isset($_SESSION['user_id'])) echo $_SESSION['user_id']; else echo "\"\"" ?> + "&pattern=" + pattern + "&NElng=" + NElng + "&NElat=" + NElat + "&SWlng=" + SWlng + "&SWlat=" + SWlat + "&filter=" + filter, true)

        xmlhttpfilter.send();
    }

    function onSearchInputBlur()
    {
//        if ((document.getElementById("filterSearchInput").value != searchInputOnKeyDownValue)&&(!wasInput))
//        {
//            document.getElementById("filterSearchInput").focus();
//            return;
//        }
//        wasInput = false;
//        document.getElementById("filterSearchInput").value = searchInputOnKeyDownValue;

        filterSearchInputHasFocus = false;
        var val = document.getElementById("filterSearchInput").value;
        if ((val == "FILTER BY TAGS")||(val == ""))
            document.getElementById("filterSearchInput").style.color = "black";
/*
        var filterSelectBox = document.getElementById("filterSelectBox");
        var target = event.explicitOriginalTarget||document.activeElement;
        if ((target.id != filterSelectBox.id)) */
            hideListBox();
    }


    function hideListBox()
    {
        if (document.getElementById("listBoxWrapper").contains(document.getElementById("listbox")))
            document.getElementById("listBoxWrapper").removeChild(document.getElementById("listbox"));
    }

    function onSearchInputKeyDown(event)
    {
        if (event.keyCode == 13)
        {
            submitSearchInput();
        }
    }

    function submitSearchInput()
    {
        parent.selectVideosOnMap(document.getElementById("filterSearchInput").value, selectedFilter);
        document.getElementById("filterSearchInput").blur();
        searchInputOnKeyDownValue = document.getElementById("filterSearchInput").value;
        hideListBox();
    }

    function checkInputAndShowListBox(input)
    {
        if (input[0] == ",")
        {
            document.getElementById("filterSearchInput").value =  document.getElementById("filterSearchInput").value.substr(1, document.getElementById("filterSearchInput").value.length);
        }
        else if (input.indexOf(' ') != -1)
        {
            //document.getElementById("filterSearchInput").value =  document.getElementById("filterSearchInput").value.substr(0, document.getElementById("filterSearchInput").value.length - 1);
            document.getElementById("filterSearchInput").value = document.getElementById("filterSearchInput").value.replace(/ /g, "");
        }
        else if (input.indexOf(',,') != -1)
        {
            document.getElementById("filterSearchInput").value = document.getElementById("filterSearchInput").value.replace(/,,/g, ",");
        }
        else
        {
            wasInput = true;
            showListBox(selectedFilter);
        }

    }

    function settingsButtonClick()
    {
        if (document.getElementById("settingsWrapper").style.display == "block")  hideSettingsBox();
        else showSettingsBox();
    }

    function showSettingsBox()
    {
        document.getElementById("settingsButton").src = '<?php echo get_template_directory_uri()?>/images/setting_symbol_over.png';
        document.getElementById("settingsWrapper").style.display = "block";
    }

    function hideSettingsBox()
    {
        document.getElementById("settingsButton").src = '<?php echo get_template_directory_uri()?>/images/setting_symbol.png';
        document.getElementById("settingsWrapper").style.display = "none";
    }

    function onSettingsMenuElementOver(element)
    {
        element.style.backgroundColor='#af121e';
        element.children[0].style.color='white';
        document.getElementById("settingsButton").onblur = function(){};
    }

    function onSettingsMenuElementOut(element)
    {
        element.style.backgroundColor='white';
        element.children[0].style.color='black';
        document.getElementById("settingsButton").onblur = function(){hideSettingsBox()};
    }



    function openMyAccount()
    {
        objCurrAccountPopup = $('#account_popup').bPopup({
            onOpen: function() { document.getElementById("accountFrame").src = "<?php echo home_url()?>/account/?programerID=<?php echo $_SESSION['user_id'] ?>" },
            onClose: function() {  }
        });

        document.getElementById("account_popup").style.width = "750px";
        document.getElementById("account_popup").style.height = "550px";
        document.getElementById("account_popup").style.borderRadius = "10px";

        window.parent.document.getElementById("accountFrame").style.height = "550px";
        window.parent.document.getElementById("accountFrame").style.width = "750px";
        window.parent.document.getElementById("accountFrame").style.borderRadius = "10px";
        window.parent.document.getElementById("accountFrame").style.backgroundColor = "#f5f5f5";
        var popup_left = screen.width / 2 - (850 / 2);
        var popup_top = screen.height / 2 - (500 / 2 ) - 100;
        window.parent.document.getElementById("account_popup").style.left = popup_left + "px";
        window.parent.document.getElementById("account_popup").style.top = popup_top + "px";

        $('body').find('.b-modal').attr('style','');
    }

    function openMyProfile()
    {
        objCurrProfilePopup = $('#profile_popup').bPopup({
            onOpen: function() { document.getElementById("profileFrame").src = "<?php echo home_url()?>/profile/?programerID=<?php echo $_SESSION['user_id'] ?>" },
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



</script>

<div id="voice_test_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <div onclick="fn_closePopup();" class="popClose"><img src="<?php echo get_template_directory_uri()?>/images/login-close.png" style="width:53px;"></div>
    <iframe id="voiceTestFrame" frameborder="no" src="" scrolling="no" style="width:1024px;height:555px;z-index: 1000000;border-radius:10px;">
    </iframe>
</div>

<div id="register_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <div onclick="fn_closePopup();" class="popClose"><img src="<?php echo get_template_directory_uri()?>/images/login-close.png" style="width:53px;"></div>
    <iframe id="loginFrame" frameborder="no" src="" style="width:690px;height:420px;z-index: 1000000;">
    </iframe>
</div>

<div id="profile_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <div onclick="fn_closeProfilePopup();" class="popClose"><img src="<?php echo get_template_directory_uri()?>/images/login-close.png" style="width:53px;"></div>
    <iframe id="profileFrame" frameborder="no" src="" style="width:690px;height:420px;z-index: 1000000;">
    </iframe>
</div>

<div id="account_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <div onclick="fn_closeAccountPopup();" class="popClose"><img src="<?php echo get_template_directory_uri()?>/images/login-close.png" style="width:53px;"></div>
    <iframe id="accountFrame" frameborder="no" src="" style="width:690px;height:420px;z-index: 1000000;">
    </iframe>
</div>

<body onload="oninit();" <?php body_class(); ?> style="background: url('<?php echo get_template_directory_uri()?>/images/background.jpg') repeat;
									background-image: url('<?php echo get_template_directory_uri()?>/images/background.jpg');
									background-position-x: 0px;
									background-position-y: 80px;
									background-size: initial;
									background-repeat-x: repeat;
									background-repeat-y: repeat;
									background-attachment: initial;
									background-origin: initial;
									background-clip: initial;
									background-color: initial;
									background-position: 0 80px;"
>
	
	<img style="width:100px;height:100px;position:absolute;display:none; z-index: 1000;" id="loadImg" src="<?php echo get_template_directory_uri()?>/images/loading1.gif">
    <div id="headerShadowDiv" style="display:none; width: 100%; height: 6vh; -webkit-box-shadow: 0 0 7px rgba(0, 0, 0, 0.7); -moz-box-shadow: 0 0 7px rgba(0, 0, 0, 0.7); box-shadow: 0 0 7px rgba(0, 0, 0, 0.7); z-index: 100; position: absolute;"></div>
    <header id="main-header" style="display:none; margin: 0px; padding: 0px;">
		<div class="container" id="headerContainer" style="height: 6vh">
			<?php $logo = ( $user_logo = et_get_option( 'explorable_logo' ) ) && '' != $user_logo ? $user_logo : $template_directory_uri . '/images/logo.png'; ?>
            <div id="logo">
			<a  href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="logo_img" style="height: 6vh; position: absolute" src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/></a>
            </div>


            <div id="et-search-form-wrapper" class="et-search-form-wrapper">
                <div id="listBoxWrapper" style="position: absolute; width: 27vh; margin-top: 1.2vh;">

                </div>
                <div class="et-search-form" id="et-search-form" style="margin-top: 1.2vh;">
                    <!--				<form method="get" class="searchform" style="line-height: 3.5vh" onsubmit="parent.selectVideosOnMap()">-->
                    <input type="image" src="<?php echo $template_directory_uri; ?>/images/search-icon.png" class="search_submit" onclick="submitSearchInput()"/>
                    <input type="text" id="filterSearchInput" value="<?php esc_html_e( 'FILTER BY TAGS', 'Explorable' ); ?>" name="s" autocomplete="off" class="search_input" onfocus="showListBox(selectedFilter)" oninput="checkInputAndShowListBox(this.value)" onkeydown="onSearchInputKeyDown(event)" onblur="onSearchInputBlur()" />
                    <!--				</form>-->
                </div> <!-- .et-search-form -->


            </div>


			
			<div class="header_right" >

                <input type="image" id="settingsButton" src="<?php echo get_template_directory_uri()?>/images/setting_symbol.png" style="width: 4.5vh; position: relative; float: right; right: 1vh; top: 0.75vh" onclick="settingsButtonClick()" onblur="hideSettingsBox()" />
<!--                     onmouseover="javascript:this.src = '--><?php //echo get_template_directory_uri()?><!--/images/setting_symbol_over.png'"  onmouseout="javascript:this.src = '--><?php //echo get_template_directory_uri()?><!--/images/setting_symbol.png'"/>-->


                    <div class="record_button" onclick="onRecordVideoClick()" align="center">
                        <a  href="javascript:void(0);">
                            <img title="Post Live" id="postImage" class="post_image" src="<?php echo get_template_directory_uri()?>/images/record_now.png">RECORD NOW
                        </a>
                    </div>


                <div id="settingsWrapper" style="min-width: 16vh; max-width: 30vh; height: 10px; display: none; float: right; position: absolute; right: 0; top: 6vh;">

                    <div id="settingsBoxWrapper" style="width: 100%">
                        <div id="settingsBox" class="settingsbox" onclick="hideSettingsBox()">

                            <?php if(isset($_SESSION['user_id'])){?>

                                <div class="my_account" onclick="openMyProfile();" align="center" onmouseover="onSettingsMenuElementOver(this)" onmouseout="onSettingsMenuElementOut(this)">
                                    <a href="javascript:void(0);">
                                        <div>
                                            <img title="My profile" id="myProfileImage" class="my_account_image" src="<?php echo get_template_directory_uri()?>/images/profile-thumb.png">
                                            <span id="myProfileName" style="padding-right: 10px">MY ACCOUNT</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="my_account" onclick="openMyAccount();" align="center" onmouseover="onSettingsMenuElementOver(this)" onmouseout="onSettingsMenuElementOut(this)">
                                    <a href="javascript:void(0);">
                                        <div>
                                            <span id="myAccountName">MY ACCOUNT</span>
                                        </div>
                                    </a>
                                </div>

                            <?php } ?>

                            <div class="sign_in" <?php if(!isset($_SESSION['user_id'])){?> onclick="fn_register();" <?php } else {?> onclick="fn_logout();" <?php }?> align="center" onmouseover="onSettingsMenuElementOver(this)" onmouseout="onSettingsMenuElementOut(this)">



                                <?php if(!isset($_SESSION['user_id'])){?>
                                    <a href="javascript:void(0);">
                                    <img title="Log in" id="loginImage" class="sign-image" src="<?php echo get_template_directory_uri()?>/images/sign_in.png">  SIGN IN</a>

                                <?php }
                                else {?>
                                    <a href="javascript:void(0);">
                                    <img title="Log out" id="logoutImage" src="<?php echo get_template_directory_uri()?>/images/sign_out.png">  SIGN OUT</a>
                                <?php }?>

                            </div>

                        </div>

                    </div>

			</div>


			
		</div> <!-- .container -->
        
        
        
		<div id="top-navigation" style="display: none;">
				<nav>
				<?php
					$menuClass = 'nav';
					if ( 'on' == et_get_option( 'explorable_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
					$primaryNav = '';
					if ( function_exists( 'wp_nav_menu' ) ) {
						$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
					}
					if ( '' == $primaryNav ) { ?>
					<ul class="<?php echo esc_attr( $menuClass ); ?>" style="height:100%; line-height:30px;">
						<?php if ( 'on' == et_get_option( 'explorable_home_link' ) ) { ?>
							<li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?> ><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home','Explorable' ); ?></a></li>
						<?php }; ?>

						<?php show_page_menu( $menuClass, false, false ); ?>
						<?php show_categories_menu( $menuClass, false ); ?>
					</ul>
					<?php }
					else echo( $primaryNav );
				?>
				</nav>
				
			</div> <!-- #top-navigation -->
		
	</header> <!-- #main-header -->

<?php if ( ! et_is_listing_page() ) : ?>
	<!-- <a href=""><div id="et-header-bg"></div></a>  -->
<?php endif; ?>
