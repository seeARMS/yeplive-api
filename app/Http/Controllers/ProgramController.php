<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;

class ProgramController extends Controller {
	public function __construct()
	{
	}


	//Mobile Methods 
	public function get_similar_videos(Request $request)
	{
		$program_id = $request->input("program_id");
		$user_id = $request->input("user_id");
		
		return 200;
	}

	public function get_tags(Request $request)
	{
		$program_id = $request->input("program_id");
		return 200;
	} 

	public function get_video_view_count(Request $request)
	{
		$program_id = $request->input("program_id");	
		return 200;
	}

	public function set_vote(Request $request)
	{
		$program_id = $request->input("program_id");
		$user_id = $request->input("user_id");
		$vote = $request->input("vote");
		return 200;
	}

	public function get_votes(Request $request)
	{
		$program_id = $request->input("program_id");
		return 200;
	}

	public function get_neg_votes(Request $request)
	{
		$program_id = $request->input("program_id");
		return 200;
	}
	
	public function get_user_vote(Request $request)
	{
		$program_id = $request->input("program_id");
		$user_id = $request->input("user_id");
		return 200;
	}

	public function report_video(Request $request)
	{
		$program_id = $request->input("program_id");
		$reporter = $request->input("reporter");
		$reported = $request->input("reported");
		$videoURL = $request->input("videoURL");
		$reason = $request->input("reason");
		return 200;
	}

	public function add_program(Request $request)
	{
		$channel_id = $request->input("channel_id");
		$user_id = $request->input("userid");
		$title = $request->input("title");
		$description = $request->input("description");
		$latitude = $request->input("latitude");
		$longitude = $request->input("longitude");
		$location = $request->input("location");
		$tags = $request->input("tags");
		$image_url = $request->input("imgurl");
		$vod_enable = $request->input("vod_enable");
		return 200;
	}

	public function update_program_info(Request $request)
	{
		$program_id = $request->input("program_id");
		$title = $request->input("title");
		$description = $request->input("description");
		$latitude = $request->input("latitude");
		$longitude = $request->input("longitude");
		$location = $request->input("location");
		$thumbnail_path = $request->input("thumbnail_path");
		$tags = $request->input("tags");
		return 200;
	}
}