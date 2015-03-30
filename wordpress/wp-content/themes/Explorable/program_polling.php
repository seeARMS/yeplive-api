<?php
/*

Template name: program_polling

*/
	require_once(ABSPATH . WPINC . '/registration.php');
	global $wpdb, $user_ID;
	
	$channel_id = $_GET["channel_id"];
	$date = date("Y-m-d H:i:s");
	$voddate = date("Y-m-d H:i:s",time() - 24 * 60 * 60);
	
	$sql_query = "select
		lp.program_id pid,
		lp.channel_id cid,
		lp.user_id uid,
		lp.title title,
		lp.image_path image_path,
		lp.start_time start_time,
		lp.end_time end_time,
		lp.vod_enable vod_enable,
		lp.vod_path vod_path,
		lp.vote vote,
		lp.latitude lat,
		lp.longitude lgt,
		lp.connect_count,
		lp.isMobile,
		lu.picture_path,
		lu.facebook_id,
		lu.facebook_name,
		lu.twitter_id,
		lu.twitter_img,
		lu.twitter_name,
		wu.user_login
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
				lp.channel_id = '" . $channel_id . "'
		and
			(lp.start_time is not null and lp.end_time is null
		or
			(lp.end_time
		between
				'" . $voddate . "' and '" . $date . "'
		and lp.vod_enable = '1')
					)
		order by lp.start_time asc";

		$result = $wpdb->get_results($sql_query);//mysql_query($sql_query);
		
		$returnValue = "";
		
		foreach($result as $i => $row)
		{
			$returnValue = $returnValue . $row->pid . "&";
			$returnValue = $returnValue . $row->cid . "&";
			$returnValue = $returnValue . $row->uid . "&";
			$returnValue = $returnValue . $row->title . "&";
			if($row->image_path != "")
				$returnValue = $returnValue . home_url() . $row->image_path . "&";
			else
				$returnValue = $returnValue . " " . "&";
			
			$returnValue = $returnValue . $row->start_time . "&";
			
			if($row->end_time == "")
				$end_time = " ";
			else 
				$end_time = $row->end_time;
			$returnValue = $returnValue . $end_time . "&";
			$returnValue = $returnValue . $row->vod_enable . "&";
			$returnValue = $returnValue . $row->vod_path . "&";
			$returnValue = $returnValue . $row->vote . "&";
			$returnValue = $returnValue . $row->latitude . "&";
			$returnValue = $returnValue . $row->longitude . "&";
			$returnValue = $returnValue . $row->connect_count . "&";
			$returnValue = $returnValue . $row->facebook_id . "&";
			$returnValue = $returnValue . $row->facebook_name . "&";
			$returnValue = $returnValue . $row->twitter_id . "&";
			$returnValue = $returnValue . $row->twitter_img . "&";
			$returnValue = $returnValue . $row->twitter_name . "&";
			$returnValue = $returnValue . $row->user_login . "&";
			$returnValue = $returnValue . $row->isMobile . "&";
			if($row->picture_path != "")
				$returnValue = $returnValue . home_url() . $row->picture_path . "~";
			else
				$returnValue = $returnValue . " " . "~";
			
		}
		echo $returnValue;
?>