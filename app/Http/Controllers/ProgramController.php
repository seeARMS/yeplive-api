<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramController extends Controller {

	public function __construct()
	{
	}

	public function index()
	{
		//try
		//{
			$body = [
			'programs' => []
			];
			$statusCode = 200;
			$programs = \App\Program::all();//->take(10);
			$displayName = 'anonymous';
			foreach($programs as $program)
			{
			if($program->user_id)
			{
				$displayName= \App\User::find($program->user_id)->display_name;
			}
				$body['programs'][] = [
					'id' => $program->id,
					'title' => $program->title,
					'description' => $program->description,
					'latitude' => $program->latitude,
					'longitude' => $program->longitude,
					'location' => $program->location,
					'displayName' => $displayName
				];
			}
		//} catch (Exception $e)
	//	{
	//		$statusCode = 404;
	//	}
	//	 finally
	 // {
			return $body;
	//	}
	}

	public function store(Request $request)
	{
		$input = $request->only(
		'channel_id',
		'title',
		'latitude',
		'longitude',
		'location',
		'description',
		'end_time',
		'start_time', 
		'tags'
		);

		return $input;

		$user = JWTAuth::parseToken()->toUser();

		if($user -> isBanned){
			return "banned";	
		}

		$input['user_id'] = $user -> id;

		$program = \App\Program::create($input);

		//Create tags
		$tags = explode(',',$input['tags']);

		if($tags[0] != '' )
		{
			foreach($tags as $tagName)
			{
				$program->tag($tagName);
			}
		}
		return $program;	
	}

	//PUT /program/{id}
	public function update(Request $request, $id)
	{
		
	}

	public function show(Request $request, $id)
	{
		return \App\Program::find($id);
	}

	//Method called on all routes?
	public function all()
	{

	}


	public function similar(Request $request, $id)
	{
		$program = \App\Program::find($id);
		$tags = $program->tagNames();
		return \App\Program::withAnyTag($tags)->get()->filter(function ($currentProgram) use ($program)
		{
			return $currentProgram->id != $program->id;
		})->values();
	}

	public function tags(Request $request, $id)
	{
		return \App\Program::find($id)->tagNames();
	}

	public function get_videos(Request $request)
	{
		$programs = Program::all();
		return $programs;
	}

	public function get_view_count(Request $request, $id)
	{
		return \App\Program::find($id)->views;
	}

	public function vote(Request $request, $id)
	{
		$user = \JWTAuth::parseToken()->toUser();
		$data = \App\Program::find($id)->vote($user);
		return $data;
	}

	public function votes(Request $request, $id)
	{
		$program = \App\Program::find($id);
		return ['votes' => $program->votes->where('vote',1)->count()];
	}

	public function userVote(Request $request, $id)
	{
		$program = \App\Program::find($id);
		$user = \JWTAuth::parseToken()->toUser();
		$vote = $program->my_vote($user)->first();
		return ['vote' => $vote -> vote];
	}

	public function report(Request $request, $id)
	{
		$program = \App\Program::find($id);
		$reason = $request->input("reason");
		$user = JWTAuth::parseToken()->toUser();
		return $program->report($user, $reason);	
	}

	
}
