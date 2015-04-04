<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UserController extends Controller {
	public function __construct()
	{
	}

	//MOBILE ALIAS METHODS
	public function become_fan(Request $request)
	{
		$user_id = $request->input("user_id");
		$logged_in_id = $request->input("loginUserId");
		return 200;
	}

	public function is_follow(Request $request)
	{
		$user_id = $request->input("user_id");
		$pan_id = $request->input("pan_id");
		return 200;
	}

	public function upload_thumbnail(Request $request)
	{
			
		return 200;
	}

	public function update_user_pic(Request $request)
	{
		$user_id = $request->input("user_id");
		$user_pic = $request->input("userpic");
		return 200;
	}

	public function update_user_settings(Request $request)
	{
		$chat_response = $request->input("chat_response_notifications");
		return 200;
	}	
}
