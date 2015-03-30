<?php

/*
 * Plugin Name: wp-webservices
 * Description: This plugin extends the basic webservices exposed by WordPress
 * Version: 1.0
 * Author: Prasath Nadarajah
 * Author URI: http://nprasath.com
 *
*/


add_filter( 'xmlrpc_methods' , 'newMethods' );

/*
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/class-phpass.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/pluggable.php');
*/

function newMethods ( $methods ) {

    // user management
    $methods['mb.login']                   = 'mb_login';
    $methods['mb.getServerInfo']           = 'mb_getServerInfo';
    $methods['mb.getChannelList']          = 'mb_getChannelList';
    $methods['mb.getProgramList']          = 'mb_getProgramList';
    $methods['mb.getProgramAllList']       = 'mb_getProgramAllList';
    $methods['mb.getProgramRestList']      = 'mb_getProgramRestList';
    $methods['mb.getPublishList']          = 'mb_getPublishList';
    $methods['mb.createProgram']           = 'mb_createProgram';
    $methods['mb.updateImage']             = 'mb_updateImage';
    $methods['mb.endProgram']              = 'mb_endProgram';
    $methods['mb.getProgramInfo']          = 'mb_getProgramInfo';
    $methods['mb.deleteProgram']           = 'mb_deleteProgram';
    $methods['mb.cutVideo']                = 'mb_cutVideo';
    $methods['mb.checkNotifications']      = 'mb_checkNotifications';
    $methods['mb.getNotifications']        = 'mb_getNotifications';
    $methods['mb.setNotificationsIsNotNew']= 'mb_setNotificationsIsNotNew';
    $methods['mb.deleteNotification']      = 'mb_deleteNotification';
    $methods['mb.getUserBroadcasts']       = 'mb_getUserBroadcasts';
    $methods['mb.getUserFollowing']        = 'mb_getUserFollowing';
    $methods['mb.getUserFollowers']        = 'mb_getUserFollowers';
    $methods['mb.generateRandomThumbnail'] = 'mb_generateRandomThumbnail';
    $methods['mb.unfollowUser']            = 'mb_unfollowUser';
    $methods['mb.getUserInfo']             = 'mb_getUserInfo';
    $methods['mb.getTagsPopularity']       = 'mb_getTagsPopularity';
    $methods['wz.setServerStart']          = 'wz_setServerStart';
    $methods['wz.setServerEnd']            = 'wz_setServerEnd';
    $methods['wz.setStreamStart']          = 'wz_setStreamStart';
    $methods['wz.setStreamEnd']            = 'wz_setStreamEnd';


    return $methods;
}

class GetChannelListResp {
	public $successFlag;
	public $failMessage;
	public $categoryList;
}

class CategoryList {
    public $categoryId;
    public $categoryName;
    public $channelList;
}

class ChannelList {
    public $channelId;
    public $channelName;
    public $connectCount;
    public $isLive;
}

function mb_getChannelList( $args )
{
	global $wpdb;
	
    $response = new GetChannelListResp;
    
    $response->successFlag = 'TRUE';
    
    $curTime = date('Y-m-d H:i:s', time());
    
    $rows = $wpdb->get_results("SELECT b.category_id, b.category_name, a.channel_id, a.channel_name, 
    									(SELECT count(*) FROM wp_program WHERE channel_id = a.channel_id AND start_time IS NOT NULL AND end_time IS NULL) is_live 
								FROM wp_channel a, wp_category b 
								WHERE a.category_id = b.category_id
								ORDER BY category_id, channel_id");
    
    $prevCategoryId = null;
    $indexCategory = 0;
    $indexChannel = 0;
	foreach ( $rows as $row ) {
    	if ($prevCategoryId == null || $prevCategoryId != $row->category_id)
    	{
    		$response->categoryList[$indexCategory] = new CategoryList;
    		$response->categoryList[$indexCategory]->categoryId = $row->category_id;
    		$response->categoryList[$indexCategory]->categoryName = $row->category_name;
    		
    		$indexCategory++;
    		$indexChannel = 0;
    		$prevCategoryId = $row->category_id;
    	}
    	
    	$response->categoryList[$indexCategory-1]->channelList[$indexChannel] = new ChannelList;
    	$response->categoryList[$indexCategory-1]->channelList[$indexChannel]->channelId = $row->channel_id;
    	$response->categoryList[$indexCategory-1]->channelList[$indexChannel]->channelName = $row->channel_name;
    	$response->categoryList[$indexCategory-1]->channelList[$indexChannel]->isLive = $row->is_live;
    	$indexChannel++;
    }
	
    return $response;
}

class LoginResp {
	public $successFlag;
	public $failMessage;
	public $userName;
	public $UID;
	public $userGroup;
    public $userpic;
	 
}

function mb_login( $args)
{
	/*------------- wordpress login ----------------
	
	$args[0] :	logintype
	$args[1] :	id
	$args[2] :	password
	
	------------- wordpress login ----------------*/
	
	/*------------- facebook login ----------------
	
	$args[0] :	logintype
	$args[1] :  facebook_token
	$args[2] :	facebook_id
	$args[3] :	facebook_name
	$args[4] :  facebook_email
	$args[5] :  facebook_img
	$args[6] :  facebook_friends
	
	------------- facebook login ----------------*/
	
	/*------------- twitter login ----------------
	
	$args[0] :	logintype
	$args[1] :  twitter_oauth_token
	$args[2] :	twitter_id
	$args[3] :	twitter_name
	$args[4] :  twitter_token_secret
	$args[5] :  twitter_img
	
	------------- twitter login ----------------*/
	global $wpdb;
	
	$response = new LoginResp;
	
	$loginType = $args[0];
	
	if ($loginType == 's')
	{
		$id = $args[1];
		$password = $args[2];
		
		$user = get_user_by( 'login', $id );
		if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID))
		{
			$rows = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE wp_user_id = " . $user->ID);
			
			$row = $rows[0];
			$response->successFlag = 'TRUE';
			$response->userName = $id;
			$response->UID = $row->user_id;
			$response->userGroup = 'publisher';
            if ($row->picture_path != null) $response->userpic = DOMAIN_ROOT . $row->picture_path;
            else $response->userpic = $row->picture_path;
		}
		else 
		{
			$response->successFlag = 'FALSE';
			$response->failMessage = 'Invalid id or password.';
		}
	}
	else if($loginType == 'f')
	{
		$facebook_token = $args[1];
		$facebook_id = $args[2];
		$facebook_name = $args[3];
		$facebook_email = $args[4];
		$facebook_img = $args[5];
		$facebook_friends = $args[6];
		
		$result = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE facebook_id = '" . $facebook_id . "'");
		
		if(count($result) > 0)
		{
			$row = $result[0];
			$response->successFlag = 'TRUE';
			$response->userName = $facebook_name;
			$response->UID = $row->user_id;
			$response->userGroup = 'publisher';
            $response->userpic = $facebook_img;
		}
		else
		{
			$wpdb->query($wpdb->prepare(
					"INSERT INTO wp_user_yep (group_id, facebook_id, facebook_name, facebook_email, facebook_access_token, facebook_friends, facebook_picture) values (%d, %s, %s, %s, %s, %d, %s)",
					2,	// 1: admin, 2 : publisher, 3 : user
					$facebook_id,
					$facebook_name,
					$facebook_email,
					$facebook_token,
					$facebook_friends,
					$facebook_img
			));
			
			$result = $wpdb->get_results("SELECT user_id FROM wp_user_yep WHERE facebook_id = '" . $facebook_id . "'");
			
			$response->successFlag = 'TRUE';
			$response->userName = $facebook_name;
			$response->UID = $result[0]->user_id;
			$response->userGroup = 'publisher';
            $response->userpic = $result[0]->facebook_picture;
			
		}
	}
	else if($loginType == 't')
	{
		$twitter_token = $args[1];
		$twitter_id = $args[2];
		$twitter_name = $args[3];
		$twitter_token_secret = $args[4];
		$twitter_img = $args[5];
		
		$result = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE twitter_id = '" . $twitter_id . "'");
		
		if(count($result) > 0)
		{
			$row = $result[0];
			$response->successFlag = 'TRUE';
			$response->userName = $twitter_name;
			$response->UID = $row->user_id;
			$response->userGroup = 'publisher';
            $response->userpic = $result[0]->twitter_img;
		}
		else
		{
			$wpdb->query( $wpdb->prepare(
  					"INSERT INTO wp_user_yep (group_id, twitter_id, twitter_name, twitter_oauth_token, twitter_oauth_token_secret, twitter_img) values (%d, %s, %s, %s, %s, %s)",
  					2,	// 1: admin, 2 : publisher, 3 : user
  					$twitter_id,
  					$twitter_name,
  					$twitter_token,
  					$twitter_token_secret,
  					$twitter_img
  			));
  	
  			$result = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE twitter_id = '" . $twitter_id . "'");
				
			$response->successFlag = 'TRUE';
			$response->userName = $twitter_name;
			$response->UID = $result[0]->user_id;
			$response->userGroup = 'publisher';
            $response->userpic = $result[0]->twitter_img;
				
		}
	}
    else if($loginType == 'g')
    {
        $google_token = $args[1];
        $google_name = $args[2];
        $google_picture = $args[3];
        $google_email = $args[4];


        $result = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE google_email = '" . $google_email . "'");

        if(count($result) == 1)
        {
            $row = $result[0];
            $response->successFlag = 'TRUE';
            $response->userName = $google_name;
            $response->UID = $row->user_id;
            $response->userGroup = 'publisher';
            $response->userpic = $result[0]->google_picture;
        }
        else
        {
            $wpdb->query($wpdb->prepare(
                "INSERT INTO wp_user_yep (group_id, google_name, google_access_token, google_email, google_picture) values (%d, %s, %s, %s, %s)",
                2,	// 1: admin, 2 : publisher, 3 : user
                $google_name,
                $google_token,
                $google_email,
                $google_picture
            ));

            $result = $wpdb->get_results("SELECT * FROM wp_user_yep WHERE google_email = '" . $google_email . "'");

            $response->successFlag = 'TRUE';
            $response->userName = $google_name;
            $response->UID = $result[0]->user_id;
            $response->userGroup = 'publisher';
            $response->userpic = $result[0]->google_picture;

        }
    }
	else
	{
		$response->successFlag = 'FALSE';
		$response->failMessage = 'Unsupported login type.';
	}
	
    return $response;
}

class GetServerInfoResp {
	public $successFlag;
	public $failMessage;
	public $serverIp;
	public $serverPort;
}

function mb_getServerInfo( $args)
{
	$response = new GetServerInfoResp;
	
	$response->successFlag = 'TRUE';
	//$response->serverIp = $_SERVER['SERVER_NAME'];
	//$response->serverPort = 1000;
	
	$response->serverIp = SERVER_ADDRESS;
	$response->serverPort = 10216;
	
	return $response;
}

class GetProgramListResp {
	public $successFlag;
	public $failMessage;
	public $programList;
}

class ProgramList {
	public $programId;
	public $channelId;
	public $channelName;
	public $type;
	public $title;
	public $startTime;
	public $endTime;
	public $publisherName;
	public $pathList;
	public $description;
	public $vote;
	public $connectCount;
	public $latitude;
	public $longitude;
	public $imageUrl;
}

class PathList {
	public $pathName;
	public $pathUrlHLS;
	public $pathUrlHDS;
}


function mb_getProgramList( $args)
{
    global $wpdb;
	
    if (is_array($args))
    {
    	$channelId = $args[0];
    }
    else
    {
    	$channelId = $args;
    }	
    
    $response = new GetProgramListResp;
    
    $response->successFlag = 'TRUE';
    
    $curTime = date('Y-m-d H:i:s', time());
    
    $rows = $wpdb->get_results("SELECT program_id, channel_id, (SELECT channel_name FROM wp_channel WHERE channel_id = a.channel_id) as channel_name, title, IFNULL(vod_path, '') vod_path, vote, start_time, end_time, description, connect_count,
    								   CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
    										WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
    										ELSE b.twitter_name END user_login,
									   CASE WHEN end_time IS NULL THEN 'live' ELSE 'vod' END type, 
    									b.user_id ID, a.latitude, a.longitude, a.isMobile,
    									a.image_path, b.picture_path, b.facebook_id, b.twitter_img
								FROM wp_program a, wp_user_yep b
								WHERE start_time >= DATE_SUB('$curTime', INTERVAL 1 DAY)
								AND (end_time IS NULL OR end_time <= '$curTime' AND a.vod_enable = 1)
								AND a.user_id = b.user_id
    							AND a.channel_id = '$channelId'");
    
    $indexProgram = 0;
	foreach ( $rows as $row ) {
		$response->programList[$indexProgram] = new ProgramList;
		$response->programList[$indexProgram]->programId = $row->program_id;
		$response->programList[$indexProgram]->channelId = $row->channel_id;
		$response->programList[$indexProgram]->channelName = $row->channel_name;
		$response->programList[$indexProgram]->type = $row->type;
		$response->programList[$indexProgram]->title = $row->title;
		$response->programList[$indexProgram]->startTime = $row->start_time;
		$response->programList[$indexProgram]->endTime = $row->end_time;
		$response->programList[$indexProgram]->publisherName = $row->user_login;
		if ($row->type == 'vod')
		{
			$files = explode(",", $row->vod_path);
			$indexFile = 0;
			foreach ( $files as $file ) {
				$response->programList[$indexProgram]->pathList[$indexFile] = new PathList;
				$streamID = $file;
				$serverDomain = HTTP_WOWZA . 'vod/mp4:';
				$response->programList[$indexProgram]->pathList[$indexFile]->pathName = $streamID;
				$response->programList[$indexProgram]->pathList[$indexFile]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
				$response->programList[$indexProgram]->pathList[$indexFile]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
				$indexFile++;
			}
		}
		else
		{
			$response->programList[$indexProgram]->pathList[0] = new PathList;
			$streamID = $row->channel_id . '_' . $row->program_id . '_' . $row->ID;
			if ($row->isMobile == 0)
				$serverDomain = HTTP_WOWZA . 'livestream/';
			else
				$serverDomain = HTTP_WOWZA . 'livemobile/';
			$response->programList[$indexProgram]->pathList[0]->pathName = $streamID;
			$response->programList[$indexProgram]->pathList[0]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
			$response->programList[$indexProgram]->pathList[0]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
		}
		$response->programList[$indexProgram]->description = $row->description;
		$response->programList[$indexProgram]->vote = $row->vote;
		$response->programList[$indexProgram]->connectCount = $row->connect_count;
		$response->programList[$indexProgram]->latitude = $row->latitude;
		$response->programList[$indexProgram]->longitude = $row->longitude;
		if ($row->image_path != null && $row->image_path != '')
		{
			$response->programList[$indexProgram]->imageUrl = DOMAIN_ROOT. $row->image_path;
		}
		else if ($row->picture_path != null && $row->picture_path != '')
		{
			$response->programList[$indexProgram]->imageUrl = DOMAIN_ROOT . $row->picture_path;
		}
		else if ($row->facebook_id != null && $row->facebook_id != '')
		{
			$response->programList[$indexProgram]->imageUrl = 'https://graph.facebook.com/' . $row->facebook_id . '/picture/?type=large';
		}
		else if ($row->twitter_img != null && $row->twitter_img != '')
		{
			$response->programList[$indexProgram]->imageUrl = $row->twitter_img;
		}
		
    	$indexProgram++;
    }
	
    return $response;
}

function mb_getProgramRestList( $args)
{
	global $wpdb;

	$channelId = $args[0];
	$programId = $args[1];

	$response = new GetProgramListResp;

	$response->successFlag = 'TRUE';

	$curTime = date('Y-m-d H:i:s', time());

	$rows = $wpdb->get_results("SELECT program_id, channel_id, (SELECT channel_name FROM wp_channel WHERE channel_id = a.channel_id) as channel_name, title, IFNULL(vod_path, '') vod_path, vote, start_time, end_time, description, connect_count,
			CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
			WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
			ELSE b.twitter_name END user_login,
			CASE WHEN end_time IS NULL THEN 'live' ELSE 'vod' END type,
			b.user_id ID, a.latitude, a.longitude, a.isMobile,
			a.image_path, b.picture_path, b.facebook_id, b.twitter_img
			FROM wp_program a, wp_user_yep b
			WHERE start_time >= DATE_SUB('$curTime', INTERVAL 1 DAY)
			AND (end_time IS NULL OR end_time <= '$curTime' AND a.vod_enable = 1)
			AND a.user_id = b.user_id
			AND a.channel_id = '$channelId'
			AND a.program_id != '$programId'");

	$indexProgram = 0;
	foreach ( $rows as $row ) {
		$response->programList[$indexProgram] = new ProgramList;
		$response->programList[$indexProgram]->programId = $row->program_id;
		$response->programList[$indexProgram]->channelId = $row->channel_id;
		$response->programList[$indexProgram]->channelName = $row->channel_name;
		$response->programList[$indexProgram]->type = $row->type;
		$response->programList[$indexProgram]->title = $row->title;
		$response->programList[$indexProgram]->startTime = $row->start_time;
		$response->programList[$indexProgram]->endTime = $row->end_time;
		$response->programList[$indexProgram]->publisherName = $row->user_login;
		if ($row->type == 'vod')
		{
			$files = explode(",", $row->vod_path);
			$indexFile = 0;
			foreach ( $files as $file ) {
				$response->programList[$indexProgram]->pathList[$indexFile] = new PathList;
				$streamID = $file;
				$serverDomain = HTTP_WOWZA . 'vod/mp4:';
				$response->programList[$indexProgram]->pathList[$indexFile]->pathName = $streamID;
				$response->programList[$indexProgram]->pathList[$indexFile]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
				$response->programList[$indexProgram]->pathList[$indexFile]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
				$indexFile++;
			}
		}
		else
		{
			$response->programList[$indexProgram]->pathList[0] = new PathList;
			$streamID = $row->channel_id . '_' . $row->program_id . '_' . $row->ID;
			if ($row->isMobile == 0)
				$serverDomain = HTTP_WOWZA + 'livestream/';
			else
				$serverDomain = HTTP_WOWZA + 'livemobile/';
			$response->programList[$indexProgram]->pathList[0]->pathName = $streamID;
			$response->programList[$indexProgram]->pathList[0]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
			$response->programList[$indexProgram]->pathList[0]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
		}
		$response->programList[$indexProgram]->description = $row->description;
		$response->programList[$indexProgram]->vote = $row->vote;
		$response->programList[$indexProgram]->connectCount = $row->connect_count;
		$response->programList[$indexProgram]->latitude = $row->latitude;
		$response->programList[$indexProgram]->longitude = $row->longitude;
		if ($row->image_path != null && $row->image_path != '')
		{
			$response->programList[$indexProgram]->imageUrl = DOMAIN_ROOT. $row->image_path;
		}
		else if ($row->picture_path != null && $row->picture_path != '')
		{
			$response->programList[$indexProgram]->imageUrl = DOMAIN_ROOT . $row->picture_path;
		}
		else if ($row->facebook_id != null && $row->facebook_id != '')
		{
			$response->programList[$indexProgram]->imageUrl = 'https://graph.facebook.com/' . $row->facebook_id . '/picture/?type=large';
		}
		else if ($row->twitter_img != null && $row->twitter_img != '')
		{
			$response->programList[$indexProgram]->imageUrl = $row->twitter_img;
		}

		$indexProgram++;
	}

	return $response;
}

function mb_getProgramAllList( $args)
{
	global $wpdb;

	$response = new GetProgramListResp;

	$response->successFlag = 'TRUE';

	$curTime = date('Y-m-d H:i:s', time());

	$rows = $wpdb->get_results("SELECT program_id, channel_id, (SELECT channel_name FROM wp_channel WHERE channel_id = a.channel_id) as channel_name, title, IFNULL(vod_path, '') vod_path, vote, vote_neg, TIMEDIFF(end_time,start_time) as timeDiff, start_time, end_time, description, connect_count,
				CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
				WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
				ELSE b.twitter_name END user_login,
				CASE WHEN end_time IS NULL THEN 'live' ELSE 'vod' END type, 
				b.user_id ID, a.latitude, a.longitude, a.isMobile, a.duration,
				a.image_path, b.picture_path, b.facebook_id, b.twitter_img
			FROM wp_program a, wp_user_yep b
			WHERE start_time >= DATE_SUB('$curTime', INTERVAL 1 DAY)
			AND (end_time IS NULL OR end_time <= '$curTime' AND a.vod_enable = 1)
			AND a.user_id = b.user_id ORDER BY program_id DESC");

	$indexProgram = 0;
	foreach ( $rows as $row ) {
		$response->programList[$indexProgram] = new ProgramList;
		$response->programList[$indexProgram]->programId = $row->program_id;
		$response->programList[$indexProgram]->channelId = $row->channel_id;
		$response->programList[$indexProgram]->channelName = $row->channel_name;
		$response->programList[$indexProgram]->type = $row->type;
		$response->programList[$indexProgram]->title = $row->title;
        $response->programList[$indexProgram]->timeDiff = $row->timeDiff;
		$response->programList[$indexProgram]->startTime = $row->start_time;
		$response->programList[$indexProgram]->endTime = $row->end_time;
		$response->programList[$indexProgram]->publisherName = $row->user_login;
        $response->programList[$indexProgram]->publisherId = $row->ID;
        $response->programList[$indexProgram]->timeAgo = nicetime($row->start_time);
        $response->programList[$indexProgram]->duration = $row->duration;
		if ($row->type == 'vod')
		{
			$files = explode(",", $row->vod_path);
			$indexFile = 0;
			foreach ( $files as $file ) {
				$response->programList[$indexProgram]->pathList[$indexFile] = new PathList;
				$streamID = $file;
				$serverDomain = HTTP_WOWZA.'vod/mp4:';
				$response->programList[$indexProgram]->pathList[$indexFile]->pathName = $streamID;
				$response->programList[$indexProgram]->pathList[$indexFile]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
				$response->programList[$indexProgram]->pathList[$indexFile]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
				$indexFile++;
			}
		}
		else
		{
			$response->programList[$indexProgram]->pathList[0] = new PathList;
			$streamID = $row->channel_id . '_' . $row->program_id . '_' . $row->ID;
			if ($row->isMobile == 0)
				$serverDomain = HTTP_WOWZA . 'livestream/';
			else
				$serverDomain = HTTP_WOWZA . 'livemobile/';
			$response->programList[$indexProgram]->pathList[0]->pathName = $streamID;
			$response->programList[$indexProgram]->pathList[0]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
			$response->programList[$indexProgram]->pathList[0]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
		}
		$response->programList[$indexProgram]->description = $row->description;
		$response->programList[$indexProgram]->vote = $row->vote;
        $response->programList[$indexProgram]->vote_neg = $row->vote_neg;
		$response->programList[$indexProgram]->connectCount = $row->connect_count;
		$response->programList[$indexProgram]->latitude = $row->latitude;
		$response->programList[$indexProgram]->longitude = $row->longitude;
		
		if ($row->picture_path != null && $row->picture_path != '')
		{
			$response->programList[$indexProgram]->userPictureUrl = DOMAIN_ROOT . $row->picture_path;
		}
		else if ($row->facebook_id != null && $row->facebook_id != '')
		{
			$response->programList[$indexProgram]->userPictureUrl = 'https://graph.facebook.com/' . $row->facebook_id . '/picture/?type=large';
		}
		else if ($row->twitter_img != null && $row->twitter_img != '')
		{
			$response->programList[$indexProgram]->userPictureUrl = $row->twitter_img;
		}
        else
        {
            $response->programList[$indexProgram]->userPictureUrl = DOMAIN_ROOT . 'wp-content/themes/Explorable/images/profile-thumb.png';
        }

        if ($row->image_path != null) $response->programList[$indexProgram]->videoPictureUrl = DOMAIN_ROOT . $row->image_path;
        else $response->programList[$indexProgram]->videoPictureUrl = DOMAIN_ROOT . 'wp-content/themes/Explorable/images/myprofile.png';

        $tags_rows = $wpdb->get_results("SELECT name FROM wp_tags WHERE id IN (SELECT tag_id FROM wp_tags_program WHERE program_id = " . $row->program_id . ")");
        $tags = "";
        foreach ( $tags_rows as $tags_row )
        {
            $tags = $tags . $tags_row->name . '/****|';
        }
        $response->programList[$indexProgram]->tags = $tags;

		$indexProgram++;
	}


	return $response;
}

function nicetime($date)
{
    if(empty($date)) {
        return "No date provided";
    }

    $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths         = array("60","60","24","7","4.35","12","10");

    $now             = time();
    $unix_date         = strtotime($date);

    // check validity of date
    if(empty($unix_date)) {
        return "Bad date";
    }

    // is it future date or past date
    if($now > $unix_date) {
        $difference     = $now - $unix_date;
        $tense         = "ago";

    } else {
        $difference     = $unix_date - $now;
        $tense         = "from now";
    }

    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if($difference != 1) {
        $periods[$j].= "s";
    }

    return "$difference $periods[$j] {$tense}";
}

class GetPublishListResp {
	public $successFlag;
	public $failMessage;
	public $publishList;
}

class PublishList {
	public $programId;
	public $channelId;
	public $title;
	public $startTime;
	public $endTime;
	public $isEnable;
	public $description;
}

function mb_getPublishList( $args)
{
	global $wpdb;
	
	if (is_array($args))
	{
		$userId = $args[0];
	}
	else
	{
		$userId = $args;
	}	
	
	$response = new GetPublishListResp;
	
	$response->successFlag = 'TRUE';
	
	$curTime = date('Y-m-d H:i:s', time());
	
	$rows = $wpdb->get_results("SELECT program_id, channel_id, title, start_time, end_time, description,
									CASE WHEN start_time <= '$curTime' THEN 1 ELSE 0 END is_enable
								FROM wp_program
								WHERE end_time >= '$curTime'
								AND user_id = '$userId'");
	
	$indexPublish = 0;
	foreach ( $rows as $row ) {
		$response->publishList[$indexPublish] = new PublishList;
		$response->publishList[$indexPublish]->programId = $row->program_id;
		$response->publishList[$indexPublish]->channelId = $row->channel_id;
		$response->publishList[$indexPublish]->title = $row->title;
		$response->publishList[$indexPublish]->startTime = $row->start_time;
		$response->publishList[$indexPublish]->endTime = $row->end_time;
		$response->publishList[$indexPublish]->isEnable = $row->is_enable;
		$response->publishList[$indexPublish]->description = $row->description;
		$indexPublish++;
	}
	
	return $response;
}

function mb_generateRandomThumbnail($args)
{
    global $wpdb;

    $programId = $args[0];
    $thumbnail_name = $args[1];

    $rows = $wpdb->get_results("SELECT vod_path FROM wp_program WHERE program_id = " . $programId);
    $video_name = $rows[0]->vod_path;

    shell_exec("bash " . SERVER_BASH_SCRIPTS_PATH. "getRandomThumbnail.sh " . WOWZA_CONTENT_PATH ." $video_name $thumbnail_name " . THUMBNAILS_PATH);

    return 1;
}

function wz_setServerStart( $args)
{
	global $wpdb;
	
	$streamId = $args;
	
	$params = explode("_", $streamId);
	$programId = $params[1];
	
	$curTime = date('Y-m-d H:i:s', time());
	
	$wpdb->query("UPDATE wp_program SET start_time = '$curTime' WHERE program_id = $programId");
	
    return 1;
}

function wz_setServerEnd( $args)
{
	global $wpdb;
	
	$streamId = $args[0];
	$fileName = $args[1];
	
	$params = explode("_", $streamId);
	$programId = $params[1];
	
	/*
	$rows = $wpdb->get_results("SELECT vod_enable, vod_path FROM wp_program WHERE program_id = " . $programId);
	
	if (count($rows) < 1) 
	{
		return 2;
	}
	
	$row = $rows[0];

	$vodEnable = $row->vod_enable;
	$vodPath = $row->vod_path;
	
	if ($vodEnable == 1)
	{
		$vodTemp = explode(",", $vodPath);
		$vodCount = count($vodTemp);
		$pathLen = strlen($vodPath);
		
		$strInsert = "_" . ($vodCount -1);
		
		$newVodPath = substr_replace($vodPath, $strInsert, $pathLen - 4, 0);
		$vodPath = $newVodPath . ',' . $fileName;
	}
	else
	{
		$vodPath = $fileName;
	}
	*/
	
	$curTime = date('Y-m-d H:i:s', time());

    $duration = shell_exec("ffmpeg -i " . WOWZA_CONTENT_PATH . $fileName . " 2>&1 | grep 'Duration' | cut -c 16-20 | head -1");
	
	$wpdb->query("UPDATE wp_program SET end_time = '$curTime', vod_enable = 1, vod_path = '$fileName', duration = '$duration' WHERE program_id = $programId");

    copy(WOWZA_CONTENT_PATH . $fileName, SERVER_CONTENT_PATH . $fileName);

	return 1;
}

function wz_setStreamStart( $args)
{
	global $wpdb;
	
	$streamId = $args;
	$params = explode("_", $streamId);
	$channelId = $params[0];
	$programId = $params[1];
	
	$rows = $wpdb->get_results("SELECT * FROM wp_program WHERE program_id = " . $programId);
	
	if (count($rows) < 1) 
	{
		return 2;
	}
	
	$row = $rows[0];

	$connectCount = $row->connect_count;
	$connectCount++;
	
	$wpdb->query( $wpdb->prepare(
			"UPDATE wp_program SET connect_count = %d WHERE program_id = %d",
			$connectCount,
			$programId
	) );

	return 1;
}

function wz_setStreamEnd( $args)
{
	global $wpdb;

	$streamId = $args;
	$params = explode("_", $streamId);
	$channelId = $params[0];
	$programId = $params[1];
	
	$rows = $wpdb->get_results("SELECT * FROM wp_program WHERE program_id = " . $programId);

	if (count($rows) < 1)
	{
		return 2;
	}

	$row = $rows[0];

	$connectCount = $row->connect_count;
	if ($connectCount > 0)
		$connectCount--;

	$wpdb->query( $wpdb->prepare(
			"UPDATE wp_program SET connect_count = %d WHERE program_id = %d",
			$connectCount,
			$programId
	) );

	return 1;
}

class CreateProgramResp {
	public $successFlag;
	public $failMessage;
	public $programId;
}

function mb_createProgram( $args)
{
	global $wpdb;
	
	$response = new CreateProgramResp;

	if (count($args) != 6 && count($args) != 7)
	{
		$response->successFlag = 'FALSE';
		$response->failMessage = 'Unknown errors occurred.';
		return $response;
	}

	$userId = $args[0];
	$title = $args[1];
	$channelId = $args[2];
	$description = $args[3];
	$latitude = $args[4];
	$longitude = $args[5];
	if (count($args) == 6)
		$imgData = "";
	else
		$imgData = $args[6];
	
	$newFileUrl = "";
	if ($imgData != "")
	{
		$realData = base64_decode($imgData);
		$RandNumber = rand(0, 9999999999);
		$uploaded_date = date("Y-m-d-H-i-s");
		$NewFileName = $uploaded_date . "_" . $RandNumber . ".jpg";
		$uploads = wp_upload_dir($time);
		
		if (!@file_exists($uploads['path'])) {
			if (!mkdir($uploads['path']))
			{
				$response->successFlag = 'FALSE';
				$response->failMessage = 'Unknown errors occurred.';
				return $response;
			}
		}
		
		$newFilePath = $uploads['path'] . "/$NewFileName";
		$newFileUrl = str_replace(DOMAIN_ROOT, "", $uploads['url']) . "/$NewFileName";
		
		$fp = fopen($newFilePath, 'w');
		fwrite($fp, $realData);
		fclose($fp);
	}
	
	$wpdb->query("INSERT INTO wp_program(channel_id, user_id, title, image_path, latitude, longitude, description, vod_enable, vote, isMobile)
				  VALUES($channelId, $userId, '$title', '$newFileUrl', $latitude, $longitude, '$description', 0, 0, 1)");
	
	$rows = $wpdb->get_results("SELECT MAX(program_id) program_id FROM wp_program");
		
	$row = $rows[0];
	
	$response->successFlag = 'TRUE';
	$response->programId = $row->program_id;
	
	return $response;
}

class UpdateImageResp {
	public $successFlag;
	public $failMessage;
}

function mb_updateImage( $args)
{
	global $wpdb;

	$response = new UpdateImageResp;

	if (count($args) != 2 || $args[0] == "" || $args[1] == "")
	{
		$response->successFlag = 'FALSE';
		$response->failMessage = 'Unknown errors occurred.';
		return $response;
	}

	$programId = $args[0];
	$imgData = $args[1];

	$realData = base64_decode($imgData);
	$RandNumber = rand(0, 9999999999);
	$uploaded_date = date("Y-m-d-H-i-s");
	$NewFileName = $uploaded_date . "_" . $RandNumber . ".jpg";
	$uploads = wp_upload_dir($time);
	
	if (!@file_exists($uploads['path'])) {
		if (!mkdir($uploads['path']))
		{
			$response->successFlag = 'FALSE';
			$response->failMessage = 'Unknown errors occurred.';
			return $response;
		}
	}
	
	$newFilePath = $uploads['path'] . "/$NewFileName";
	$newFileUrl = str_replace(DOMAIN_ROOT, "", $uploads['url']) . "/$NewFileName";

	$fp = fopen($newFilePath, 'w');
	fwrite($fp, $realData);
	fclose($fp);
	

	$wpdb->query("UPDATE wp_program SET image_path = '$newFileUrl' WHERE program_id = $programId");

	$response->successFlag = 'TRUE';

	return $response;
}

class EndProgramResp {
	public $successFlag;
	public $failMessage;
}

function mb_endProgram( $args)
{
	global $wpdb;

	$response = new EndProgramResp;

	if (is_array($args))
	{
		$programId = $args[0];
	}
	else
	{
		$programId = $args;
	}

	$curTime = date('Y-m-d H:i:s', time());
	$wpdb->query("UPDATE wp_program SET end_time = '$curTime', vod_enable = 1 WHERE program_id = $programId");

	$response->successFlag = 'TRUE';

	return $response;
}

class GetProgramInfoResp {
	public $successFlag;
	public $failMessage;
	public $type;
	public $enable;
	public $pathList;
    public $timeAgo;
    public $duration;
    public $title;
    public $description;
    public $isFollow;
    public $vote;
    public $vote_neg;
    public $publisherName;
    public $publisherId;
    public $connectCount;
    public $userPictureUrl;
    public $videoPictureUrl;
}

function mb_getProgramInfo( $args)
{
	global $wpdb;

	$response = new GetProgramInfoResp;
	
	if (is_array($args))
	{
		$programId = $args[0];
        $user_id = $args[1];
	}
	else
	{
		$programId = $args;
	}
	
	$response->successFlag = 'TRUE';
	
	$curTime = date('Y-m-d H:i:s', time());

    $rows = $wpdb->get_results("SELECT program_id, title, IFNULL(vod_path, '') vod_path, vote, vote_neg, start_time, description, connect_count,
				CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
				WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
				ELSE b.twitter_name END user_login,
				CASE WHEN end_time IS NULL THEN 'live' ELSE 'vod' END type,
				b.user_id ID, a.isMobile, a.duration,
				a.image_path, b.picture_path, b.facebook_id, b.twitter_img
			FROM wp_program a, wp_user_yep b
			WHERE program_id = '$programId' AND a.user_id = b.user_id");
	
	$row = $rows[0];

    if (is_null($row))
    {
        $response->enable = 'FALSE';
        return $response;
    }
	
	$response->type = $row->type;
	if ($row->type == 'vod')
	{
		if (empty($row->vod_path) == false)
		{
			$response->enable = 'TRUE';
			$response->pathList[0] = new PathList;
			$streamID = $row->vod_path;
			$serverDomain = HTTP_WOWZA . 'vod/mp4:';
			$response->pathList[0]->pathName = $streamID;
			$response->pathList[0]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
			$response->pathList[0]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';
		}
		else
		{
			$response->enable = 'FALSE';
		}
	}
	else
	{
		$response->enable = 'TRUE';
		$response->pathList[0] = new PathList;
		$streamID = $row->channel_id . '_' . $row->program_id . '_' . $row->ID;
		if ($row->isMobile == 0)
			$serverDomain = HTTP_WOWZA . 'livestream/';
		else
			$serverDomain = HTTP_WOWZA . 'livemobile/';
		$response->pathList[0]->pathName = $streamID;
		$response->pathList[0]->pathUrlHLS = $serverDomain . $streamID . '/playlist.m3u8';
		$response->pathList[0]->pathUrlHDS = $serverDomain . $streamID . '/manifest.f4m';

	}

    $response->timeAgo = nicetime($row->start_time);
    $response->duration = $row->duration;
    $response->title = $row->title;
    $response->description = $row->description;
    $response->publisherName = $row->user_login;
    $response->publisherId = $row->ID;
    $response->vote = $row->vote;
    $response->vote_neg = $row->vote_neg;
    $response->connectCount = $row->connect_count;

    if ($row->picture_path != null && $row->picture_path != '')
    {
        $response->userPictureUrl = DOMAIN_ROOT . $row->picture_path;
    }
    else if ($row->facebook_id != null && $row->facebook_id != '')
    {
        $response->userPictureUrl = 'https://graph.facebook.com/' . $row->facebook_id . '/picture/?type=large';
    }
    else if ($row->twitter_img != null && $row->twitter_img != '')
    {
        $response->userPictureUrl = $row->twitter_img;
    }
    else
    {
        $response->userPictureUrl = DOMAIN_ROOT . 'wp-content/themes/Explorable/images/profile-thumb.png';
    }

    if ($row->image_path != null) $response->videoPictureUrl = DOMAIN_ROOT . $row->image_path;
    else $response->videoPictureUrl = DOMAIN_ROOT . 'wp-content/themes/Explorable/images/myprofile.png';

    if (!is_null($user_id))
    {
        $rows = $wpdb->get_results("select count(*) as isFollow from wp_user_pans where user_id = " . $row->ID . " and pan_id = " . $user_id);
        $row = $rows[0];
        $response->isFollow = $row->isFollow;
    }
    else
        $response->isFollow = false;
    return $response;
}

class DeleteProgramResp {
    public $successFlag;
    public $failMessage;
}

function mb_deleteProgram( $args)
{
    global $wpdb;

    $response = new DeleteProgramResp;

    if (is_array($args))
    {
        $programId = $args[0];
    }
    else
    {
        $programId = $args;
    }

    $wpdb->query("DELETE FROM wp_program WHERE program_id = '$programId'");

    $response->successFlag = 'TRUE';

    return $response;
}

class CutVideoResp {
    public $successFlag;
    public $failMessage;
}

function mb_cutVideo( $args)
{
    global $wpdb;

    $response = new CutVideoResp;

    $vod_path = $args[0];
    $start_time = $args[1];
    $end_time = $args[2];
    $new_duration = $args[3];
    $programId = $args[4];

//    echo "Cutting video...";
//    echo shell_exec("ffmpeg -i ". WOWZA_CONTENT_PATH . $vod_path .".mp4  -ss '$start_time' -t '$new_duration' -async 1 -strict -2 ". WOWZA_CONTENT_PATH . $vod_path ."_cut.mp4 &");
//    echo "Done.\n";
//
//    echo "Renaming video...";
//    echo shell_exec("mv ". WOWZA_CONTENT_PATH . $vod_path ."_cut.mp4 ". WOWZA_CONTENT_PATH . $vod_path .".mp4 -f");
//    echo "Done.\n";
//
//    echo "Copying video...";
//    echo shell_exec("cp ". WOWZA_CONTENT_PATH . $vod_path .".mp4 ". SERVER_CONTENT_PATH . $vod_path .".mp4 -f");
//    echo "Done.\n";

    shell_exec("ffmpeg -i ". WOWZA_CONTENT_PATH . $vod_path .".mp4  -ss '$start_time' -t '$new_duration' -async 1 -strict -2 ". WOWZA_CONTENT_PATH . $vod_path ."_cut.mp4 &");
    shell_exec("mv ". WOWZA_CONTENT_PATH . $vod_path ."_cut.mp4 ". WOWZA_CONTENT_PATH . $vod_path .".mp4 -f");
    shell_exec("cp ". WOWZA_CONTENT_PATH . $vod_path .".mp4 ". SERVER_CONTENT_PATH . $vod_path .".mp4 -f");

    $duration = substr($new_duration, 3, strlen($new_duration));

    $wpdb->query("UPDATE wp_program SET duration = '$duration' WHERE program_id = '$programId'");

    $response->successFlag = 'TRUE';

    return $response;
}


class CheckNotificationsResp {
    public $successFlag;
    public $failMessage;
    public $notificationsCount;
}

function mb_checkNotifications( $args)
{
    global $wpdb;

    $response = new CheckNotificationsResp;

    $userId = $args;

    $rows = $wpdb->get_results("SELECT COUNT(*) as notificationCount FROM wp_notifications WHERE user_receiver_id = '$userId' AND is_new = 1");

    $row = $rows[0];
    $count = $row->notificationCount;
    if ($count > 0)
    {
        $response->successFlag = 'TRUE';
        $response->notificationsCount = $count;
    }
    else
    {
        $response->successFlag = 'FALSE';
        $response->notificationsCount = 0;
    }


    return $response;
}


class GetNotificationsResp {
    public $successFlag;
    public $failMessage;
    public $notificationList;
}

class NotificationList {
    public $id;
    public $type;
    public $user_sender_id;
    public $program_id;
    public $action;
    public $is_new;
    public $picture_path;
}

function mb_getNotifications( $args)
{
    global $wpdb;

    $response = new GetNotificationsResp;

    $userId = $args;

    $rows = $wpdb->get_results("SELECT * FROM wp_notifications WHERE user_receiver_id = '$userId'");

    $index = 0;
    foreach ( $rows as $row ) {
        $response->notificationList[$index] = new NotificationList;
        $response->notificationList[$index]->id = $row->id;
        $response->notificationList[$index]->type = $row->type;
        $response->notificationList[$index]->user_sender_id = $row->user_sender_id;
        $response->notificationList[$index]->program_id = $row->program_id;
        $response->notificationList[$index]->action = $row->action;
        $response->notificationList[$index]->is_new = $row->is_new;

        if ((strpos($row->picture_path, 'http://graph.facebook.com/') !== false)||(strpos($row->picture_path, 'http://abs.twimg.com/') !== false))
            $response->notificationList[$index]->picture_path = $row->picture_path;
        else
            $response->notificationList[$index]->picture_path = DOMAIN_ROOT . $row->picture_path;

        $index++;
    }

    $response->successFlag = 'TRUE';
    return $response;
}

function mb_setNotificationsIsNotNew($args)
{
    global $wpdb;

    $notificationsIDs = $args;

    $IDs = explode("_", $notificationsIDs);

    $or_clause = "";
    foreach($IDs as $id)
    {
        $or_clause = $or_clause . " id=" . $id . " OR ";
    }
    $or_clause = $or_clause . "0";

    $result = $wpdb->query("UPDATE wp_notifications SET is_new = 0 WHERE " . $or_clause);
    return $result;
}

function mb_deleteNotification($args)
{
    global $wpdb;

    $notificationsID = $args;

    $result = $wpdb->query("DELETE FROM wp_notifications WHERE id=" . $notificationsID);
    return $result;
}


class GetUserBroadcastsResp {
    public $successFlag;
    public $failMessage;
    public $broadcastsList;
}

class BroadcastsList {
    public $programId;
    public $image_path;
    public $duration;
    public $title;
    public $description;
    public $timeAgo;
    public $views;
}

function mb_getUserBroadcasts( $args)
{
    global $wpdb;

    $response = new GetUserBroadcastsResp;

    $user_id = $args;

    $rows = $wpdb->get_results("SELECT * FROM wp_program WHERE user_id = '$user_id' AND start_time IS NOT NULL ORDER BY program_id DESC");

    $index = 0;
    foreach ( $rows as $row ) {
        $response->broadcastsList[$index] = new BroadcastsList;
        $response->broadcastsList[$index]->programId = $row->program_id;
        $response->broadcastsList[$index]->image_path = DOMAIN_ROOT . $row->image_path;
        $response->broadcastsList[$index]->duration = $row->duration;
        $response->broadcastsList[$index]->title = $row->title;
        $response->broadcastsList[$index]->description = $row->description;
        $response->broadcastsList[$index]->timeAgo = nicetime($row->start_time);
        $response->broadcastsList[$index]->views = $row->connect_count;

        $index++;
    }

    $response->successFlag = 'TRUE';
    return $response;
}



class GetUserFollowingResp {
    public $successFlag;
    public $failMessage;
    public $followingList;
}

class FollowingList {
    public $UID;
    public $userpic;
    public $userName;
}

function mb_getUserFollowing( $args)
{
    global $wpdb;

    $response = new GetUserFollowingResp;

    $user_id = $args;

    $rows = $wpdb->get_results("SELECT a.user_id as UID, a.picture_path as userpic, b.user_nicename as userName FROM wp_user_yep a, wp_users b WHERE a.user_id IN (SELECT pan_id FROM wp_user_pans WHERE user_id = '$user_id') AND a.wp_user_id = b.ID");

    $index = 0;
    foreach ( $rows as $row ) {
        $response->followingList[$index] = new FollowingList;
        $response->followingList[$index]->UID = $row->UID;
        $response->followingList[$index]->userpic = DOMAIN_ROOT . $row->userpic;
        $response->followingList[$index]->userName = $row->userName;

        $index++;
    }

    $response->successFlag = 'TRUE';
    return $response;
}



class GetUserFollowersResp {
    public $successFlag;
    public $failMessage;
    public $followersList;
}

class FollowersList {
    public $UID;
    public $userpic;
    public $userName;
}

function mb_getUserFollowers( $args)
{
    global $wpdb;

    $response = new GetUserFollowersResp;

    $user_id = $args;

    $rows = $wpdb->get_results("SELECT a.user_id as UID, a.picture_path as userpic, b.user_nicename as userName FROM wp_user_yep a, wp_users b WHERE a.user_id IN (SELECT user_id FROM wp_user_pans WHERE pan_id = '$user_id') AND a.wp_user_id = b.ID");

    $index = 0;
    foreach ( $rows as $row ) {
        $response->followersList[$index] = new FollowersList;
        $response->followersList[$index]->UID = $row->UID;
        $response->followersList[$index]->userpic = DOMAIN_ROOT . $row->userpic;
        $response->followersList[$index]->userName = $row->userName;

        $index++;
    }

    $response->successFlag = 'TRUE';
    return $response;
}


class UnfollowUserResp {
    public $successFlag;
    public $failMessage;
}

function mb_unfollowUser( $args)
{
    global $wpdb;

    $response = new UnfollowUserResp;

    $user_id = $args[0];
    $pan_id = $args[1];

    $result = $wpdb->query("DELETE FROM wp_user_pans WHERE user_id = '$user_id' AND pan_id = '$pan_id'");

    if ($result)
        $response->successFlag = 'TRUE';
    else
        $response->successFlag = 'FALSE';
    return $response;
}


class UserInfoResp {
    public $successFlag;
    public $failMessage;
    public $UID;
    public $userpic;
    public $nicename;
}

function mb_getUserInfo( $args)
{
    global $wpdb;

    $response = new UserInfoResp;

    $user_id = $args;

    //the query could be extended later
    $rows = $wpdb->get_results("SELECT uy.user_id, uy.picture_path, uw.user_nicename FROM wp_user_yep uy INNER JOIN wp_users uw ON uw.ID = uy.wp_user_id WHERE user_id = '$user_id'");

    $row = $rows[0];

    if ($row)
    {
        $response->successFlag = 'TRUE';
        $response->UID = $row->user_id;
        $response->userpic = DOMAIN_ROOT . $row->picture_path;
        $response->nicename = $row->user_nicename;
    }
    else
        $response->successFlag = 'FALSE';
    return $response;
}


class TagsPopularityResp {
    public $successFlag;
    public $failMessage;
    public $tagsPopularityList;
}

class TagsPopularityList {
    public $tag;
    public $popularity;
}

function mb_getTagsPopularity( $args)
{
    global $wpdb;

    $response = new TagsPopularityResp;

    $rows = $wpdb->get_results("SELECT count(*) as popularity, t.name FROM wp_tags_program p JOIN wp_tags t ON p.tag_id = t.id GROUP BY tag_id ORDER BY popularity DESC");

    $index = 0;
    foreach ( $rows as $row ) {
        $response->tagsPopularityList[$index] = new TagsPopularityList;
        $response->tagsPopularityList[$index]->tag = $row->name;
        $response->tagsPopularityList[$index]->popularity = $row->popularity;

        $index++;
    }

    $response->successFlag = 'TRUE';
    return $response;
}

?>
