<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UsersController extends Controller {

	public function __construct()
	{
	}
	/*
	 * GET : Check user auth token
	 *
	 * @return {Array}    response()    User information
	 */
	public function checkAuth()
	{
		try {
			$user = \JWTAuth::parseToken()->toUser();
			return $user;
		} catch (\JWTException $e){
			return response()->json(['error' => 'no_token'], 401);
		}
	}

	/*
	 * GET : Get all registered users
	 *
	 * @return {Object}    All users in db
	 */
	public function index(Request $request)
	{
		$name = $request->input('name');
		if(! $name)
		{
			return \App\Errors::invalid('must supply name');
		}

		$user = \App\User::where('display_name','=',$name)->get()->first();

		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		$user -> followers  = $user->followers();
		$user -> following = $user->following();
		$user -> yeps = $user->yeps;

		return response()->json($user,200);
	}

	public function show(Request $request, $id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('yep not found');
		}
		//$user -> yeps = \App\Yep::where('user_id', '=', $user->user_id)->get();	
		try{
			$currentUser = \JWTAuth::parseToken()->toUser();
			if($currentUser)
			{
			$follow = \App\Follower::where('follower_id','=',$currentUser->user_id)
				->where('followee_id','=',$user->user_id)->get()->first();
			if($follow){
				$user->is_following = 1;
			} else {
				$user->is_following = 0;
			}
		} else {
				$user->is_following = 0;
		}
		} catch(\Exception $e){
		}

		
		$user -> follower_count  = $user->followerCount();	
		$user -> following_count = $user->followingCount();
		$user -> yeps_count = count($user->yeps);
		return response()->json($user,200);
	}

	public function getUserYeps(Request $request, $id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('yep not found');
		}

		$yeps = \App\Yep::where('user_id', '=', $user->user_id)->orderBy('created_at','desc')->get();	

		return response()->json(['yeps' => $yeps], 200);	
	}


	public function setFacebookToken(Request $request, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{		
		$user = \JWTAuth::parseToken()->toUser();

		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		if(! $user->isFacebookAuthed($fb)){
			return \App\Errors::unauthorized('user is not authed with facebook');
		}

		
		if($request->has('access_token')){
			$user->facebook_access_token = $request->input('access_token');
			return response()->json(['success'=>1, 'id'=>$user->user_id]);
		} else {
			return \App\Errors::invalid('no token provided');
		}
	}

	/*
	 * POST : Take in social login tokens and return with token and user id
	 */
	public function socialAuth(Request $request, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$user_ip = $request->ip();
		//FACEBOOK
		if($request->has('facebook_access_token') && $request->has('facebook_user_id'))
		{
			$params = $request->only(
				'facebook_access_token',
				'facebook_user_id'
			);
			$fb->setDefaultAccessToken($params['facebook_access_token']);
			try {
				$response = $fb->get('/me?fields=id,email,first_name,picture.type(large)');
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				return \Errors\invalid('invalid facebook token');
				dd($e->getMessage());
			}
			$data = $response->getGraphObject();
			$facebookId = $data->getProperty('id');
			if($facebookId != $params['facebook_user_id'])
			{
				return \App\Errors::invalid('facebook id mismatch');
			}	
			$user = \App\User::where('facebook_id', '=', $params['facebook_user_id'])->first();
			if(! $user)
			{
				$newUserParams = [
					'email' => $data->getProperty('email') || 'no email',
					'facebook_id' => $data->getProperty('id'),
					'picture_path' => $data->getProperty('picture')->getProperty('url'),
					'display_name' => $data->getProperty('first_name'),
					'facebook_access_token' => $params['facebook_access_token']
				];
				$user = \App\User::create($newUserParams);
			} else { 
				$user -> facebook_access_token = $params['facebook_access_token'];
				$user -> registration_ip = $user_ip;
				$user -> save();
			}

			$jwtoken = \JWTAuth::fromUser($user);

			return response()->json(['success' => 1, 'token' => $jwtoken, 'id' => $user->user_id, 'display_name' => $user->display_name, 'picture_path' => $user->picture_path]);	
			return response()->json(['success' => 1, 'token' => $jwtoken, 'id' => $user->user_id]);	
		}
		else if($request->has('twitter_access_token') && $request->has('twitter_user_id') &&
			$request->has('twitter_secret_token') )
		{
			 $request_token = [
					'token' =>$request->input('twitter_access_token'),
					'secret' => $request->input('twitter_secret_token')
        ];

			\Twitter::reconfig($request_token);
			$credentials = \Twitter::getCredentials();
			if($request->input('twitter_user_id') != $credentials->id){
				return \App\Errors::invalid('twitter id mismatch');
			}
			$user = \App\User::where('twitter_id','=', $credentials->id)->get()->first();
			if(! $user)
			{
				$newUserParams = [
					'email' => strval(time()),
					'name' => $credentials->name,
					'twitter_id' => $credentials->id,
					'display_name' => $credentials->screen_name,
					'picture_path' => $credentials->profile_image_url,
					'twitter_oauth_token' => $request->input('twitter_access_token'),
					'twitter_oauth_token_secret' => $request->input('twitter_secret_token')
				];

				$user = \App\User::create($newUserParams);
			} else {
				$user -> twitter_oauth_token = $request->input('twitter_access_token');
				$user -> twitter_oauth_token_secret = $request->input('twitter_secret_token');
				$user -> registration_ip = $user_ip;
				$user -> save();
			}

			$jwtoken = \JWTAuth::fromUser($user);
			return response()->json(['success' => 1, 'token' => $jwtoken, 'id' => $user->user_id, 'display_name' => $user->display_name, 'picture_path' => $user->picture_path]);	
		}
		else if($request->has('google_access_token') && $request->has('google_user_id'))
		{
			$client = new \Google_Client();
			$access_token = $request->input('google_access_token');
			$access_token_json = json_encode([
				"access_token" =>$access_token,
				"token_type"=>"Bearer",
				"expires_in"=>3600,
				"id_token"=>$access_token,
				"created"=>time(),
				"refresh_token"=>''
			]);
			$client->setAccessToken($access_token_json);
			$plus = new \Google_Service_Plus($client);
			$person = $plus->people->get('me');
			$id = $person->id;
			if($request->input('google_user_id') != $id)
			{
				return \App\Errors::invalid('google id mismatch');
			}
			$user = \App\User::where('google_id','=', $id)->get()->first();
			if(! $user)
			{
				$newUserParams = [
					'name' => $person->displayName,
					'google_id' => $person->id,
					'display_name' => $person->displayName,
					'picture_path' => $person->getImage()->url,
					'google_access_token' => $request->input('google_access_token')
				];
				$user = \App\User::create($newUserParams);
			} else {
				$user -> google_access_token = $request->input('google_access_token');
				$user -> registration_ip = $user_ip;
				$user -> save();
			}
			$jwtoken = \JWTAuth::fromUser($user);
			return response()->json(['success' => 1, 'token' => $jwtoken, 'id' => $user->user_id, 'display_name' => $user->display_name, 'picture_path' => $user->picture_path]);	
			return response()->json(['success' => 1, 'token' => $jwtoken, 'id' => $user->user_id]);	
		}
		//400
		else
		{
			return \App\Errors::invalid('no access token / user id provided');
		}
	}

	//TODO
	public function shareAll()
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		if($user->shareAll())
		{

		} else {

		};
	}

	//GET PERMISSIONS FROM FACEBOOK
	public function shareFacebook(Request $request, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$user = \JWTAuth::parseToken()->toUser();

		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		$message = $request->input('message');
		if(! $message){
			$message = '';
		}

		if(! $user->isFacebookAuthed($fb)){
			return \App\Errors::unauthorized('user is not authed with facebook');
		}
		if($user->shareFacebook($fb, $message)){
			return response()->json(['success' => 1, 'id' => $user->user_id],200);	
		} else {
			return \App\Errors::invalid('');
		}
	}

	public function getFriends(Request $request, $id, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		$friends = $user->getAllFriends($fb);

		return response()->json($friends, 200);
	}

	//
	public function shareGoogle(Request $request, $id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		if(! $user->google_access_token)
		{
			return \App\Errors::unauthorized('no google access token found');
		}

		$client = new \Google_Client();
		$access_token = $user->google_access_token;
		$access_token_json = json_encode([
			"access_token" =>$access_token,
			"token_type"=>"Bearer",
			"expires_in"=>3600,
			"id_token"=>$access_token,
			"created"=>time(),
			"refresh_token"=>''
		]);
		$client->setAccessToken($access_token_json);
		$plus = new \Google_Service_Plus($client);
//		$moment = new \Google_Moment();
 //   $moment->setType('http://schemas.google.com/AddActivity');
    $itemScope = new \Google_ItemScope();
    $itemScope->setUrl('https://developers.google.com/+/plugins/snippet/examples/thing');
    $moment->setTarget($itemScope);
    $plus->moments->insert('me', 'vault', $moment);
		return response()->json(['success'=>1,'id'=>$id],200);

	}

	public function shareTwitter(Request $request)
	{
		try {
			$user = \JWTAuth::parseToken()->toUser();
		} catch(\Exception $e) {
			return \App\Errors::unauthorized('user is not authorized');
		}

		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		$yep = \App\Yep::where('user_id','=',$user->user_id)->orderBy('created_at','desc')->get()->first();

		if(! $yep)
		{
			return \App\Errors::invalid('no');
		}
		
		$hash = \App\Algorithm\ProHash::toHash($yep->id);

		if($user->shareTwitter($hash))
		{
			return response()->json(["success"=>1],200);		
		} 
		else
		{
			return \App\Errors::invalid('an error occured when sharing');
		}
	}

	//This function will link existing users other social media accounts
	public function linkSoclai(Request $request, $id)
	{	
		$user = \App\User::find($id);
		if(! $user)
		{
			\App\Errors::notFound('user not found');
		}	

			//FACEBOOK
		if($request->has('facebook_access_token') && $request->has('facebook_user_id'))
		{
			$params = $request->only(
				'facebook_access_token',
				'facebook_user_id'
			);
			$fb->setDefaultAccessToken($params['facebook_access_token']);
			try {
				$response = $fb->get('/me?fields=id,email,first_name,picture.type(large)');
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				return \Errors\invalid('invalid facebook token');
				dd($e->getMessage());
			}
			$data = $response->getGraphObject();
			$facebookId = $data->getProperty('id');
			if($facebookId != $params['facebook_user_id'])
			{
				return \App\Errors::invalid('facebook id mismatch');
			}	
			$user -> facebook_access_token = $params['facebook_access_token'];
			$user -> save();

			return response()->json(['success' => 1, 'id' => $user->user_id]);
		}
		else if($request->has('twitter_access_token') && $request->has('twitter_user_id') &&
			$request->has('twitter_secret_token') )
		{
			 $request_token = [
					'token' =>$request->input('twitter_access_token'),
					'secret' => $request->input('twitter_secret_token')
        ];

			\Twitter::reconfig($request_token);
			$credentials = \Twitter::getCredentials();
			if($request->input('twitter_user_id') != $credentials->id){
				return \App\Errors::invalid('twitter id mismatch');
			}
			$user -> twitter_oauth_token = $request->input('twitter_access_token');
			$user -> twitter_oauth_token_secret = $request->input('twitter_secret_token');
			$user -> save();

			return response()->json(['success' => 1, 'id' => $user->user_id]);
		}
		else if($request->has('google_access_token') && $request->has('google_user_id'))
		{
			$client = new \Google_Client();
			$access_token = $request->input('google_access_token');
			$access_token_json = json_encode([
				"access_token" =>$access_token,
				"token_type"=>"Bearer",
				"expires_in"=>3600,
				"id_token"=>$access_token,
				"created"=>time(),
				"refresh_token"=>''
			]);
			$client->setAccessToken($access_token_json);
			$plus = new \Google_Service_Plus($client);
			$person = $plus->people->get('me');
			$id = $person->id;
			if($request->input('google_user_id') != $id)
			{
				return \App\Errors::invalid('google id mismatch');
			}
			$user -> google_access_token = $request->input('google_access_token');
			$user -> save();

			return response()->json(['success' => 1, 'id' => $user->user_id]);
		}
		//400
		else
		{
			return \App\Errors::invalid('no access token / user id provided');
		}

		
	}

	public function setDisplayName(Request $request)
	{
			
	}

	public function massFollow(Request $request)
	{
		$followees = $request->input('users');

		try {
			$user = \JWTAuth::parseToken()->toUser();
		} catch(\Exception $e) {
			return \App\Errors::unauthorized('user is not authorized');
		}

		foreach($followees as $followee_id)
		{
			$following_obj = new \App\Follower;
			$following_obj->followee_id = $folloee->user_id;
			$following_obj->follower_id = $currentUser->user_id;
			$following_obj->save();
		}		

		return response()->json(['success'=>1, 'id'=>$user->user_id]);
	}

	/*
	 * POST : Add a follower and followee relationship
	 *
	 * @param  {Array}   $request    Payload consists of Followee user_id
	 * @param  {int}     $id         Follower user_id
	 */
	public function follow(Request $request, $id)
	{	
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		//Get token source
		try {
			$currentUser = $this->checkAuth();
		} catch (\Exception $e) {
			return response()->json(['error' => 'no_token'], 401);
		}

		$followed = \App\Follower::where('follower_id', '=', $currentUser->user_id)
			->where('followee_id', '=', $user->user_id)->get()->first();
		if($followed){
			//unfollow
			$followed->delete();
			return response()->json(['success'=>1, 'id' => $id, 'following' => 0], 200);
		}


		$following_obj = new \App\Follower;
		$following_obj->followee_id = $user->user_id;
		$following_obj->follower_id = $currentUser->user_id;
		$following_obj->save();
		
		return response()->json(['success' => 1, 'id' => $id, 'following' => 1], 200);
	}

	public function unfollow(Request $request, $id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('user not found');
		}

		try {
			$currentUser = $this->checkAuth();
		} catch (\Exception $e) {
			return response()->json(['error' => 'no_token'], 401);
		}

		$followed = \App\Follower::where('follower_id', '=', $currentUser->user_id)
			->where('followee_id', '=', $user->user_id)->get()->first();
		if(! $followed){
			return \App\Errors::invalid('not following');
		}
		$followed->delete();
		
		return response()->json(['success' => 1, 'id' => $id], 200);
		
	}

	/*
	 * GET : Get all all followees followed by follower {id}
	 *
	 * @return {Object}    All followees followed by follower {id}
	 */
	public function getFollowers($id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Error::notFound('user not found');
		}
		$followers = $user->followers();
		return response()->json(['users' => $followers]);
		//return $followees = \App\Following::where('follower_id' , '=' , $id )->get();
	}

	/*
	 * GET : Get all all followers following followee {id}
	 *
	 * @return {Object}    All followers following followee {id}
	 */
	public function getFollowing($id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Error::notFound('user not found');
		}
		$followers = $user->following();
		return response()->json(['users' => $followers]);
	}
	/*
	 * POST : Change user password
	 *
	 * @param  {String}    $id    			User id
	 * @param  {Object}    $request 		Payload contains old and new email
	 * @return {Object}    Json response    Return status
	 */
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
	/*
	 * POST : Change user email
	 *
	 * @param  {String}    $id    			User id
	 * @param  {Object}    $request 		Payload contains new email
	 * @return {Object}    Json response    Return status
	 */
	public function changeEmail(Request $request, $id)
	{
		//Validate POST request
		$params = $request->only('new_email');

		$validator = \Validator::make( $params, [
			'new_email' => 'required|email|'
		]);

		if ( $validator -> fails() )
		{
			return \App\Errors::invalid(null, $validator);
		}
		//Changing user email
		$credentials = \App\User::find($id);
		$credentials['email'] = $params['new_email'];
		$credentials->save();
		return response()->json(['success' => 1, 'id' => $credentials->user_id ], 200);
	}

	public function updatePicture(Request $request, $id)
	{
		$params = $request->only(
			'picture'
		);
		$validator = \Validator::make( $params, [
			'picture' => 'required|image'
		]);

		if ( $validator -> fails() )
		{
			return \App\Errors::invalid(null, $validator);
		}

		if (! $request->hasFile('picture'))
		{
			return \App\Errors::invalid('not found');
		}
		$file = $request->file('picture');
		if(! $file->isValid())
		{
			return \App\Errors::invalid('invalid upload');
	
		}

		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound();
		}
		$url = \App\Algorithm\ProS3::storeThumbnail($user, $file);
		$user -> picture_path = $url;
		$user->save();
		return response()->json(['success' => 1, 'id'=> $user->user_id]);
	}

	public function getSettings(Request $request, $id)
	{
		$user = \App\User::find($id);
		return response()->json(['push_notifications' => $user->push_notifications, 'device_token' => $user->device_token]);
	}

	public function settings(Request $request, $id)
	{
		$user = \App\User::find($id);

		if(! $user)
		{
			return \App\Errors::notFound();
		}

		$params = $request->only(
			'push_notifications',
			'device_token',
			'status',
			'display_name'
		);

		$validator = \Validator::make( $params, [
			'push_notifications' => 'boolean',
			'device_token' => 'string|max:255'
		]);

		if ( $validator -> fails() )
		{
			return \App\Errors::invalid(null, $validator);
		}
		if($params['push_notifications'])
		{
			$push = $params['push_notifications'];
			$user->push_notifications = (boolean) $request->input('push_notifications');
		}
		if($request->has('device_token'))
		{
			$user->device_token = $request->input('device_token');
		}	
		if($request->has('display_name'))
		{
			$user->display_name = $request->input('display_name');
		}	
		if($request->has('status'))
		{
			$user->status= $request->input('status');
		}	
		$user->save();
		return response()->json(['success'=>1, 'id' => $user->user_id]);
	}


//THESE METHODS WILL BE REMOVED IN PRODUCTION


	/*
	 * POST: Sign in through facebook
	 */
	public function facebookLogin(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb,  Request $request)
	{
		$url = $fb->getLoginUrl(['email','public_profile']);
		try {
			$user = \JWTAuth::parseToken()->toUser();
		} catch(\Exception $e) {
				redirect()->away($url);
		}
	}

	public function facebookCallback(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
	 try {
			$token = $fb->getAccessTokenFromRedirect();
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
			dd($e->getMessage());
		}
	  $fb->setDefaultAccessToken($token);
		try {
			$response = $fb->get('/me?fields=id,email,first_name,picture.type(large)');
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
			dd($e->getMessage());
    }
		$data = $response->getGraphObject();

		$userData = [
			'email' => $data->getProperty('email'),
			'name' => $data->getProperty('first_name'),
			'password' => $data->getProperty('first_name'),
			'picture_path' => $data->getProperty('picture')->getProperty('url'),
			'facebook_id' => $data->getProperty('id'),
			'facebook_access_token' => (string) $token
		];	


		$users = \App\User::where('facebook_id',$userData['facebook_id'])->get();
		if($users->count() == 0)
		{
			$user = \App\User::create($userData);
		} else {
			$user = $users->first();
		}
		$jwtoken = \JWTAuth::fromUser($user);

		return response()->json(['success' => $jwtoken]);
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
			'email' => 'required|email|',
			'password' => 'required'
		]);

		if ( $validator -> fails() )
		{
			return response()->json(\App\Errors::invalid($validator));
		}
		//Catch the unhashed password for auth token
		$password = $params['password'];
		$params['password'] = \Hash::make($params['password']);
		$user = \App\Algorithm\ProSave::user($params);
		$output = \App\Algorithm\ProAuth::jwtAuth(array('email' => $user['email'], 'password' => $password));
		return response()->json([ 'user_id' => $user['user_id'],
								  'email' => $user['email'], 
								  $output['status'] => $output['message'] 
								], 200);
	}

	public function checkSocialAuth(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$user = \JWTAuth::parseToken()->toUser();
		if(! $user)
		{
			return \App\Errors::notFound("user not found");	
		}
		$data = [
			'id' => $user->user_id,
			'google_auth' => $user->isGoogleAuthed(),
			'facebook_auth' => $user->isFacebookAuthed($fb),
			'twitter_auth' => $user->isTwitterAuthed()
		];

		return response()->json($data, 200);

		
	}


}
