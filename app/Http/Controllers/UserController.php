<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
//ONLY USED BY SHITTY WORDPRESS CODE
/* 
const DB_HOST = "yeplive-dev.czc6detrzhw4.us-west-2.rds.amazonaws.com:3306";
const DOMAIN_ROOT = "example.com";
const DB_USER = "root";
const DB_PASSWORD = "rootroot";
const DB_NAME = "test";
*/

class UserController extends Controller {
	public function __construct()
	{
	}

	public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');

	 try {
		// attempt to verify the credentials and create a token for the user
		if (! $token = \JWTAuth::attempt($credentials)) {
			return response()->json(['error' => 'invalid_credentials'], 401);
		}
	} catch (\JWTException $e) {
		// something went wrong whilst attempting to encode the token
		return response()->json(['error' => 'could_not_create_token'], 500);
	}
		// all good so return the token
		return response()->json(compact('token'));
	}

	public function check_auth(Request $request)
	{
		try {
		return \JWTAuth::parseToken()->toUser();
		} catch (\JWTException $e){
		return response()->json(['error' => 'no_token'], 401);	
	}
 
	}

	public function index()
	{
		return \App\User::all();
	}

	public function me()
	{
		$user = \JWTAuth::parseToken()->toUser();
		return $user;
	}

	public function store(Request $request)
	{
		$params = $request->only("email", "password");
		$params["password"] = \Hash::make($params["password"]);
		$user = \App\User::create($params);
		return $user;
	}

//LEAVING OLD WORDPRESS CODE IN FOR REFERENCE PLS NO DELETEO
/*
	//MOBILE ALIAS METHODS
	public function become_fan(Request $request)
	{
		$user_id = $request->input("user_id");
		$loginUserId = $request->input("loginUserId");
		$need = $request->input("Need");

	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		return "mysql_connect error";
	}
	if(!mysql_select_db(DB_NAME , $link))
	{
		return "mysql_select_db error";
	}
	$sql_query = "select user_id,pan_id from wp_user_pans where user_id = '" . $user_id . "' and pan_id = '" . $loginUserId . "'";
	$result = mysql_query($sql_query);
	
	if(!$result)
	{
		return "mysql_query error";
	}
	$i = 0;
	while($row = mysql_fetch_array($result))
	{
		$i++;
	}
	if($i > 0)
	{
		mysql_free_result($result);
		mysql_close($link);
		return "exist.";
	}
	mysql_free_result($result);
	
	$sql_query = "insert into wp_user_pans(user_id,pan_id) values('" . $user_id ."' , '" . $loginUserId . "')";
	if(!mysql_select_db(DB_NAME , $link))
	{
		return "mysql_select_db error";
	}
	$result = mysql_query($sql_query);
	if(!$result)
	{
		return "mysql_query error";
	}

    $sql_query = "insert into wp_notifications (user_receiver_id, user_sender_id, type, action, picture_path) values ('" . $loginUserId . "' , '" . $user_id . "', '1' ,
                                                (
                                                    SELECT CASE WHEN b.wp_user_id IS NOT NULL THEN (SELECT user_login FROM wp_users WHERE ID = b.wp_user_id)
                                                    WHEN b.facebook_name IS NOT NULL THEN b.facebook_name
                                                    ELSE b.twitter_name END user_login
                                                    FROM wp_user_yep b WHERE user_id = '". $user_id ."'
                                                )
                                                ,
                                                (
                                                    SELECT CASE WHEN picture_path IS NOT NULL THEN picture_path
                                                                    WHEN facebook_name IS NOT NULL THEN facebook_picture
                                                                    WHEN twitter_name IS NOT NULL THEN twitter_img
                                                                    ELSE NULL
                                                                    END picture_path FROM wp_user_yep WHERE user_id = '". $user_id ."'
                                                )
                                            )";
    if(!mysql_select_db(DB_NAME , $link))
    {
        return "mysql_select_db error";
    }
    $result = mysql_query($sql_query);
    if(!$result)
    {
        return "mysql_query error";
    }

	//mysql_free_result($result);
	if($need == "")
	{
		mysql_close($link);
		return "success";
	}
	else
	{
		
		$sql_query = "select  wu.user_nicename ,
							  lu.facebook_name,
							  lu.facebook_id,
							  lu.twitter_name,
							  lu.twitter_img, 
							wu.user_email ,
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
							 lup.user_id = '" . $user_id . "'
					  and
							 lup.pan_id = '" . $loginUserId . "'";
		
		if(!mysql_select_db(DB_NAME , $link))
		{
			return "mysql_select_db error";
		}
		
		$result = mysql_query($sql_query);
		if(!$result)
		{
			return "mysql_query error";
		}
		
		$row = mysql_fetch_array($result);
		
		$user_name = $row['user_nicename'];
		$user_email = $row['user_email'];
		$pan_id = $loginUserId;
		$picture_path = $row['picture_path'];
		$facebook_name = $row['facebook_name'];
		$facebook_id = $row['facebook_id'];
		$twitter_name = $row['twitter_name'];
		$twitter_img = $row['twitter_img'];
		
		mysql_free_result($result);
		mysql_close($link);
		return $user_name . "&&" . $user_email . "&&" . $pan_id . "&&" . $picture_path . "&&" . $facebook_name . "&&" . $twitter_name . "&&" . $facebook_id . "&&" . $twitter_img;
	}

	}

	public function is_follow(Request $request)
	{
		$user_id = $request->input("user_id");
		$pan_id = $request->input("pan_id");
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME,$link))
		{
			return "mysql_select_db error";
		}
	

    $sql_query = "select COUNT(*) as isFollow from wp_user_pans where user_id = '" . $user_id . "' and pan_id = '" . $pan_id . "'";
    $result = mysql_query($sql_query);
    $row = mysql_fetch_array($result);
    $isFollow = $row['isFollow'];


    if ($isFollow == 0)
    {
       return "false";
    }
    else
    {
        return "true";
    }

	}

	public function upload_thumbnail(Request $request)
	{	
		
		return 200;
	}

	public function update_user_pic(Request $request)
	{
		$user_id = $request->input("user_id");
		$userpic = $request->input("userpic");
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
	if(!$link)
	{
		return "mysql_connect error";
	}
	if(!mysql_select_db(DB_NAME , $link))
	{
		return "mysql_select_db error";
	}

	$sql_query = "UPDATE wp_user_yep SET picture_path = '" . $userpic . "' where user_id = '" . $user_id . "'";
	$result = mysql_query($sql_query);
	return $result;
	}

	public function update_user_settings(Request $request)
	{
		$chat_response_notifications = $request->input("chat_response_notifications");
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		if(!$link)
		{
			return "mysql_connect error";
		}
		if(!mysql_select_db(DB_NAME , $link))
		{
			return "mysql_select_db error";
		}

    $sql_query = "SELECT * FROM wp_user_settings WHERE user_id = '" . $user_id . "'";
    $result = mysql_query($sql_query);
    if (mysql_fetch_array($result))
    {
        $sql_query = "UPDATE wp_user_settings SET chat_response_notifications = '" . $chat_response_notifications. "' where user_id = '" . $user_id . "'";
        $result = mysql_query($sql_query);
        if ($result)
        {
            return "success";
        }
        else
        {
            return "failed";
        }
    }
    else
    {
        $sql_query = "INSERT INTO wp_user_settings (user_id, chat_response_notifications) VALUES ('" . $user_id. "', '" . $chat_response_notifications . "');";
        $result = mysql_query($sql_query);
        if ($result)
        {
            return "success";
        }
        else
        {
            return "failed";
        }
    }

	}	
*/
}
