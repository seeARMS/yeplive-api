<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramController extends Controller {

	public function __construct()
	{
	}

	public function index(Request $request)
	{
		$programs = \App\Program::all();
		return response()->json(['programs' => $programs], 200);
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
			return \App\Errors::invalid(null, $validator);
		}

		$user = \JWTAuth::parseToken()->toUser();
		if($user -> isBanned()){
			return \App\Errors::forbidden("user is banned");
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
			return \App\Errors::forbidden("token mismatch");
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
			return \App\Errors::invalid(null, $validator);
		}

		$program -> fill ($params);

		$program -> save();

		return response()->json(['program' => $program]);
	}

	public function show(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if (! $program)
		{
			return \App\Errors::notFound('program not found');
		}
		return $program;
	}

	//Method called on all routes?
	public function all()
	{

	}


	public function similar(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if (! $program)
		{
			return \App\Errors::notFound('program not found');
		}
		$tags = $program->tagNames();
		return \App\Program::withAnyTag($tags)->get()->filter(function ($currentProgram) use ($program)
		{
			return $currentProgram->id != $program->id;
		})->values();
	}

	public function tags(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if (! $program)
		{
			return \App\Errors::notFound('program not found');
		}
		return $program->tagNames();
	}

	public function viewCount(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if (! $program)
		{
			return \App\Errors::notFound('program not found');
		}
		$views = $program->views;

		return response()->json(['views'=>$views],200);
	}

	public function incrementViews(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if(! $program)
		{
			return \App\Errors::notFound("program not found");
		}
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
			return \App\Errors::notFound('program not found');
		}
		$data = $program->vote($user);
		return response()->json($data, 200);
	}

	public function votes(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if(! $program)
		{
			return \App\Errors::notFound('program not found');
		}
		return response()->json(['votes' => $program->votes->where('vote',1)->count()], 200);
	}

	public function userVote(Request $request, $id)
	{
		$program = \App\Program::find($id);
		if(! $program)
		{
			return \App\Errors::notFound('program not found');
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
			return \App\Errors::invalid(null, $validator);
		}

		$program = \App\Program::find($id);
		if(! $program)
		{
			return \App\Errors::notFound('program not found');
		}

		$reporter = \JWTAuth::parseToken()->toUser();
		$data = $program->report($reporter, $params['reason']);	
		return response()->json($data, 200);
	}

	
}
