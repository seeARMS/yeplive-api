<?php session_start();
 
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
.popClose {
	position:absolute; 
	border-radius:7px; 
	font:bold 131% sans-serif; 
	padding:3px 6px 2px; 
	right:-7px; 
	top:-7px; 
	background-color:silver; 
	color:#fff; 
	cursor:pointer; 
	text-align:center; 
	text-decoration:none;
	z-index: 3000;
}

</style>
<script language="javascript">
	var objCurrPopup;
	function fn_register()
	{
		objCurrPopup = $('#register_popup').bPopup({
			onOpen: function() { document.getElementById("loginFrame").src = "<?php echo home_url( '/' )?>/login/" }, 
			onClose: function() {  }
		});
	}
	
	function fn_closePopup()
	{
		objCurrPopup.close();
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
</script>
		

<div id="register_popup" class="popup" style="padding:0px; z-index: 1000000;">
    <span onclick="fn_closePopup();" class="popClose"><span>X</span></span>
    <iframe id="loginFrame" frameborder="no" src="" style="width:410px;height:567px;z-index: 1000000;">
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
	<header id="main-header" style="margin: 0px; padding: 0px;">
		<div class="container">
			<?php $logo = ( $user_logo = et_get_option( 'explorable_logo' ) ) && '' != $user_logo ? $user_logo : $template_directory_uri . '/images/logo.png'; ?>
            <div id="logo">
			<a  href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="logo_img" src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/></a>
            </div>
			
			<div class="et-search-form">
				<form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="image" src="<?php echo $template_directory_uri; ?>/images/search-icon.png" class="search_submit" />
					<input type="text" value="<?php esc_html_e( 'SEARCH', 'Explorable' ); ?>" name="s" class="search_input" />
				</form>
			</div> <!-- .et-search-form -->
			
			<div class="header_right">
			
				<div class="menu_wrapper" onmouseover="show_menu();"  onmouseout="show_menu_out();">   
                    <div class="nav-button" id="nav-button" >
                        <a class="arrow">
                            <img class="nav_image" src="<?php echo $template_directory_uri; ?>/images/category.png">
                            CATEGORIES
                            <img class="nav_image" src="<?php echo $template_directory_uri; ?>/images/dropdown.png">
                        </a>
                    </div> 
                    
             <!--  *********************************** category menu ************************************* -->  
                  
                  <div class="nav_container" id="nav_container">
			            <?php
			            
			                $menu_name = 'livestream';
			                
			                $menu = wp_get_nav_menu_object( $menu_name );
			
			                $menu_items = wp_get_nav_menu_items($menu->term_id);
			
			                $menu_list = '<div id="menu-' . $menu_name . '">';
			                
			                $i = 0;
			                foreach ( (array) $menu_items as $key => $menu_item ) {
			                    $title = $menu_item->title;
			                    $url = $menu_item->url;
			                    if ($i == 0){
			                        $menu_list .= '<div class="sub_wrap1" onclick="onmenuClick(\'' . $title . '\');"><a href="javascript:">' . $title . '</a></div><div style="font-size: 18px;"><div class="sub_wrap">';
			                        $i = 1;    
			                    } else {
			                        if ($i > 2){
			                            $i = 1;
			                            $menu_list .= '</div><div class="sub_wrap">';    
			                        }
			                        if ($i == 1){
			                            $menu_list .= '<div class="sub_menu1" onclick="onmenuClick(\'' . $title . '\');"><a href="javascript:void(null);">' . $title . '</a></div>'; 
			                        } else {
			                            $menu_list .= '<div class="sub_menu2" onclick="onmenuClick(\'' . $title . '\');"><a href="javascript:void(null);">' . $title . '</a></div>';
			                        }
			                        $i += 1;                                    
			                    }
			                }
			                $menu_list .= '</div></div></div>';
			                echo $menu_list;
			            ?>    
			        </div>
                  
                                         
                </div>
                
                <script type="text/javascript">
                
                 function show_menu () {
                     var nav = document.getElementById( 'nav_container' );
                     nav.className += ' toggled-on';
                    /*if ( -1 != nav.className.indexOf( 'toggled-on' ) ) {                    	
                        nav.className = nav.className.replace( ' toggled-on', '' );                       
                    } else {

                        nav.className += ' toggled-on';                       
                   }*/
                 }
                 function show_menu_out () {
                     //alert('ok');
                     var nav = document.getElementById( 'nav_container' );
                     nav.className = nav.className.replace( 'toggled-on', '' );
                 }


                </script>
                
				<div class="sign_in" align="center" >
					
					<?php if(!isset($_SESSION['user_id'])){?>
                    <a href="javascript:void(0);" onclick="fn_register();">	
						<img title="Log in" id="loginImage" class="sign-image" src="<?php echo get_template_directory_uri()?>/images/sign_in.png">  SIGN IN</a>
					
					<?php }
					else {?>
                    <a href="javascript:void(0);" onclick="fn_logout();">
						<img title="Log out" id="logoutImage" src="<?php echo get_template_directory_uri()?>/images/sign_out.png">  SIGN OUT</a>
					<?php }?>
					
				</div>	
				
				<div class="record_button" align="center">
					<a href="<?php echo home_url( '/' )?>/broadcast/"></a> 
					<a href="<?php echo home_url( '/' )?>/add-program/"></a>
					<a onclick="onPostClick()" href="javascript:void(0);">
						<img title="Post Live" id="postImage" class="post_image" src="<?php echo get_template_directory_uri()?>/images/record_now.png">RECORD NOW
					</a>
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
