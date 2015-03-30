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
			progInfo[ progInfo.length ] = 
				{
					pid : "<?php echo $value->pid?>",
					vote : "<?php echo $value->vote?>",
					cid : "<?php echo $value->cid?>",
					start_time : "<?php echo $value->start_time?>",
					end_time : "<?php echo $value->end_time?>",
					vod_path : "<?php echo $value->vod_path?>",
					vod_enable : "<?php echo $value->vod_enable?>",
					user_id : "<?php echo $value->user_id?>",
					username : "<?php echo $value->username?>",
					facebook_name : "<?php echo $value->facebook_name?>",
					twitter_name : "<?php echo $value->twitter_name?>",
					lat : "<?php echo $value->lat?>",
					lgt : "<?php echo $value->lgt?>",
					title : "<?php echo $value->title?>",
					description : "<?php echo $value->description?>",
					isMobile : "<?php echo $value->isMobile?>"
				};
		<?php 
		}
	?>

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
<div id="et_main_map"></div>
<script>
var loadFlag = 0;
(function($){
	var $et_main_map = $( '#et_main_map' );
	var oCenter = ['0', '0'];
<?php if (sizeof($emap_items) > 0) {
	echo "oCenter=['".$emap_items[0]->lat."', '".$emap_items[0]->lgt."'];";
} ?>
	et_active_marker = null;
	$et_main_map.gmap3({
		map:{
			options:{
				zoom:3,
				center: oCenter,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: true,
				mapTypeControlOptions: {
					position : google.maps.ControlPosition.RIGHT_TOP,
					style : google.maps.MapTypeControlStyle.DROPDOWN_MENU
				},
				//streetViewControlOptions: {
				//	position: google.maps.ControlPosition.LEFT_TOP
				//},
				navigationControl: true,
				scrollwheel: true,
				streetViewControl: true,
				zoomControl: true,
				zoomControlOptions: {
			        style: google.maps.ZoomControlStyle.SMALL
			    }
			}
		}
	});
	
	function et_add_marker( marker_order, marker_lat, marker_lng, marker_description, nRating, bPlayer, username ){
		alert(marker_order);
		var marker_id = 'et_marker_' + marker_order;
		var oIcon = "<?php echo get_template_directory_uri(); ?>/images/red-marker.png";
		var oAnimation = google.maps.Animation.DROP;
		/* if (marker_order == '0') {
			oIcon = "<?php //echo get_template_directory_uri(); ?>/images/blue-marker.png";
			oAnimation = google.maps.Animation.BOUNCE;
		} */
			
		$et_main_map.gmap3({
			marker : {
				id : marker_id,
				latLng : [marker_lat, marker_lng],
				options: {
					icon : oIcon,
					animation: oAnimation
				},
				events : {
					click: function( marker ){
						if(loadFlag == 1)
						{ 
							var markerObj = $(this).gmap3({get:"et_marker_0"}); //document.getElementById("et_marker_0");
							//markerObj = $("#et_marker_0");
							if(markerObj != null && markerObj != undefined)
							{
								var samePosArr = new Array();
								var counter = 0;
								//alert(marker.id);
								
								//alert(marker.getPosition().lat() + "," + marker.getPosition().lng());
								
								samePosArr[ samePosArr.length ] = {id:marker, lat : marker.getPosition().lat() , lng : marker.getPosition().lng()  , index : 0};
								
								while(markerObj != null && markerObj != undefined)
								{
									//alert(markerObj.position.);
									if(marker != markerObj)
									{
										
										if((marker.getPosition().lat() == markerObj.getPosition().lat()) 
												&& (marker.getPosition().lng() == markerObj.getPosition().lng()))
										{
											samePosArr[ samePosArr.length ] = {id:markerObj, lat : markerObj.getPosition().lat() , lng : markerObj.getPosition().lng() , index : counter + 1};
										}
									}
									counter++;
									//markerObj = $("#et_marker_" + counter.toString());
									markerObj = $(this).gmap3({get:"et_marker_" + counter.toString()});
								}
								
								if(samePosArr.length > 1)
								{
									var centerPos = {latitude : samePosArr[0].lat , longitude : samePosArr[0].lng};
									
									for(var i = 0; i < samePosArr.length; i++)
									{
										samePosArr[i].lat = samePosArr[i].lat +  Math.cos(( 2 * Math.PI / samePosArr.length) * i) * 10;
										samePosArr[i].lng = samePosArr[i].lng +  Math.sin(( 2 * Math.PI / samePosArr.length) * i) * 10; 
										
									}	
									counter = 0;
									//markerObj = $("#et_marker_" + counter.toString());
									markerObj = $(this).gmap3({get:"et_marker_" + counter.toString()});
									
									while(markerObj != null && markerObj != undefined)
									{
										for(i = 0; i < samePosArr.length; i++)
										{
											if(samePosArr[i].id == markerObj)
											{
												markerObj.setPosition(new google.maps.LatLng(samePosArr[i].lat, samePosArr[i].lng));
												break;
											}
										}
										counter++;
										
										markerObj = $(this).gmap3({get:"et_marker_" + counter.toString()});
									}
									 
									
									for(i = 0; i < samePosArr.length; i++)
									{
										
										$(this).gmap3({
											polyline:{
												options:{
													strokeColor: "#FF0000",
													strokeOpacity: 1.0,
													strokeWeight: 2,
													path:[[centerPos.latitude , centerPos.longitude],
															[samePosArr[i].lat , samePosArr[i].lng]]
													}
													
												}
											});
							            
									}
									
								}
								
								
							}
							
						}
						else
							loadFlag = 1;
						if ( et_active_marker ){
							et_active_marker.setAnimation( null );
							et_active_marker.setIcon( '<?php echo get_template_directory_uri(); ?>/images/red-marker.png' );
						}
						et_active_marker = marker;

						marker.setAnimation( google.maps.Animation.BOUNCE);
						marker.setIcon( '<?php echo get_template_directory_uri(); ?>/images/blue-marker.png' );
						$(this).gmap3("get").panTo( marker.position );

						$.fn.et_simple_slider.external_move_to( marker_order );
					},
					mouseover: function( marker ){
						$( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
					},
					mouseout: function( marker ){
						$( '#' + marker_id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 500, function() {
							$(this).css( { 'display' : 'none' } );
						} );
					}
				}
			},
			overlay : {
				latLng : [marker_lat, marker_lng],
				options : {
					content : '<div id="' + marker_id + '" class="et_marker_info"><div class="location-description"> <div class="location-title"> <h2>' + marker_description + '</h2> <div class="listing-info" ><p>' + username +'</p></div> </div> <div class="location-rating"><span class="et-rating"><span style="width:' + (nRating * 17) + 'px;"></span></span></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->',
					offset : {
						y:-42,
						x:-122
					}
				}
			}
		});

		
	/*	var oms = new OverlappingMarkerSpiderfier($et_main_map.gmap3("get"),
		        {markersWontMove: true, markersWontHide: true});

		oms.addMarker($( '#' + marker_id ));*/
	}
	/* et_add_marker( 0, '11.5581074', '104.9174743', 'First Title', 5, false );
	et_add_marker( 1, '51.5581074', '104.9174743', 'Second Title', 4, false );
	et_add_marker( 2, '21.5581074', '114.9174743', '2.5 Title', 2.5, false, uid ); */
	<?php
	if (sizeof($emap_items) > 0 ) {
		foreach ($emap_items as $row => $values) {
			
			if($values->username != "")
				echo "et_add_marker($row,'".$values->lat."', '".$values->lgt."','".$values->title."', ".$values->vote.", false, '".$values->username."');";
			else if($values->facebook_name != "")
				echo "et_add_marker($row,'".$values->lat."', '".$values->lgt."','".$values->title."', ".$values->vote.", false, '".$values->facebook_name."');";
			else if($values->twitter_name != "")
				echo "et_add_marker($row,'".$values->lat."', '".$values->lgt."','".$values->title."', ".$values->vote.", false, '".$values->twitter_name."');";
		}
	} 
	?>
	/*$.fn.emap_polling = function(){
		alert(2222);	
	}*/
		
	//window.setInterval($.emap_polling(),3000);

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
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
			{
				if(xmlhttp.responseText == "")
					return;

				var et_map_slides = "";

				
				if(progInfo.length == 0)
				{
					var et_slider_wrapper = document.createElement("div");
					et_slider_wrapper.className = "et-map-post ui-draggable";
					et_slider_wrapper.id = "et-slider-wrapper";
					document.body.appendChild(et_slider_wrapper);
					
					et_map_slides = document.createElement("div");
					et_map_slides.id = "et-map-slides";
					et_slider_wrapper.appendChild(et_map_slides);
				
					/*
					<div class="et-slider-arrows">
						<a class="et-arrow-prev" href="#">Previous</a>
						<a class="et-arrow-next" href="#">Next</a>
					</div>
					<div class="et-slider-arrows">
						<a class="et-arrow-prev" href="#">Previous</a>
						<a class="et-arrow-next" href="#">Next</a>
					</div>
					*/
					
					var et_slider_arrows = document.createElement("div");
					et_slider_arrows.className = "et-slider-arrows";
					et_slider_wrapper.appendChild(et_slider_arrows);
				
						
					var aTag = document.createElement("a");
					aTag.className = "et-arrow-prev";
					aTag.href = "#";
					aTag.innerHTML = "Previous";
				
					
					et_slider_arrows.appendChild(aTag);

					var aTag = document.createElement("a");
					aTag.className = "et-arrow-next";
					aTag.href = "#";
					aTag.innerHTML = "Next";
				
					
					et_slider_arrows.appendChild(aTag);

					var et_slider_arrows = document.createElement("div");
					et_slider_arrows.className = "et-slider-arrows";
					et_slider_wrapper.appendChild(et_slider_arrows);
				

					var aTag = document.createElement("a");
					aTag.className = "et-arrow-prev";
					aTag.href = "#";
					aTag.innerHTML = "Previous";

					et_slider_arrows.appendChild(aTag);
				

					var aTag = document.createElement("a");
					aTag.className = "et-arrow-next";
					aTag.href = "#";
					aTag.innerHTML = "Next";

					et_slider_arrows.appendChild(aTag);
				
					
				}
				
				if(et_map_slides == "")
					et_map_slides = document.getElementById("et-map-slides");
				
				var emapinfo = xmlhttp.responseText.split("///");
				if(progInfo.length == 0)
				{
					
				}
				
				for(i = 0; i < emapinfo.length - 1; i++)
				{
					if(emapinfo[i].split("---")[8] != "")
					{
						/*
						$pids = $pids . $values->pid . "---"; 			    => emapinfo[0]
						$pids = $pids . $values->vote . "---";			    => emapinfo[1]
						$pids = $pids . $values->cid . "---";			    => emapinfo[2]
						$pids = $pids . $values->start_time . "---";		=> emapinfo[3]
						$pids = $pids . $values->end_time . "---";			=> emapinfo[4]
						$pids = $pids . $values->vod_path . "---";			=> emapinfo[5]
						$pids = $pids . $values->vod_enable . "---";		=> emapinfo[6]
						$pids = $pids . $values->user_id . "---";			=> emapinfo[7]
						$pids = $pids . $values->username . "---";			=> emapinfo[8]
						$pids = $pids . $values->facebook_name . "---";		=> emapinfo[9]
						$pids = $pids . $values->twitter_name . "---";		=> emapinfo[10]
						$pids = $pids . $values->lat . "---";				=> emapinfo[11]
						$pids = $pids . $values->lgt . "---";				=> emapinfo[12]
						$pids = $pids . $values->title . "---";				=> emapinfo[13]
						$pids = $pids . $values->description . "---";		=> emapinfo[14]
						$pids = $pids . $values->isMobile . "///";			=> emapinfo[15]
						*/
						alert(11);
						et_add_marker(progInfo.length + i , emapinfo[i].split("---")[11] , emapinfo[i].split("---")[12] , emapinfo[i].split("---")[13] , emapinfo[i].split("---")[1] , false , emapinfo[i].split("---")[8]);
						alert(12);
					}
					else if(emapinfo[i].split("---")[9] != "")
					{
						et_add_marker(progInfo.length + i , emapinfo[i].split("---")[11] , emapinfo[i].split("---")[12] , emapinfo[i].split("---")[13] , emapinfo[i].split("---")[1] , false , emapinfo[i].split("---")[9]);
					}
					else if(emapinfo[i].split("---")[10] != "")
					{
						et_add_marker(progInfo.length + i, emapinfo[i].split("---")[11] , emapinfo[i].split("---")[12] , emapinfo[i].split("---")[13] , emapinfo[i].split("---")[1] , false , emapinfo[i].split("---")[10]);
					}
				}
				
				for(i = 0; i < emapinfo.length - 1; i++)
				{
					progInfo[ progInfo.length ] = {
							pid : emapinfo[i].split("---")[0], // pid;
							vote : emapinfo[i].split("---")[1], // vote;
							cid : emapinfo[i].split("---")[2], // cid;
							start_time : emapinfo[i].split("---")[3], // start_time;
							end_time : emapinfo[i].split("---")[4], // end_time;
							vod_path : emapinfo[i].split("---")[5], // vod_path;
							vod_enable : emapinfo[i].split("---")[6], // vod_enable;
							user_id : emapinfo[i].split("---")[7], // user_id;
							username : emapinfo[i].split("---")[8], // username;
							facebook_name : emapinfo[i].split("---")[9], // facebook_name;
							twitter_name : emapinfo[i].split("---")[10], // twitter_name;
							lat : emapinfo[i].split("---")[11], // lat;
							lgt : emapinfo[i].split("---")[12], // lgt;
							title : emapinfo[i].split("---")[13], // title;
							description : emapinfo[i].split("---")[14], // description;
							isMobile : emapinfo[i].split("---")[15] // isMobile;
						}

					
					var et_map_slide = $("div[id='et-map-slide-clone']").clone(true).appendTo($("#et-map-slides"));//document.createElement("div");
					// et_map_slide.className = "et-map-slide";
					et_map_slide.attr("id", "");
					et_map_slide.attr("style", "");
					et_map_slide.addClass("");
					if(progInfo.length - 1 == 0)
						et_map_slide.className += " et-active-map-slide";
					
					et_map_slides.appendChild(et_map_slide); // <div class='et-map-slide'>
					var thumbnail = document.createElement("div");
					thumbnail.className = "thumbnail";
					thumbnail.style.height = "230px";
					thumbnail.style.backgroundColor = "rgba(0,0,0,0.2)";
					et_map_slide.appendChild(thumbnail); // <div class='thumbnail'>
					
					
					var et_description = document.createElement("div");
					et_description.className = "et-description";
					et_description.style.marginTop = "-50px";
					thumbnail.appendChild(et_description);  // <div class='et-description' >
					
					
					var H1 = document.createElement("h1");
					H1.style.fontSize = "28px";
					
					
					et_description.appendChild(H1); // <h1 style='font-size:28px' >
					var aTag = document.createElement("a");
					
					
					aTag.href = "javascript:void(0)";
					aTag.innerHTML = progInfo[progInfo.length - 1].title;
					H1.appendChild(aTag); // <a href='javascript:void(0);'>
					
					
					var pTag = document.createElement("p");
					pTag.id = "timeShow" + (progInfo.length - 1);
					pTag.style.fontSize = "12px";
					et_description.appendChild(pTag); // <p id='timeShow'>
					
					
					var pTag = document.createElement("p");
					pTag.style.fontSize = "12px";
					pTag.innerHTML = progInfo[progInfo.length - 1].description;
					et_description.appendChild(pTag); // <p style='font-size: 12px'>
					
					
					var clientStartTime = new Date(fn_dateConvert(progInfo[progInfo.length - 1].start_time));
					var clientEndTime = new Date(0);
					
					
					year = clientStartTime.getFullYear();
					month = clientStartTime.getMonth() + 1;
					intDate = clientStartTime.getDate();
						
					hours = clientStartTime.getHours();
					minutes = clientStartTime.getMinutes();
					seconds = clientStartTime.getSeconds();

					
					if(month.toString().length == 1)
						month = "0" + month;
					if(intDate.toString().length == 1)
						intDate = "0" + intDate;
					if(hours.toString().length == 1)
						hours = "0" + hours;

					if(minutes.toString().length == 1)
						minutes = "0" + minutes;
					if(seconds.toString().length == 1)
						seconds = "0" + seconds;

					
					document.getElementById("timeShow" + (progInfo.length - 1)).innerHTML += "Start Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
					
					if(progInfo[progInfo.length - 1].end_time != "")
					{
						clientEndTime.setTime(fn_dateConvert(progInfo[progInfo.length - 1].end_time));
						
						year = clientEndTime.getFullYear();
						month = clientEndTime.getMonth() + 1;
						intDate = clientEndTime.getDate();

						hours = clientEndTime.getHours();
						minutes = clientEndTime.getMinutes();
						seconds = clientEndTime.getSeconds();

						if(month.toString().length == 1)
							month = "0" + month;
						if(intDate.toString().length == 1)
							intDate = "0" + intDate;
						if(hours.toString().length == 1)
							hours = "0" + hours;

						if(minutes.toString().length == 1)
							minutes = "0" + minutes;
						if(seconds.toString().length == 1)
							seconds = "0" + seconds;

						document.getElementById("timeShow" + (progInfo.length - 1)).innerHTML += "<br>" + "End Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
					}
					
					var span = document.createElement("span");
					span.className = "et-rating";
					et_description.appendChild(span);
					

					var subSpan = document.createElement("span");
					subSpan.style.width = (parseInt(progInfo[ progInfo.length - 1].vote) * 20 ) + "px";
					span.appendChild(subSpan);
					

					var div = document.createElement("div");
					div.style.marginTop = "5px";
					et_description.appendChild(div);
							

					var input = document.createElement("input");
					input.type = "button";
					input.name = "emapValues";
					input.value = "Go to watch";
					input.style.cursor = "pointer";
					input.style.width = "100px";
					input.style.borderLeft = "0px";
					input.style.borderTop = "0px";
					input.style.height = "35px";
					input.style.color = "#ffffff";
					input.style.borderRadius = "10px";
					input.style.background = "url('<?php echo get_template_directory_uri()?>/images/login1.png')";
					
					//input.onclick = "onEmapClick('" + progInfo[ progInfo.length - 1 ].pid + "' , '" + progInfo[ progInfo.length - 1 ].cid + "' , '" + progInfo[ progInfo.length - 1 ].vod_enable + "',this)";
					input.onclick = function()
					{
						onEmapClick( progInfo[ progInfo.length - 1 ].pid , progInfo[ progInfo.length - 1 ].cid , progInfo[ progInfo.length - 1 ].vod_enable , this);  
					};
					
					div.appendChild(input);
					var et_date_wrapper = document.createElement("div");
					et_date_wrapper.className = "et-date-wrapper";
					thumbnail.appendChild(et_date_wrapper);
					
					
					var span = document.createElement("span");
					span.className = "et-date";
					span.innerHTML = "<?php echo date("F j")?>";
					et_date_wrapper.appendChild(span);
					
					
					var subSpan = document.createElement("span");
					subSpan.innerHTML = "<?php echo date("Y")?>";
					span.appendChild(subSpan);
					
					
					var et_place_content = document.createElement("div");
					et_place_content.className = "et-place-content";
					et_place_content.style.height = "0px";
					et_place_content.style.padding = "0px";
					et_place_content.style.border = "0px";
					et_map_slide.appendChild(et_place_content); //et-place-content
					

					var et_place_text_wrapper = document.createElement("div"); //et-place-text-wrapper
					et_place_text_wrapper.className = "et-place-text-wrapper";
					et_place_content.appendChild(et_place_text_wrapper);
					
					
					var et_place_main_text = document.createElement("div"); //et-place-main-text
					et_place_main_text.className = "et-place-main-text";
					et_place_text_wrapper.appendChild(et_place_main_text);
					
					
					var scrollbar = document.createElement("div");
					scrollbar.className = "scrollbar disable";
					scrollbar.style.height = "0px";
					et_place_main_text.appendChild(scrollbar);
					
					
					var track = document.createElement("div");
					track.className = "track";
					track.style.height = "0px";
					scrollbar.appendChild(track);
					
					
					var thumb = document.createElement("div");
					thumb.className = "thumb";
					thumb.style.height = "0px";
					track.appendChild(thumb);
					

					var end = document.createElement("div");
					end.className = "end";
					thumb.appendChild(end);
					
					
					var viewport = document.createElement("div");
					viewport.className = "viewport";
					viewport.style.marginTop = "0px";
					viewport.style.height = "0px";
					et_place_main_text.appendChild(viewport);
					
					
					var overview = document.createElement("div");
					overview.className = "overview";
					overview.style.top = "0px";
					viewport.appendChild(overview);
					
					
					var pTag = document.createElement("p");
					overview.appendChild(pTag);
					
					var div = document.createElement("div");
					div.style.width = "100%";
					div.style.height = "320px";
					div.style.backgroundColor = "black";
					et_place_content.appendChild(div);
					
					var object = document.createElement("object");
					object.type = "application/x-shockwave-flash";
					object.name = "StrobeMediaPlayback_" + (progInfo.length - 1);
					object.data = "<?php echo get_template_directory_uri()?>/StrobeMediaPlayback.swf";
					object.style.width = "480px";
					object.style.height = "320px";
					object.id = "video_player";
					object.style.visibility = "visible";
					div.appendChild(object);
					

					var param = document.createElement("param");
					param.name = "allowFullScreen";
					param.value = "true";
					object.appendChild(param);

					var param = document.createElement("param");
					param.name = "wmode";
					param.value = "opaque";
					object.appendChild(param);

					var param = document.createElement("param");
					param.name = "flashvars";
					
					if(progInfo[ progInfo.length - 1 ].end_time == "")
					{
						if(progInfo[ progInfo.length - 1 ].isMobile == "0")
						{
							param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream/" + progInfo[ progInfo.length - 1 ].cid + "_" + progInfo[ progInfo.length - 1 ].pid + "_" + progInfo[ progInfo.length - 1 ].user_id + "&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000";
						}
						else
						{
							param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livemobile/" + progInfo[ progInfo.length - 1 ].cid + "_" + progInfo[ progInfo.length - 1 ].pid + "_" + progInfo[ progInfo.length - 1 ].user_id + "&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000";
						}	
					}
					else
					{
						var vod_path = progInfo[ progInfo.length - 1 ].vod_path.trim();
						var realPath = vod_path.split(",")[0];
						param.value = "src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/vod/mp4:" + realPath + "&amp;autoPlay=true&amp;streamType=vod&amp;bgColor=#000000";
					} 
					object.appendChild(param);
					alert(22222);
				}	
			}
		}
		
		xmlhttp.open("GET","<?php echo get_template_directory_uri()?>/ajaxemapshow.php?program_id=" + program_id,true);
		xmlhttp.send();

		
		
	}, 3000);
	
	})(jQuery)

function onEmapClick(pid,cid,vodLive,obj)
{
	
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
	
	var ch = document.getElementById(cid);
	var inputTag = ch.getElementsByTagName("input");
	var channelUrlStr = inputTag[0].value;

	document.getElementById("hidChannelId").value = cid;
	
	
	
	if(pid)
		document.getElementById("programId").value = pid;
	
	document.aform.action = channelUrlStr;
	document.aform.submit();
	
	//location.href = channelUrlStr + "?pid=" + pid + "&cid=" + cid;
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

function emap_pollingss()
{
	
	
}
</script>

<form name="aform" method="post" style="width:100%;height:100%;display:none;">
<input type="hidden" id="hidMenus" name="hidMenus" value="">
<input type="hidden" id="hidFlags" name="hidFlags" value="">
<input type="hidden" id="hidChannelId" name="hidChannelId" value="">
<input type="hidden" id="channelIds" name="channelIds" value="">
<input type="hidden" id="liveVod" name="liveVod" value="" >
<input type="hidden" id="programId" name="programId" value="" >
</form>
<?php if (sizeof($emap_items) > 0) {
?>
<div id="et-slider-wrapper" class="et-map-post ui-draggable">
	<div id="et-map-slides">
	<?php
	$i = 0; 
	foreach ($emap_items as $row => $values) {
	
?>
		<div class="et-map-slide <?php if ($row == 0) echo 'et-active-map-slide'; ?>">
			<div class="thumbnail" style="height:230px;background-color:rgba(0,0,0,0.2);">
				<div class="et-description" style="margin-top: -50px;">
					<h1 style="font-size: 28px;"><a href= "javascript:void(0);"  
					><?php echo $values->title;?></a></h1>				
					<p id="timeShow<?php echo $i?>" style="font-size: 12px;">
						<script>
					  
						var clientStartTime = new Date(fn_dateConvert("<?php echo $values->start_time?>"));
						var clientEndTime = new Date(0);

						//fn_dateConvert("<?php //echo $values->end_time?>")
						
						year = clientStartTime.getFullYear();
						month = clientStartTime.getMonth() + 1;
						intDate = clientStartTime.getDate();

						hours = clientStartTime.getHours();
						minutes = clientStartTime.getMinutes();
						seconds = clientStartTime.getSeconds();

						if(month.toString().length == 1)
							month = "0" + month;
						if(intDate.toString().length == 1)
							intDate = "0" + intDate;
						if(hours.toString().length == 1)
							hours = "0" + hours;

						if(minutes.toString().length == 1)
							minutes = "0" + minutes;
						if(seconds.toString().length == 1)
							seconds = "0" + seconds;

						document.getElementById("timeShow<?php echo $i?>").innerHTML += "Start Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;

						if("<?php echo $values->end_time?>" != "")
						{
							clientEndTime.setTime(fn_dateConvert("<?php echo $values->end_time?>"));
							
							year = clientEndTime.getFullYear();
							month = clientEndTime.getMonth() + 1;
							intDate = clientEndTime.getDate();
	
							hours = clientEndTime.getHours();
							minutes = clientEndTime.getMinutes();
							seconds = clientEndTime.getSeconds();
	
							if(month.toString().length == 1)
								month = "0" + month;
							if(intDate.toString().length == 1)
								intDate = "0" + intDate;
							if(hours.toString().length == 1)
								hours = "0" + hours;
	
							if(minutes.toString().length == 1)
								minutes = "0" + minutes;
							if(seconds.toString().length == 1)
								seconds = "0" + seconds;
	
							document.getElementById("timeShow<?php echo $i?>").innerHTML += "<br>" + "End Time : " + year + "-" + month + "-" + intDate + " " + hours + ":" + minutes + ":" + seconds;
						}
					  </script>
					</p>
					<p style="font-size: 12px;"><?php echo $values->description;?></p>
					<span class="et-rating"><span style="width: <?php echo $values->vote * 20;?>px;"></span></span>
					<div style="margin-top:5px;">
						<input type="button" name="emapValues" value="Go to watch" style="cursor:pointer; width:100px; border-left:0px; border-top:0px; height:35px;  color:#ffffff; border-radius:10px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');" onclick="onEmapClick('<?php echo $values->pid?>','<?php echo $values->cid?>','<?php echo $values->vod_enable?>',this)">
					</div>
				</div>
				<div class="et-date-wrapper">
					<span class="et-date"><?php echo date("F j");?><span><?php echo date("Y");?></span></span></div>
			</div>			
			<!-- <div class="et-map-postmeta"><?php //echo et_get_emap_location($values->lat, $values->lgt); ?></div>  -->
		
			<div class="et-place-content" style="height: 0px;padding: 0px; border: 0px;">
				<div class="et-place-text-wrapper">
					<div class="et-place-main-text">
						<div class="scrollbar disable" style="height: 0px;"><div class="track" style="height: 0px;"><div class="thumb" style="height: 0px;"><div class="end"></div></div></div></div>
						<div class="viewport" style="margin-top: 0px;height: 0px;">
							<div class="overview" style="top: 0px;">
								<p></p>
							</div>
						</div>
					</div> <!-- .et-place-main-text -->
				</div><!-- .et-place-text-wrapper -->
				<!-- <a class="more" href="#">More Information<span>»</span></a> -->
				<div style="width: 100%;height: 320px;background-color:black;">
				
				
				<object type="application/x-shockwave-flash" name="StrobeMediaPlayback_<?php echo $i;?>" data="<?php echo get_template_directory_uri()?>/StrobeMediaPlayback.swf" width="480" height="320" id="video_player" style="visibility: visible;">
					<param name="allowFullScreen" value="true">
					<param name="wmode" value="opaque">
					<param name="flashvars" value="<?php if ($values->end_time == "") {
						if($values->isMobile == "0")
							echo 'src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livestream/'.$values->cid.'_'.$values->pid.'_'.$values->userid.'&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000';
						else
							echo 'src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/livemobile/'.$values->cid.'_'.$values->pid.'_'.$values->userid.'&amp;autoPlay=true&amp;streamType=live&amp;bgColor=#000000';
					} else {
						
						echo 'src=rtmp://ec2-50-112-62-239.us-west-2.compute.amazonaws.com:1935/vod/mp4:'.get_vod_title($values->vod_path).'&amp;autoPlay=true&amp;streamType=vod&amp;bgColor=#000000';
					}?>">
				</object>
				
				</div>
			</div> <!-- .et-place-content -->
		</div> <!-- .et-map-slide -->
		<?php
		$i++; 
}?>
	</div> <!-- #et-map-slides -->
	<div class="et-slider-arrows">
		<a class="et-arrow-prev" href="#">Previous</a>
		<a class="et-arrow-next" href="#">Next</a>
	</div>
	<div id="et-map-slide-clone" style="display:none">
		<div class="thumbnail" style="height:230px;background-color:rgba(0,0,0,0.2);">
			<div class="et-description" style="margin-top: -50px;">
				<h1 style="font-size: 28px;"><a href= "javascript:void(0);"></a></h1>				
				<p id="timeShow" style="font-size: 12px;">						
				</p>
				<p style="font-size: 12px;"></p>
				<span class="et-rating"><span></span></span>
				<div style="margin-top:5px;">
					<input type="button" name="emapValues" value="Go to watch" style="cursor:pointer; width:100px; border-left:0px; border-top:0px; height:35px;  color:#ffffff; border-radius:10px; background: url('<?php echo get_template_directory_uri()?>/images/login1.png');" onclick="onEmapClick('<?php echo $values->pid?>','<?php echo $values->cid?>','<?php echo $values->vod_enable?>',this)">
				</div>
			</div>
			<div class="et-date-wrapper">
				<span class="et-date"><span></span></span></div>
			</div>			
		
			<div class="et-place-content" style="height: 0px;padding: 0px; border: 0px;">
				<div class="et-place-text-wrapper">
					<div class="et-place-main-text">
						<div class="scrollbar disable" style="height: 0px;">
							<div class="track" style="height: 0px;">
								<div class="thumb" style="height: 0px;"><div class="end">
								</div>
							</div>
						</div>
					</div>
					<div class="viewport" style="margin-top: 0px;height: 0px;">
						<div class="overview" style="top: 0px;">
							<p></p>
						</div>
					</div>
				</div> <!-- .et-place-main-text -->
			</div><!-- .et-place-text-wrapper -->
			<!-- <a class="more" href="#">More Information<span>≫</span></a> -->
			<div style="width: 100%;height: 320px;background-color:black;">
				<object type="application/x-shockwave-flash" name="StrobeMediaPlayback" data="<?php echo get_template_directory_uri()?>/StrobeMediaPlayback.swf" width="480" height="320" id="video_player" style="visibility: visible;">
					<param name="allowFullScreen" value="true">
					<param name="wmode" value="opaque">
					<param name="flashvars" value="">
				</object>				
			</div>
		</div> <!-- .et-place-content -->
	</div> <!-- .et-map-slide -->
</div>
<?php } ?>

<?php get_footer();?>

