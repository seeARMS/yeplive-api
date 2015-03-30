<?php

class UserMgm {
	
	function moveUserId($oldId, $newId) {
		global $wpdb;
		
		$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE user_id = " . $oldId);
		$row = $rows[0];
		
		$updateSql = "UPDATE wp_user_yep SET ";
		
		if ($row->facebook_id != null)
		{
			$updateSql .= "facebook_id = '$row->facebook_id', ";
			$updateSql .= "facebook_name = '$row->facebook_name', ";
			$updateSql .= "facebook_email = '$row->facebook_email', ";
			$updateSql .= "facebook_access_token = '$row->facebook_access_token' ";
		}
		
		if ($row->twitter_id != null)
		{
			if ($row->facebook_id != null)
			{
				$updateSql .= ", ";
			}
			
			$updateSql .= "twitter_id = '$row->twitter_id', ";
			$updateSql .= "twitter_name = '$row->twitter_name', ";
			$updateSql .= "twitter_oauth_token = '$row->twitter_oauth_token', ";
			$updateSql .= "twitter_oauth_token_secret = '$row->twitter_oauth_token_secret', ";
			$updateSql .= "twitter_img = '$row->twitter_img' ";
		}
		
		$updateSql .= "WHERE user_id = " . $newId;
		
		$wpdb->query($updateSql);
		
		$updateSql = "UPDATE wp_user_pans SET user_id = $newId WHERE user_id = $oldId";
		
		$wpdb->query($updateSql);
		
		$updateSql = "UPDATE wp_user_pans SET pan_id = $newId WHERE pan_id = $oldId";
		
		$wpdb->query($updateSql);
		
		$updateSql = "UPDATE wp_program SET user_id = $newId WHERE user_id = $oldId";
		
		$wpdb->query($updateSql);
		
		$updateSql = "DELETE FROM wp_user_yep WHERE user_id = $oldId";
		
		$wpdb->query($updateSql);

	}
}