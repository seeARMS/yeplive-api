<?php
/*

Template name: twitter_notify

*/

/* Start session and load lib */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');
require_once('wp-content/themes/Explorable/userMgm.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

$content = $connection->get('account/verify_credentials');

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';
  //header('Location: ./index.php');
  
  
  $_SESSION['twitter_id'] = $access_token['user_id'];
  $_SESSION['twitter_name'] = $access_token['screen_name'];
  $_SESSION['twitter_oauthToken'] = $access_token['oauth_token'];
  $_SESSION['twitter_oauthTokenSecret'] = $access_token['oauth_token_secret'];
  $_SESSION['twitter_img'] = str_replace("_normal", "", $content->profile_image_url);
  
  global $wpdb;
  
 
  $current_user = wp_get_current_user();
  $wp_user_id = $current_user->ID;
  
  if ($wp_user_id != 0)
  {
    $rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE twitter_id = '" . $_SESSION['twitter_id'] . "'");

//    if (count($rows) == 1)
//    {
//        $row = $rows[0];
//        $userMgm = new UserMgm;
//        $userMgm->moveUserId($row->user_id, $_SESSION['user_id']);
//    }

    $wpdb->query( $wpdb->prepare(
            "UPDATE wp_user_yep SET twitter_id = %s, twitter_name = %s, twitter_oauth_token = %s, twitter_oauth_token_secret = %s, twitter_img = %s WHERE wp_user_id = %d",
            $_SESSION['twitter_id'],
            $_SESSION['twitter_name'],
            $_SESSION['twitter_oauthToken'],
            $_SESSION['twitter_oauthTokenSecret'],
            $_SESSION['twitter_img'],
            $wp_user_id
    ));

    $rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE wp_user_id = " . $wp_user_id);

    $row = $rows[0];

      if ($rows[0]->diff != null)
          if (strpos($rows[0]->diff, "-") !== 0)
              $banned = $rows[0]->bannedUntil;
      if($banned != "")
      {
          echo "You have been banned until " . $banned . " (server time)";
          exit;
      }

    $_SESSION['user_id'] = $row->user_id;
  }
  else
  {

  	if (isset($_SESSION['user_id']))
  	{
  		$rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE twitter_id = '" . $_SESSION['twitter_id'] . "'");

        if ($rows[0]->diff != null)
            if (strpos($rows[0]->diff, "-") !== 0)
                $banned = $rows[0]->bannedUntil;
        if($banned != "")
        {
            echo "You have been banned until " . $banned . " (server time)";
            exit;
        }
  		
//  		if (count($rows) == 1)
//  		{
//  			$row = $rows[0];
//  			$userMgm = new UserMgm;
//  			if ($row->wp_user_id == null)
//			{
//				$userMgm->moveUserId($row->user_id, $_SESSION['user_id']);
//			}
//			else
//			{
//				$userMgm->moveUserId($_SESSION['user_id'], $row->user_id);
//				$_SESSION['user_id'] = $row->user_id;
//			}
//  		}
  		
  		$wpdb->query( $wpdb->prepare(
  				"UPDATE wp_user_yep SET twitter_id = %s, twitter_name = %s, twitter_oauth_token = %s, twitter_oauth_token_secret = %s, twitter_img = %s WHERE user_id = %d",
  				$_SESSION['twitter_id'],
  				$_SESSION['twitter_name'],
  				$_SESSION['twitter_oauthToken'],
  				$_SESSION['twitter_oauthTokenSecret'],
  				$_SESSION['twitter_img'],
  				$_SESSION['user_id']
  		));
  	
  		$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE user_id = " . $_SESSION['user_id']);
  	
  		$row = $rows[0];
  	}
  	else
  	{
  		$rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE twitter_id = '" . $_SESSION['twitter_id'] . "' ORDER BY user_id DESC");
  	
  		if (count($rows) < 1)
  		{
  			$wpdb->query( $wpdb->prepare(
  					"INSERT INTO wp_user_yep (group_id, twitter_id, twitter_name, twitter_oauth_token, twitter_oauth_token_secret, twitter_img) values (%d, %s, %s, %s, %s, %s)",
  					3,	// 1: admin, 2 : publisher, 3 : user
  					$_SESSION['twitter_id'],
  					$_SESSION['twitter_name'],
  					$_SESSION['twitter_oauthToken'],
  					$_SESSION['twitter_oauthTokenSecret'],
  					$_SESSION['twitter_img']
  			));
  	
  			$rows = $wpdb->get_results("SELECT *, TIMEDIFF(bannedUntil, NOW()) as diff FROM wp_user_yep WHERE twitter_id = '" . $_SESSION['twitter_id'] . "'");
  	
  		}

        if ($rows[0]->diff != null)
            if (strpos($rows[0]->diff, "-") !== 0)
                $banned = $rows[0]->bannedUntil;
        if($banned != "")
        {
            echo "You have been banned until " . $banned . " (server time)";
            exit;
        }

  		$row = $rows[0];
  		$_SESSION['user_id'] = $row->user_id;
  	}

  }
    
  if ($row->facebook_id != null)
  {
  	$_SESSION['facebook_login'] = 1;
  	$_SESSION['facebook_id'] = $row->facebook_id;
  	$_SESSION['facebook_name'] = $row->facebook_name;
  	$_SESSION['facebook_email'] = $row->facebook_email;
  	$_SESSION['facebook_accessToken'] = $row->facebook_access_token;
  }


  
  echo 'Successfully logged in.';
  
  $_SESSION['twitter_login'] = 1;
  
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  //header('Location: ./clearsessions.php');
  echo 'Cannot login to twitter.';
}
