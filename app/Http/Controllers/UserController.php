<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UserController extends Controller {

	public function __construct()
	{
	}

	/*
	 * POST : Authenticate a user
	 *
	 * @param  {Array}    $request      An array consists of user email and password keys
	 * @return {Array}    response()    User token (if any), Authentication status
	 */
	public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');
		$output = \App\Algorithm\ProAuth::jwtAuth($credentials);
		return response()->json([$output['status'] => $output['message']], $output['code']);
	}

	/*
	 * GET : Check user auth token
	 *
	 * @return {Array}    response()    User information
	 */
	public function checkAuth()
	{
		try {
			return \JWTAuth::parseToken()->toUser();
		} catch (\JWTException $e){
			return response()->json(['error' => 'no_token'], 401);
		}
	}

	/*
	 * GET : Get all registered users
	 *
	 * @return {Object}    All users in db
	 */
	public function showAllUsers()
	{
		return \App\User::all();
	}

	/*
	 * POST : Take in user signup credentials, and return with user signup credentials and auth token
	 *
	 * @param  {Array}   $request    An array consists of user email and password keys
	 * @return {Array}   response()  An array consists of user email, password, and auth token
	 */
	public function signup(Request $request)
	{
		$params = $request->only('email', 'password');

		$validator = \Validator::make( $params, [
			'email' => 'required|email|unique:yeplive_users,email',
			'password' => 'required'
		]);

		if ( $validator -> fails() )
		{
			return response()-> json(['error' => 'invalid_input'], 400);
		}
		//Catch the unhashed password for auth token
		$password = $params['password'];
		$params['password'] = \Hash::make($params['password']);

		$user = \App\Algorithm\ProSave::user($params);
		$output = \App\Algorithm\ProAuth::jwtAuth(array('email' => $user['email'], 'password' => $password));
		return response()->json([ 'user_id' => $user['user_id'],
								  'email' => $user['email'], 
								  'password' => $user['password'], 
								  $output['status'] => $output['message'] 
								], 200);
	}

	/*
	public function me()
	{
		$user = \JWTAuth::parseToken()->toUser();
		return $user;
	}*/

	/*
	 * POST : Add a follower and followee relationship
	 *
	 * @param  {Array}   $request    Payload consists of Followee user_id
	 * @param  {int}     $id         Follower user_id
	 */
	public function addFollowing(Request $request, $id)
	{	
		$params = $request->only('user_id');

		$validator = \Validator::make( $params, [
			'user_id' => 'required|exists:yeplive_users,user_id',
		]);

		if ( $validator -> fails() )
		{
			return response()-> json(['error' => 'invalid_followee'], 400);
		}

		//Get token source
		try {
			$user = $this->checkAuth();
		} catch (\Exception $e) {
			return response()->json(['error' => 'no_token'], 401);
		}

		//If this user sends this reqeust
		if ($id == $user['user_id']){

			$following_obj = new \App\Following;
			$following_obj->user_id = $params['user_id'];
			$following_obj->follower_id = $id;
			try {
				$following_obj->save();
				return response()->json(['success' => 'followed'], 200);
			} catch (\Exception $e){
				return response()->json(['error' => 'followed_already'], 401);
			}
		}
		else {
			return response()->json(['error' => 'unauthorized'], 401);
		}
	}

	/*
	 * GET : Get all all followees followed by follower {id}
	 *
	 * @return {Object}    All followees followed by follower {id}
	 */
	public function showAllFollowee($id)
	{
		return $followees = \App\Following::where('follower_id' , '=' , $id )->get();
	}

	/*
	 * GET : Get all all followers following followee {id}
	 *
	 * @return {Object}    All followers following followee {id}
	 */
	public function showAllFollower($id)
	{
		return $followees = \App\Following::where('user_id' , '=' , $id )->get();
	}

	public function changePassword(Request $request, $id)
	{
		//Validate POST request
		$params = $request->only('old_password', 'new_password');

		$validator = \Validator::make( $params, [
			'old_password' => 'required',
			'new_password' => 'required'
		]);

		if ( $validator -> fails() )
		{
			return response()-> json(['error' => 'invalid_input'], 400);
		}

		//Verify if request source has authority to change password
		try {
			$user = $this->checkAuth();
		} catch (\Exception $e) {
			return response()->json(['error' => 'no_token'], 401);
		}
		if($id == $user->user_id){
			$credentials = \App\User::find($id);
			
			if( \Auth::attempt(['email' => $credentials['email'], 'password' => $params['old_password']]) ){
				$credentials['password'] = \Hash::make($params['new_password']);
				$credentials->save();
				return response()->json(['success' => 'password_has_modified'], 200);
			}
			else{
				return response()->json(['error' => 'password_not_matched'], 401);
			}
		}
		else{
			return response()->json(['error' => 'unauthorized'], 401);
		}

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
>>>>>>> Stashed changes
}
