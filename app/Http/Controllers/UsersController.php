<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UsersController extends Controller {

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
		$user = \App\User::where('email', '=', $credentials['email'])->first(); 
		if($user)
		{
			$output = \App\Algorithm\ProAuth::jwtAuth($credentials);
			return response()->json([$output['status'] => $output['message']], $output['code']);
		}
		else
		{
			$password = $credentials['password'];
					$credentials['password'] = \Hash::make($credentials['password']);
					$user = \App\Algorithm\ProSave::user($credentials);
					$output = \App\Algorithm\ProAuth::jwtAuth(array('email' => $user['email'], 'password' => $password));
					return response()->json([ 'user_id' => $user['user_id'],
												'email' => $user['email'], 
												$output['status'] => $output['message'] 
											], 200);
		}
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
			$user -> yeps = \App\Yep::where('user_id', '=', $user->user_id)->get();	
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
	public function showAllUsers()
	{
		return \App\User::all();
	}

	public function getUser(Request $request, $id)
	{
		$user = \App\User::find($id);
		if(! $user)
		{
			return \App\Errors::notFound('yep not found');
		}
		$user -> yeps = \App\Yep::where('user_id', '=', $user->user_id)->get();	
		
		return $user;
	}


	/*
	 * POST : Take in social login tokens and return with token and user id
	 */
	public function socialAuth(Request $request, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
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
				return \Errors\invalid('facebook id mismatch');
			}	
			$user = \App\User::where($facebook_id, '=', $params['facebook_user_id'])->first();
			if(! $user)
			{
				$newUserParams = [

				];
				$user = \App\User::create($params);
			} else { 
				$user -> facebook_access_token = $params['facebook_access_token'];
				$user -> save();
			}

			$jwtoken = \JWTAuth::fromUser($user);

			return response()->json(['success' => 1, 'token' => $jwtoken]);	
		}
		//TWITTER - TODO
		else if($request->has('twitter_access_token') && $request->has('twitter_user_id'))
		{
			
		}
		//GOOGLE - TODO
		else if($request->has('google_access_token') && $request->has('google_user_id'))
		{

		}
		//400
		else
		{
			return \App\Errors::invalid('no access token provided');
		}
	}

	//This function will link existing users other social media accounts
	public function linkSoclai(Request $request)
	{

	}




	public function friends(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$user = \JWTAuth::parseToken()->toUser();
	  $fb->setDefaultAccessToken($user->facebook_access_token);
		$response = $fb->get('/me/friends');
		$loc = $response->getGraphObjectList();//getGraphObject();
		return $loc;
		return $response->getGraphObject();
	}


	public function setDisplayName(Request $request)
	{
			
	}

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
			return response()->json(\App\Errors::invalid($validator));
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
			'new_email' => 'required|email|unique:yeplive_users,email'
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
			'photo'
		);
		$validator = \Validator::make( $params, [
			'photo' => 'required|image'
		]);

		if ( $validator -> fails() )
		{
			return \App\Errors::invalid(null, $validator);
		}

		if (! $request->hasFile('photo'))
		{
			return \App\Errors::invalid('not found');
		}
		$file = $request->file('photo');
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
			'device_token'
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

	public function loginTwitter(Request $request)
	{
		return \Socialize::with('twitter')->redirect();
	}

	public function twitterCallback(Request $request)
	{
		$user = \Socialize::with('twitter')->user();
		$data = [
			'email' => $user->getEmail(),
			'id' => $user->getId(),
			'nickname' => $user->getNickName(),
			'name' => $user->getName(),
			'avatar' => $user->getAvatar()
		];	
		return $data;
	}

	public function loginGoogle(Request $request)
	{
		return \Socialize::with('google')->redirect();
	}

	public function googleCallback(Request $request)
	{
		$user = \Socialize::with('google')->user();
		$data = [
			'email' => $user->getEmail(),
			'id' => $user->getId(),
			'nickname' => $user->getNickName(),
			'name' => $user->getName(),
			'avatar' => $user->getAvatar()
		];	
		return $data;

	}

	/* * POST : Take in user signup credentials, and return with user signup credentials and auth token
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



}

