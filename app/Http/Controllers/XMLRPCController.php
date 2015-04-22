<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
const DB_HOST = "yeplive-dev.czc6detrzhw4.us-west-2.rds.amazonaws.com:3306";
const DOMAIN_ROOT = "example.com";
const DB_USER = "root";
const DB_PASSWORD = "rootroot";
const DB_NAME = "test";
const WP_DEBUG = false;
global $wpdb;

class TagsPopularityResp {
    public $successFlag;
    public $failMessage;
    public $tagsPopularityList;
}

class TagsPopularityList {
    public $tag;
    public $popularity;
}

class UserInfoResp {
    public $successFlag;
    public $failMessage;
    public $UID;
    public $userpic;
    public $nicename;
}

class UnfollowUserResp {
    public $successFlag;
    public $failMessage;
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

class CutVideoResp {
    public $successFlag;
    public $failMessage;
}

class DeleteProgramResp {
    public $successFlag;
    public $failMessage;
}


class CheckNotificationsResp {
    public $successFlag;
    public $failMessage;
    public $notificationsCount;
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

class EndProgramResp {
	public $successFlag;
	public $failMessage;
}

class UpdateImageResp {
	public $successFlag;
	public $failMessage;
}

class CreateProgramResp {
	public $successFlag;
	public $failMessage;
	public $programId;
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



class GetServerInfoResp {
	public $successFlag;
	public $failMessage;
	public $serverIp;
	public $serverPort;
}
class LoginResp {
	public $successFlag;
	public $failMessage;
	public $userName;
	public $UID;
	public $userGroup;
    public $userpic;
	 
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
class XMLRPCController extends Controller {

	public function __construct()
	{
		//USES mysql_* deprecated functions
		//error_reporting = E_ALL ^ E_DEPRECATED to stop messages
		error_reporting(E_ALL ^ E_DEPRECATED);
		global $wpdb;
		$wpdb  = new wpdb(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
	}

	public function index(Request $request)
	{
		
		$method = $request->input('method');
		$action = $this->parse_methods($method);
		$args = $this->parse_args($request);
		return $this->{$action}($args);
	}

	private function parse_args($request)
	{
		return $request->input('args');
	}

private function parse_methods($method)
	{
		$methods = [];
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


		if(array_key_exists($method, $methods)){
			return $methods[$method];
		} else {
			return "invalid_method";
		}
	}

	private function invalid_method()
	{
		return "invalid RPC method";
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
	return $rows;
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


	

}
/**
 * WordPress DB Class
 *
 * Original code from {@link http://php.justinvincent.com Justin Vincent (justin@visunet.ie)}
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */

/**
 * @since 0.71
 */
define( 'EZSQL_VERSION', 'WP1.25' );

/**
 * @since 0.71
 */
define( 'OBJECT', 'OBJECT', true );

/**
 * @since 2.5.0
 */
define( 'OBJECT_K', 'OBJECT_K' );

/**
 * @since 0.71
 */
define( 'ARRAY_A', 'ARRAY_A' );

/**
 * @since 0.71
 */
define( 'ARRAY_N', 'ARRAY_N' );

/**
 * WordPress Database Access Abstraction Object
 *
 * It is possible to replace this class with your own
 * by setting the $wpdb global variable in wp-content/db.php
 * file to your class. The wpdb class will still be included,
 * so you can extend it or simply use your own.
 *
 * @link http://codex.wordpress.org/Function_Reference/wpdb_Class
 *
 * @package WordPress
 * @subpackage Database
 * @since 0.71
 */
class wpdb {

	/**
	 * Whether to show SQL/DB errors
	 *
	 * @since 0.71
	 * @access private
	 * @var bool
	 */
	var $show_errors = false;

	/**
	 * Whether to suppress errors during the DB bootstrapping.
	 *
	 * @access private
	 * @since 2.5.0
	 * @var bool
	 */
	var $suppress_errors = false;

	/**
	 * The last error during query.
	 *
	 * @since 2.5.0
	 * @var string
	 */
	var $last_error = '';

	/**
	 * Amount of queries made
	 *
	 * @since 1.2.0
	 * @access private
	 * @var int
	 */
	var $num_queries = 0;

	/**
	 * Count of rows returned by previous query
	 *
	 * @since 0.71
	 * @access private
	 * @var int
	 */
	var $num_rows = 0;

	/**
	 * Count of affected rows by previous query
	 *
	 * @since 0.71
	 * @access private
	 * @var int
	 */
	var $rows_affected = 0;

	/**
	 * The ID generated for an AUTO_INCREMENT column by the previous query (usually INSERT).
	 *
	 * @since 0.71
	 * @access public
	 * @var int
	 */
	var $insert_id = 0;

	/**
	 * Last query made
	 *
	 * @since 0.71
	 * @access private
	 * @var array
	 */
	var $last_query;

	/**
	 * Results of the last query made
	 *
	 * @since 0.71
	 * @access private
	 * @var array|null
	 */
	var $last_result;

	/**
	 * MySQL result, which is either a resource or boolean.
	 *
	 * @since 0.71
	 * @access protected
	 * @var mixed
	 */
	protected $result;

	/**
	 * Saved info on the table column
	 *
	 * @since 0.71
	 * @access protected
	 * @var array
	 */
	protected $col_info;

	/**
	 * Saved queries that were executed
	 *
	 * @since 1.5.0
	 * @access private
	 * @var array
	 */
	var $queries;

	/**
	 * WordPress table prefix
	 *
	 * You can set this to have multiple WordPress installations
	 * in a single database. The second reason is for possible
	 * security precautions.
	 *
	 * @since 2.5.0
	 * @access private
	 * @var string
	 */
	var $prefix = '';

	/**
	 * Whether the database queries are ready to start executing.
	 *
	 * @since 2.3.2
	 * @access private
	 * @var bool
	 */
	var $ready = false;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access public
	 * @var int
	 */
	var $blogid = 0;

	/**
	 * {@internal Missing Description}}
	 *
	 * @since 3.0.0
	 * @access public
	 * @var int
	 */
	var $siteid = 0;

	/**
	 * List of WordPress per-blog tables
	 *
	 * @since 2.5.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $tables = array( 'posts', 'comments', 'links', 'options', 'postmeta',
		'terms', 'term_taxonomy', 'term_relationships', 'commentmeta' );

	/**
	 * List of deprecated WordPress tables
	 *
	 * categories, post2cat, and link2cat were deprecated in 2.3.0, db version 5539
	 *
	 * @since 2.9.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $old_tables = array( 'categories', 'post2cat', 'link2cat' );

	/**
	 * List of WordPress global tables
	 *
	 * @since 3.0.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $global_tables = array( 'users', 'usermeta' );

	/**
	 * List of Multisite global tables
	 *
	 * @since 3.0.0
	 * @access private
	 * @see wpdb::tables()
	 * @var array
	 */
	var $ms_global_tables = array( 'blogs', 'signups', 'site', 'sitemeta',
		'sitecategories', 'registration_log', 'blog_versions' );

	/**
	 * WordPress Comments table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $comments;

	/**
	 * WordPress Comment Metadata table
	 *
	 * @since 2.9.0
	 * @access public
	 * @var string
	 */
	var $commentmeta;

	/**
	 * WordPress Links table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $links;

	/**
	 * WordPress Options table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $options;

	/**
	 * WordPress Post Metadata table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $postmeta;

	/**
	 * WordPress Posts table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $posts;

	/**
	 * WordPress Terms table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	var $terms;

	/**
	 * WordPress Term Relationships table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	var $term_relationships;

	/**
	 * WordPress Term Taxonomy table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	var $term_taxonomy;

	/*
	 * Global and Multisite tables
	 */

	/**
	 * WordPress User Metadata table
	 *
	 * @since 2.3.0
	 * @access public
	 * @var string
	 */
	var $usermeta;

	/**
	 * WordPress Users table
	 *
	 * @since 1.5.0
	 * @access public
	 * @var string
	 */
	var $users;

	/**
	 * Multisite Blogs table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $blogs;

	/**
	 * Multisite Blog Versions table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $blog_versions;

	/**
	 * Multisite Registration Log table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $registration_log;

	/**
	 * Multisite Signups table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $signups;

	/**
	 * Multisite Sites table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $site;

	/**
	 * Multisite Sitewide Terms table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $sitecategories;

	/**
	 * Multisite Site Metadata table
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $sitemeta;

	/**
	 * Format specifiers for DB columns. Columns not listed here default to %s. Initialized during WP load.
	 *
	 * Keys are column names, values are format types: 'ID' => '%d'
	 *
	 * @since 2.8.0
	 * @see wpdb::prepare()
	 * @see wpdb::insert()
	 * @see wpdb::update()
	 * @see wpdb::delete()
	 * @see wp_set_wpdb_vars()
	 * @access public
	 * @var array
	 */
	var $field_types = array();

	/**
	 * Database table columns charset
	 *
	 * @since 2.2.0
	 * @access public
	 * @var string
	 */
	var $charset;

	/**
	 * Database table columns collate
	 *
	 * @since 2.2.0
	 * @access public
	 * @var string
	 */
	var $collate;

	/**
	 * Whether to use mysql_real_escape_string
	 *
	 * @since 2.8.0
	 * @access public
	 * @var bool
	 */
	var $real_escape = false;

	/**
	 * Database Username
	 *
	 * @since 2.9.0
	 * @access protected
	 * @var string
	 */
	protected $dbuser;

	/**
	 * Database Password
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbpassword;

	/**
	 * Database Name
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbhost;

	/**
	 * Database Handle
	 *
	 * @since 0.71
	 * @access protected
	 * @var string
	 */
	protected $dbh;

	/**
	 * A textual description of the last query/get_row/get_var call
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	var $func_call;

	/**
	 * Whether MySQL is used as the database engine.
	 *
	 * Set in WPDB::db_connect() to true, by default. This is used when checking
	 * against the required MySQL version for WordPress. Normally, a replacement
	 * database drop-in (db.php) will skip these checks, but setting this to true
	 * will force the checks to occur.
	 *
	 * @since 3.3.0
	 * @access public
	 * @var bool
	 */
	public $is_mysql = null;

	/**
	 * Connects to the database server and selects a database
	 *
	 * PHP5 style constructor for compatibility with PHP5. Does
	 * the actual setting up of the class properties and connection
	 * to the database.
	 *
	 * @link http://core.trac.wordpress.org/ticket/3354
	 * @since 2.0.8
	 *
	 * @param string $dbuser MySQL database user
	 * @param string $dbpassword MySQL database password
	 * @param string $dbname MySQL database name
	 * @param string $dbhost MySQL database host
	 */
	function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		register_shutdown_function( array( $this, '__destruct' ) );

		if ( WP_DEBUG )
			$this->show_errors();

		$this->init_charset();

		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;

		$this->db_connect();
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @see wpdb::__construct()
	 * @since 2.0.8
	 * @return bool true
	 */
	function __destruct() {
		return true;
	}

	/**
	 * PHP5 style magic getter, used to lazy-load expensive data.
	 *
	 * @since 3.5.0
	 *
	 * @param string $name The private member to get, and optionally process
	 * @return mixed The private member
	 */
	function __get( $name ) {
		if ( 'col_info' == $name )
			$this->load_col_info();

		return $this->$name;
	}

	/**
	 * Magic function, for backwards compatibility
	 *
	 * @since 3.5.0
	 *
	 * @param string $name  The private member to set
	 * @param mixed  $value The value to set
	 */
	function __set( $name, $value ) {
		$this->$name = $value;
	}

	/**
	 * Magic function, for backwards compatibility
	 *
	 * @since 3.5.0
	 *
	 * @param string $name  The private member to check
	 *
	 * @return bool If the member is set or not
	 */
	function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Magic function, for backwards compatibility
	 *
	 * @since 3.5.0
	 *
	 * @param string $name  The private member to unset
	 */
	function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Set $this->charset and $this->collate
	 *
	 * @since 3.1.0
	 */
	function init_charset() {
		if ( function_exists('is_multisite') && is_multisite() ) {
			$this->charset = 'utf8';
			if ( defined( 'DB_COLLATE' ) && DB_COLLATE )
				$this->collate = DB_COLLATE;
			else
				$this->collate = 'utf8_general_ci';
		} elseif ( defined( 'DB_COLLATE' ) ) {
			$this->collate = DB_COLLATE;
		}

		if ( defined( 'DB_CHARSET' ) )
			$this->charset = DB_CHARSET;
	}

	/**
	 * Sets the connection's character set.
	 *
	 * @since 3.1.0
	 *
	 * @param resource $dbh     The resource given by mysql_connect
	 * @param string   $charset The character set (optional)
	 * @param string   $collate The collation (optional)
	 */
	function set_charset($dbh, $charset = null, $collate = null) {
		if ( !isset($charset) )
			$charset = $this->charset;
		if ( !isset($collate) )
			$collate = $this->collate;
		if ( $this->has_cap( 'collation', $dbh ) && !empty( $charset ) ) {
			if ( function_exists( 'mysql_set_charset' ) && $this->has_cap( 'set_charset', $dbh ) ) {
				mysql_set_charset( $charset, $dbh );
				$this->real_escape = true;
			} else {
				$query = $this->prepare( 'SET NAMES %s', $charset );
				if ( ! empty( $collate ) )
					$query .= $this->prepare( ' COLLATE %s', $collate );
				mysql_query( $query, $dbh );
			}
		}
	}

	/**
	 * Sets the table prefix for the WordPress tables.
	 *
	 * @since 2.5.0
	 *
	 * @param string $prefix Alphanumeric name for the new prefix.
	 * @param bool $set_table_names Optional. Whether the table names, e.g. wpdb::$posts, should be updated or not.
	 * @return string|WP_Error Old prefix or WP_Error on error
	 */
	function set_prefix( $prefix, $set_table_names = true ) {

		if ( preg_match( '|[^a-z0-9_]|i', $prefix ) )
			return new WP_Error('invalid_db_prefix', 'Invalid database prefix' );

		$old_prefix = is_multisite() ? '' : $prefix;

		if ( isset( $this->base_prefix ) )
			$old_prefix = $this->base_prefix;

		$this->base_prefix = $prefix;

		if ( $set_table_names ) {
			foreach ( $this->tables( 'global' ) as $table => $prefixed_table )
				$this->$table = $prefixed_table;

			if ( is_multisite() && empty( $this->blogid ) )
				return $old_prefix;

			$this->prefix = $this->get_blog_prefix();

			foreach ( $this->tables( 'blog' ) as $table => $prefixed_table )
				$this->$table = $prefixed_table;

			foreach ( $this->tables( 'old' ) as $table => $prefixed_table )
				$this->$table = $prefixed_table;
		}
		return $old_prefix;
	}

	/**
	 * Sets blog id.
	 *
	 * @since 3.0.0
	 * @access public
	 * @param int $blog_id
	 * @param int $site_id Optional.
	 * @return string previous blog id
	 */
	function set_blog_id( $blog_id, $site_id = 0 ) {
		if ( ! empty( $site_id ) )
			$this->siteid = $site_id;

		$old_blog_id  = $this->blogid;
		$this->blogid = $blog_id;

		$this->prefix = $this->get_blog_prefix();

		foreach ( $this->tables( 'blog' ) as $table => $prefixed_table )
			$this->$table = $prefixed_table;

		foreach ( $this->tables( 'old' ) as $table => $prefixed_table )
			$this->$table = $prefixed_table;

		return $old_blog_id;
	}

	/**
	 * Gets blog prefix.
	 *
	 * @uses is_multisite()
	 * @since 3.0.0
	 * @param int $blog_id Optional.
	 * @return string Blog prefix.
	 */
	function get_blog_prefix( $blog_id = null ) {
		if ( is_multisite() ) {
			if ( null === $blog_id )
				$blog_id = $this->blogid;
			$blog_id = (int) $blog_id;
			if ( defined( 'MULTISITE' ) && ( 0 == $blog_id || 1 == $blog_id ) )
				return $this->base_prefix;
			else
				return $this->base_prefix . $blog_id . '_';
		} else {
			return $this->base_prefix;
		}
	}

	/**
	 * Returns an array of WordPress tables.
	 *
	 * Also allows for the CUSTOM_USER_TABLE and CUSTOM_USER_META_TABLE to
	 * override the WordPress users and usermeta tables that would otherwise
	 * be determined by the prefix.
	 *
	 * The scope argument can take one of the following:
	 *
	 * 'all' - returns 'all' and 'global' tables. No old tables are returned.
	 * 'blog' - returns the blog-level tables for the queried blog.
	 * 'global' - returns the global tables for the installation, returning multisite tables only if running multisite.
	 * 'ms_global' - returns the multisite global tables, regardless if current installation is multisite.
	 * 'old' - returns tables which are deprecated.
	 *
	 * @since 3.0.0
	 * @uses wpdb::$tables
	 * @uses wpdb::$old_tables
	 * @uses wpdb::$global_tables
	 * @uses wpdb::$ms_global_tables
	 * @uses is_multisite()
	 *
	 * @param string $scope Optional. Can be all, global, ms_global, blog, or old tables. Defaults to all.
	 * @param bool $prefix Optional. Whether to include table prefixes. Default true. If blog
	 * 	prefix is requested, then the custom users and usermeta tables will be mapped.
	 * @param int $blog_id Optional. The blog_id to prefix. Defaults to wpdb::$blogid. Used only when prefix is requested.
	 * @return array Table names. When a prefix is requested, the key is the unprefixed table name.
	 */
	function tables( $scope = 'all', $prefix = true, $blog_id = 0 ) {
		switch ( $scope ) {
			case 'all' :
				$tables = array_merge( $this->global_tables, $this->tables );
				if ( is_multisite() )
					$tables = array_merge( $tables, $this->ms_global_tables );
				break;
			case 'blog' :
				$tables = $this->tables;
				break;
			case 'global' :
				$tables = $this->global_tables;
				if ( is_multisite() )
					$tables = array_merge( $tables, $this->ms_global_tables );
				break;
			case 'ms_global' :
				$tables = $this->ms_global_tables;
				break;
			case 'old' :
				$tables = $this->old_tables;
				break;
			default :
				return array();
				break;
		}

		if ( $prefix ) {
			if ( ! $blog_id )
				$blog_id = $this->blogid;
			$blog_prefix = $this->get_blog_prefix( $blog_id );
			$base_prefix = $this->base_prefix;
			$global_tables = array_merge( $this->global_tables, $this->ms_global_tables );
			foreach ( $tables as $k => $table ) {
				if ( in_array( $table, $global_tables ) )
					$tables[ $table ] = $base_prefix . $table;
				else
					$tables[ $table ] = $blog_prefix . $table;
				unset( $tables[ $k ] );
			}

			if ( isset( $tables['users'] ) && defined( 'CUSTOM_USER_TABLE' ) )
				$tables['users'] = CUSTOM_USER_TABLE;

			if ( isset( $tables['usermeta'] ) && defined( 'CUSTOM_USER_META_TABLE' ) )
				$tables['usermeta'] = CUSTOM_USER_META_TABLE;
		}

		return $tables;
	}

	/**
	 * Selects a database using the current database connection.
	 *
	 * The database name will be changed based on the current database
	 * connection. On failure, the execution will bail and display an DB error.
	 *
	 * @since 0.71
	 *
	 * @param string $db MySQL database name
	 * @param resource $dbh Optional link identifier.
	 * @return null Always null.
	 */
	function select( $db, $dbh = null ) {
		if ( is_null($dbh) )
			$dbh = $this->dbh;

		if ( !@mysql_select_db( $db, $dbh ) ) {
			$this->ready = false;
			//wp_load_translations_early();
			/*
			$this->bail( sprintf( __( '<h1>Can&#8217;t select database</h1>
<p>We were able to connect to the database server (which means your username and password is okay) but not able to select the <code>%1$s</code> database.</p>
<ul>
<li>Are you sure it exists?</li>
<li>Does the user <code>%2$s</code> have permission to use the <code>%1$s</code> database?</li>
<li>On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?</li>
</ul>
<p>If you don\'t know how to set up a database you should <strong>contact your host</strong>. If all else fails you may find help at the <a href="http://wordpress.org/support/">WordPress Support Forums</a>.</p>' ), htmlspecialchars( $db, ENT_QUOTES ), htmlspecialchars( $this->dbuser, ENT_QUOTES ) ), 'db_select_fail' );
	*/
			return;
		}
	}

	/**
	 * Weak escape, using addslashes()
	 *
	 * @see addslashes()
	 * @since 2.8.0
	 * @access private
	 *
	 * @param string $string
	 * @return string
	 */
	function _weak_escape( $string ) {
		return addslashes( $string );
	}

	/**
	 * Real escape, using mysql_real_escape_string() or addslashes()
	 *
	 * @see mysql_real_escape_string()
	 * @see addslashes()
	 * @since 2.8.0
	 * @access private
	 *
	 * @param  string $string to escape
	 * @return string escaped
	 */
	function _real_escape( $string ) {
		if ( $this->dbh && $this->real_escape )
			return mysql_real_escape_string( $string, $this->dbh );
		else
			return addslashes( $string );
	}

	/**
	 * Escape data. Works on arrays.
	 *
	 * @uses wpdb::_escape()
	 * @uses wpdb::_real_escape()
	 * @since  2.8.0
	 * @access private
	 *
	 * @param  string|array $data
	 * @return string|array escaped
	 */
	function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->_escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content for insertion into the database using addslashes(), for security.
	 *
	 * Works on arrays.
	 *
	 * @since 0.71
	 * @param string|array $data to escape
	 * @return string|array escaped as query safe string
	 */
	function escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array( $v ) )
					$data[$k] = $this->escape( $v );
				else
					$data[$k] = $this->_weak_escape( $v );
			}
		} else {
			$data = $this->_weak_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content by reference for insertion into the database, for security
	 *
	 * @uses wpdb::_real_escape()
	 * @since 2.3.0
	 * @param string $string to escape
	 * @return void
	 */
	function escape_by_ref( &$string ) {
		if ( ! is_float( $string ) )
			$string = $this->_real_escape( $string );
	}

	/**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 *
	 * The following directives can be used in the query format string:
	 *   %d (integer)
	 *   %f (float)
	 *   %s (string)
	 *   %% (literal percentage sign - no argument needed)
	 *
	 * All of %d, %f, and %s are to be left unquoted in the query string and they need an argument passed for them.
	 * Literals (%) as parts of the query must be properly written as %%.
	 *
	 * This function only supports a small subset of the sprintf syntax; it only supports %d (integer), %f (float), and %s (string).
	 * Does not support sign, padding, alignment, width or precision specifiers.
	 * Does not support argument numbering/swapping.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * Both %d and %s should be left unquoted in the query string.
	 *
	 * <code>
	 * wpdb::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 1337 )
	 * wpdb::prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
	 * </code>
	 *
	 * @link http://php.net/sprintf Description of syntax.
	 * @since 2.3.0
	 *
	 * @param string $query Query statement with sprintf()-like placeholders
	 * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
	 * 	being called like {@link http://php.net/sprintf sprintf()}.
	 * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/sprintf sprintf()}.
	 * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
	 * 	if there was something to prepare
	 */
	function prepare( $query, $args = null ) {
		if ( is_null( $query ) )
			return;

		if ( func_num_args() < 2 )
			_doing_it_wrong( 'wpdb::prepare', 'wpdb::prepare() requires at least two arguments.', '3.5' );

		$args = func_get_args();
		array_shift( $args );
		// If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array($args[0]) )
			$args = $args[0];
		$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
		$query = preg_replace( '|(?<!%)%f|' , '%F', $query ); // Force floats to be locale unaware
		$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
		array_walk( $args, array( $this, 'escape_by_ref' ) );
		return @vsprintf( $query, $args );
	}

	/**
	 * Print SQL/DB error.
	 *
	 * @since 0.71
	 * @global array $EZSQL_ERROR Stores error information of query and error string
	 *
	 * @param string $str The error to display
	 * @return bool False if the showing of errors is disabled.
	 */
	function print_error( $str = '' ) {
		global $EZSQL_ERROR;

		if ( !$str )
			$str = mysql_error( $this->dbh );
		$EZSQL_ERROR[] = array( 'query' => $this->last_query, 'error_str' => $str );

		if ( $this->suppress_errors )
			return false;

		//wp_load_translations_early();

		if ( $caller = $this->get_caller() )
			$error_str = sprintf( __( 'WordPress database error %1$s for query %2$s made by %3$s' ), $str, $this->last_query, $caller );
		else
			$error_str = sprintf( __( 'WordPress database error %1$s for query %2$s' ), $str, $this->last_query );

		error_log( $error_str );

		// Are we showing errors?
		if ( ! $this->show_errors )
			return false;

		// If there is an error then take note of it
		if ( is_multisite() ) {
			$msg = "WordPress database error: [$str]\n{$this->last_query}\n";
			if ( defined( 'ERRORLOGFILE' ) )
				error_log( $msg, 3, ERRORLOGFILE );
			if ( defined( 'DIEONDBERROR' ) )
				die( $msg );
		} else {
			$str   = htmlspecialchars( $str, ENT_QUOTES );
			$query = htmlspecialchars( $this->last_query, ENT_QUOTES );

			print "<div id='error'>
			<p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
			<code>$query</code></p>
			</div>";
		}
	}

	/**
	 * Enables showing of database errors.
	 *
	 * This function should be used only to enable showing of errors.
	 * wpdb::hide_errors() should be used instead for hiding of errors. However,
	 * this function can be used to enable and disable showing of database
	 * errors.
	 *
	 * @since 0.71
	 * @see wpdb::hide_errors()
	 *
	 * @param bool $show Whether to show or hide errors
	 * @return bool Old value for showing errors.
	 */
	function show_errors( $show = true ) {
		$errors = $this->show_errors;
		$this->show_errors = $show;
		return $errors;
	}

	/**
	 * Disables showing of database errors.
	 *
	 * By default database errors are not shown.
	 *
	 * @since 0.71
	 * @see wpdb::show_errors()
	 *
	 * @return bool Whether showing of errors was active
	 */
	function hide_errors() {
		$show = $this->show_errors;
		$this->show_errors = false;
		return $show;
	}

	/**
	 * Whether to suppress database errors.
	 *
	 * By default database errors are suppressed, with a simple
	 * call to this function they can be enabled.
	 *
	 * @since 2.5.0
	 * @see wpdb::hide_errors()
	 * @param bool $suppress Optional. New value. Defaults to true.
	 * @return bool Old value
	 */
	function suppress_errors( $suppress = true ) {
		$errors = $this->suppress_errors;
		$this->suppress_errors = (bool) $suppress;
		return $errors;
	}

	/**
	 * Kill cached query results.
	 *
	 * @since 0.71
	 * @return void
	 */
	function flush() {
		$this->last_result = array();
		$this->col_info    = null;
		$this->last_query  = null;

		if ( is_resource( $this->result ) )
			mysql_free_result( $this->result );
	}

	/**
	 * Connect to and select database
	 *
	 * @since 3.0.0
	 */
	function db_connect() {

		$this->is_mysql = true;

		$new_link = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
		$client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;

		if ( WP_DEBUG ) {
			$this->dbh = mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
		} else {
			$this->dbh = @mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
		}

		if ( !$this->dbh ) {
			//wp_load_translations_early();
			$this->bail( sprintf( __( "
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information in your <code>wp-config.php</code> file is incorrect or we can't contact the database server at <code>%s</code>. This could mean your host's database server is down.</p>
<ul>
	<li>Are you sure you have the correct username and password?</li>
	<li>Are you sure that you have typed the correct hostname?</li>
	<li>Are you sure that the database server is running?</li>
</ul>
<p>If you're unsure what these terms mean you should probably contact your host. If you still need help you can always visit the <a href='http://wordpress.org/support/'>WordPress Support Forums</a>.</p>
" ), htmlspecialchars( $this->dbhost, ENT_QUOTES ) ), 'db_connect_fail' );

			return;
		}

		$this->set_charset( $this->dbh );

		$this->ready = true;

		$this->select( $this->dbname, $this->dbh );
	}

	/**
	 * Perform a MySQL database query, using current database connection.
	 *
	 * More information can be found on the codex page.
	 *
	 * @since 0.71
	 *
	 * @param string $query Database query
	 * @return int|false Number of rows affected/selected or false on error
	 */
	function query( $query ) {
		if ( ! $this->ready )
			return false;

		// some queries are made before the plugins have been loaded, and thus cannot be filtered with this method
		$query = apply_filters( 'query', $query );

		$return_val = 0;
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES )
			$this->timer_start();

		$this->result = @mysql_query( $query, $this->dbh );
		$this->num_queries++;

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES )
			$this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );

		// If there is an error then take note of it..
		if ( $this->last_error = mysql_error( $this->dbh ) ) {
			$this->print_error();
			return false;
		}

		if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
			$return_val = $this->result;
		} elseif ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
			$this->rows_affected = mysql_affected_rows( $this->dbh );
			// Take note of the insert_id
			if ( preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
				$this->insert_id = mysql_insert_id($this->dbh);
			}
			// Return number of rows affected
			$return_val = $this->rows_affected;
		} else {
			$num_rows = 0;
			while ( $row = @mysql_fetch_object( $this->result ) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			// Log number of rows the query returned
			// and return number of rows selected
			$this->num_rows = $num_rows;
			$return_val     = $num_rows;
		}

		return $return_val;
	}

	/**
	 * Insert a row into a table.
	 *
	 * <code>
	 * wpdb::insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 * wpdb::insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 * </code>
	 *
	 * @since 2.5.0
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	function insert( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
	}

	/**
	 * Replace a row into a table.
	 *
	 * <code>
	 * wpdb::replace( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 * wpdb::replace( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 * </code>
	 *
	 * @since 3.0.0
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @return int|false The number of rows affected, or false on error.
	 */
	function replace( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
	}

	/**
	 * Helper function for insert and replace.
	 *
	 * Runs an insert or replace query based on $type argument.
	 *
	 * @access private
	 * @since 3.0.0
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @param string $type Optional. What type of operation is this? INSERT or REPLACE. Defaults to INSERT.
	 * @return int|false The number of rows affected, or false on error.
	 */
	function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
		if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
			return false;
		$formats = $format = (array) $format;
		$fields = array_keys( $data );
		$formatted_fields = array();
		foreach ( $fields as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			elseif ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$formatted_fields[] = $form;
		}
		$sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . implode( ",", $formatted_fields ) . ")";
		return $this->query( $this->prepare( $sql, $data ) );
	}

	/**
	 * Update a row in the table
	 *
	 * <code>
	 * wpdb::update( 'table', array( 'column' => 'foo', 'field' => 'bar' ), array( 'ID' => 1 ) )
	 * wpdb::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) )
	 * </code>
	 *
	 * @since 2.5.0
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string $table table name
	 * @param array $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param array|string $format Optional. An array of formats to be mapped to each of the values in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where. If string, that format will be used for all of the items in $where. A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $where will be treated as strings.
	 * @return int|false The number of rows updated, or false on error.
	 */
	function update( $table, $data, $where, $format = null, $where_format = null ) {
		if ( ! is_array( $data ) || ! is_array( $where ) )
			return false;

		$formats = $format = (array) $format;
		$bits = $wheres = array();
		foreach ( (array) array_keys( $data ) as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			elseif ( isset($this->field_types[$field]) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$bits[] = "`$field` = {$form}";
		}

		$where_formats = $where_format = (array) $where_format;
		foreach ( (array) array_keys( $where ) as $field ) {
			if ( !empty( $where_format ) )
				$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
			elseif ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$wheres[] = "`$field` = {$form}";
		}

		$sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
	}

	/**
	 * Delete a row in the table
	 *
	 * <code>
	 * wpdb::delete( 'table', array( 'ID' => 1 ) )
	 * wpdb::delete( 'table', array( 'ID' => 1 ), array( '%d' ) )
	 * </code>
	 *
	 * @since 3.4.0
	 * @see wpdb::prepare()
	 * @see wpdb::$field_types
	 * @see wp_set_wpdb_vars()
	 *
	 * @param string $table table name
	 * @param array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where. If string, that format will be used for all of the items in $where. A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $where will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @return int|false The number of rows updated, or false on error.
	 */
	function delete( $table, $where, $where_format = null ) {
		if ( ! is_array( $where ) )
			return false;

		$bits = $wheres = array();

		$where_formats = $where_format = (array) $where_format;

		foreach ( array_keys( $where ) as $field ) {
			if ( !empty( $where_format ) ) {
				$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
			} elseif ( isset( $this->field_types[ $field ] ) ) {
				$form = $this->field_types[ $field ];
			} else {
				$form = '%s';
			}

			$wheres[] = "$field = $form";
		}

		$sql = "DELETE FROM $table WHERE " . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, $where ) );
	}


	/**
	 * Retrieve one variable from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row, this function returns the value in the column and row specified.
	 * If $query is null, this function returns the value in the specified column and row from the previous SQL result.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
	 * @param int $x Optional. Column of value to return. Indexed from 0.
	 * @param int $y Optional. Row of value to return. Indexed from 0.
	 * @return string|null Database query result (as string), or null on failure
	 */
	function get_var( $query = null, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\", $x, $y)";
		if ( $query )
			$this->query( $query );

		// Extract var out of cached results based x,y vals
		if ( !empty( $this->last_result[$y] ) ) {
			$values = array_values( get_object_vars( $this->last_result[$y] ) );
		}

		// If there is a value return it else return null
		return ( isset( $values[$x] ) && $values[$x] !== '' ) ? $values[$x] : null;
	}

	/**
	 * Retrieve one row from the database.
	 *
	 * Executes a SQL query and returns the row from the SQL result.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query SQL query.
	 * @param string $output Optional. one of ARRAY_A | ARRAY_N | OBJECT constants. Return an associative array (column => value, ...),
	 * 	a numerically indexed array (0 => value, ...) or an object ( ->column = value ), respectively.
	 * @param int $y Optional. Row to return. Indexed from 0.
	 * @return mixed Database query result in format specified by $output or null on failure
	 */
	function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		if ( $query )
			$this->query( $query );
		else
			return null;

		if ( !isset( $this->last_result[$y] ) )
			return null;

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ) : null;
		} else {
			$this->print_error( " \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N" );
		}
	}

	/**
	 * Retrieve one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, this function returns the column specified.
	 * If $query is null, this function returns the specified column from the previous SQL result.
	 *
	 * @since 0.71
	 *
	 * @param string|null $query Optional. SQL query. Defaults to previous query.
	 * @param int $x Optional. Column to return. Indexed from 0.
	 * @return array Database query result. Array indexed from 0 by SQL result row number.
	 */
	function get_col( $query = null , $x = 0 ) {
		if ( $query )
			$this->query( $query );

		$new_array = array();
		// Extract the column values
		for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
			$new_array[$i] = $this->get_var( null, $x, $i );
		}
		return $new_array;
	}

	/**
	 * Retrieve an entire SQL result set from the database (i.e., many rows)
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 * @since 0.71
	 *
	 * @param string $query SQL query.
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. With one of the first three, return an array of rows indexed from 0 by SQL result row number.
	 * 	Each row is an associative array (column => value, ...), a numerically indexed array (0 => value, ...), or an object. ( ->column = value ), respectively.
	 * 	With OBJECT_K, return an associative array of row objects keyed by the value of each row's first column's value. Duplicate keys are discarded.
	 * @return mixed Database query results
	 */
	function get_results( $query = null, $output = OBJECT ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
			$this->query( $query );
		else
			return null;

		$new_array = array();
		if ( $output == OBJECT ) {
			// Return an integer-keyed array of row objects
			return $this->last_result;
		} elseif ( $output == OBJECT_K ) {
			// Return an array of row objects with keys from column 1
			// (Duplicates are discarded)
			foreach ( $this->last_result as $row ) {
				$var_by_ref = get_object_vars( $row );
				$key = array_shift( $var_by_ref );
				if ( ! isset( $new_array[ $key ] ) )
					$new_array[ $key ] = $row;
			}
			return $new_array;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				foreach( (array) $this->last_result as $row ) {
					if ( $output == ARRAY_N ) {
						// ...integer-keyed row arrays
						$new_array[] = array_values( get_object_vars( $row ) );
					} else {
						// ...column name-keyed row arrays
						$new_array[] = get_object_vars( $row );
					}
				}
			}
			return $new_array;
		}
		return null;
	}

	/**
	 * Load the column metadata from the last query.
	 *
	 * @since 3.5.0
	 *
	 * @access protected
	 */
	protected function load_col_info() {
		if ( $this->col_info )
			return;

		for ( $i = 0; $i < @mysql_num_fields( $this->result ); $i++ ) {
			$this->col_info[ $i ] = @mysql_fetch_field( $this->result, $i );
		}
	}

	/**
	 * Retrieve column metadata from the last query.
	 *
	 * @since 0.71
	 *
	 * @param string $info_type Optional. Type one of name, table, def, max_length, not_null, primary_key, multiple_key, unique_key, numeric, blob, type, unsigned, zerofill
	 * @param int $col_offset Optional. 0: col name. 1: which table the col's in. 2: col's max length. 3: if the col is numeric. 4: col's type
	 * @return mixed Column Results
	 */
	function get_col_info( $info_type = 'name', $col_offset = -1 ) {
		$this->load_col_info();

		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				$new_array = array();
				foreach( (array) $this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}

	/**
	 * Starts the timer, for debugging purposes.
	 *
	 * @since 1.5.0
	 *
	 * @return true
	 */
	function timer_start() {
		$this->time_start = microtime( true );
		return true;
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @since 1.5.0
	 *
	 * @return float Total time spent on the query, in seconds
	 */
	function timer_stop() {
		return ( microtime( true ) - $this->time_start );
	}

	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if wpdb::$show_errors is false.
	 *
	 * @since 1.5.0
	 *
	 * @param string $message The Error message
	 * @param string $error_code Optional. A Computer readable string to identify the error.
	 * @return false|void
	 */
	function bail( $message, $error_code = '500' ) {
		if ( !$this->show_errors ) {
			if ( class_exists( 'WP_Error' ) )
				$nice;
//				this->error = new WP_Error($error_code, $message);
			else
				$this->error = $message;
			return false;
		}
//		wp_die($message);
	}

	/**
	 * Whether MySQL database is at least the required minimum version.
	 *
	 * @since 2.5.0
	 * @uses $wp_version
	 * @uses $required_mysql_version
	 *
	 * @return WP_Error
	 */
	function check_database_version() {
		global $wp_version, $required_mysql_version;
		// Make sure the server has the required MySQL version
		if ( version_compare($this->db_version(), $required_mysql_version, '<') )
//			return new WP_Error('database_version', sprintf( __( '<strong>ERROR</strong>: WordPress %1$s requires MySQL %2$s or higher' ), $wp_version, $required_mysql_version ));
		$nice;
	}

	/**
	 * Whether the database supports collation.
	 *
	 * Called when WordPress is generating the table scheme.
	 *
	 * @since 2.5.0
	 * @deprecated 3.5.0
	 * @deprecated Use wpdb::has_cap( 'collation' )
	 *
	 * @return bool True if collation is supported, false if version does not
	 */
	function supports_collation() {
		_deprecated_function( __FUNCTION__, '3.5', 'wpdb::has_cap( \'collation\' )' );
		return $this->has_cap( 'collation' );
	}

	/**
	 * The database character collate.
	 *
	 * @since 3.5.0
	 *
	 * @return string The database character collate.
	 */
	public function get_charset_collate() {
		$charset_collate = '';

		if ( ! empty( $this->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $this->charset";
		if ( ! empty( $this->collate ) )
			$charset_collate .= " COLLATE $this->collate";

		return $charset_collate;
	}

	/**
	 * Determine if a database supports a particular feature
	 *
	 * @since 2.7.0
	 * @see   wpdb::db_version()
	 *
	 * @param string $db_cap the feature
	 * @return bool
	 */
	function has_cap( $db_cap ) {
		$version = $this->db_version();

		switch ( strtolower( $db_cap ) ) {
			case 'collation' :    // @since 2.5.0
			case 'group_concat' : // @since 2.7
			case 'subqueries' :   // @since 2.7
				return version_compare( $version, '4.1', '>=' );
			case 'set_charset' :
				return version_compare($version, '5.0.7', '>=');
		};

		return false;
	}

	/**
	 * Retrieve the name of the function that called wpdb.
	 *
	 * Searches up the list of functions until it reaches
	 * the one that would most logically had called this method.
	 *
	 * @since 2.5.0
	 *
	 * @return string The name of the calling function
	 */
	function get_caller() {
//		return wp_debug_backtrace_summary( __CLASS__ );
	}

	/**
	 * The database version number.
	 *
	 * @since 2.7.0
	 *
	 * @return false|string false on failure, version number on success
	 */
	function db_version() {
		return preg_replace( '/[^0-9.].*/', '', mysql_get_server_info( $this->dbh ) );
	}
}

?>