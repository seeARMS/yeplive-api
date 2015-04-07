<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;

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
}
