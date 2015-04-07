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
		$output = ProAuth::jwtAuth($credentials);
		return response()->json([$output['status'] => $output['message']], $output['code']);
		/*
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
		*/
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

	public function signup(Request $request)
	{
		$params = $request->only("email", "password");

		$validator = \Validator::make( $params, [
			'email' => 'required|email|unique:yeplive_users,email',
			'password' => 'required'
		]);

		if ( $validator -> fails() )
		{
			return response()-> json(['error' => 'invalid_input'], 400);
		}

		
		$params["password"] = \Hash::make($params["password"]);
		
		$ProSave = new \App\Algorithm\ProSave;
		$ProAuth = new \App\Algorithm\ProAuth;

		$user = $ProSave::user($params);
		$output = $ProAuth::jwtAuth(array('email' => $user['email'], 'password' => $user['password']));
		return response()->json([ 'user_id' => $user['user_id'], 
								  'email' => $user['email'], 
								  'password' => $user['password'], 
								  $output['status'] => $output['message'] 
								], 200);
	}
}
