<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramController extends Controller {

	public function __construct()
	{
	}

	public function index(Request $request)
	{
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
			return $body;
	}

	public function store(Request $request)
	{
		$params = $request->only(
//		'channel_id',
		'title',
		'latitude',
		'longitude'
//		'location',
//		'description',
//		'end_time',
//		'start_time', 
		,'tags'
		);
	
		$validator = \Validator::make( $params, [
		//	'channel_id' => 'required',
			'title' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
		//disable for mobile
		//	'location' => 'required',
		//	'start_time' => 'required',
		//	'end_time' => 'required'
		]);

		if($validator -> fails())
		{
			return response()->json(\App\Errors::invalid($validator), 400);
		}

		$user = \JWTAuth::parseToken()->toUser();
		if($user -> isBanned()){
			return response()->json(\App\Errors::unauthorized(null, ['user is banned']), 401);	
		}

		$params['user_id'] = $user -> id;
		

		$program = \App\Program::create($params);

		//Create tags
		$tags = explode(',',$params['tags']);

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
		$program = \App\Program::find($id);
		$user = \JWTAuth::parseToken()->toUser();
		
		if($program -> user_id != $user ->id)
		{
			return response()->json(\App\Errors::unauthorized(null, ['token mismatch error']));
		}

		$params = $request->only([
			'title',
			'description',
			'tags',
			
		]);

		$validator = \Validator::make( $params, [
			'channel_id' => 'required',
			'title' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'loaction' => 'required'
		]);

		if($validator -> fails())
		{
			return response()->json(\App\Errors::invalid($validator), 400);
		}

		$program -> fill ($params);

		$program -> save();

		return response()->json(['program' => $program]);
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

	public function viewCount(Request $request, $id)
	{
		$views = \App\Program::find($id)->views;
		return response()->json(['views'=>$views],200);
	}

	public function incrementViews(Request $request, $id)
	{
		$program = \App\Program::find($id);
		$program -> views = $program->views + 1;
		$program -> save();
		return response()->json(['views' => $program->views], 200);
	}

	public function vote(Request $request, $id)
	{
		$user = \JWTAuth::parseToken()->toUser();
		$program= \App\Program::find($id);
		if(! $program)
		{
			return response()->json(['stauts_code' => '404'], 404);	
		}
		$data = $program->vote($user);
		return response()->json($data, 200);
	}

	public function votes(Request $request, $id)
	{
		$program = \App\Program::find($id);
		return response()->json(['votes' => $program->votes->where('vote',1)->count()], 200);
	}

	public function userVote(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if(! $program)
		{
			return response()->json(['stauts_code' => '404'], 404);	
		}
		$user = \JWTAuth::parseToken()->toUser();
		$vote = $program->my_vote($user);
		if($vote->count() == 0){
			return response()->json(['vote' => 0], 200);
		}		
		return response()->json(['vote' => $vote -> first() -> vote], 200);
	}

	public function report(Request $request, $id)
	{
		$params = $request->only([
			'reason'
		]);
		
		$validator = \Validator::make( $params, [
			'reason' => 'required'
		]);

		if($validator -> fails())
		{
			return response()->json(\App\Errors::invalid($validator), 400);
		}

		$program = \App\Program::find($id);
		$reporter = \JWTAuth::parseToken()->toUser();
		$data = $program->report($reporter, $params['reason']);	
		return response()->json($data, 200);
	}

	
}
