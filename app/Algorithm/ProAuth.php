<?php namespace App\Algorithm;

class ProAuth {
	static function jwtAuth($credentials)
	{
		try {
			// attempt to verify the credentials and create a token for the user
			if (! $token = \JWTAuth::attempt($credentials)) {
				return array('status' => 'error', 'message' => 'invalid_credentials', 'code' => 401);
			}
		} catch (\JWTException $e) {
			// something went wrong while attempting to encode the token
			return array('status' => 'error', 'message' => 'could_not_create_token' ,'code' => 500);
		}
		// all good so return the token
		return array('status' => 'success', 'message' => compact('token') ,'code' => 200);
	}
}