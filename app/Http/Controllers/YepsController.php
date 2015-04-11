<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YepsController extends Controller {

	public function __construct()
	{
	}

	//GET /yeps
	public function index(Request $request)
	{
		$params = $request->only(
			'tags',
			'quantity'
		);
		$validator = \Validator::make( $params, [
			'tags' => 'string',
			'quantity' => 'integer'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		$yeps = \App\Yep::queryYeps($params);

		foreach($yeps as $yep)
		{
			$yep['votes'] = $yep->votes()->count();
			$yep['tags'] = $yep->tagNames();
			$yep['user'] = $yep->getUser();
		}
		return response()->json(['yeps' => $yeps], 200);
	}

	//POST /yeps
	public function store(Request $request)
	{
		$params = $request->only(
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
	
		$validator = \Validator::make( $params, [
			'channel_id' => 'integer',
			'title' => 'required|string|max:100',
			'latitude' => 'required|numeric',
			'longitude' => 'required|numeric',
			'description' => 'string|max:255',
			'start_time' => 'string|max:255',
			'end_time' => 'string|max:255'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}
		$user = \JWTAuth::parseToken()->toUser();

		if($user -> isBanned()){
			return \App\Errors::forbidden("user is banned");
		}

		$params['user_id'] = $user -> user_id;

		$filtered_params = array_filter($params, 'strlen');

		$yep= \App\Yep::create($filtered_params);

		//Create tags
		$tags = explode(',',$params['tags']);

		if($tags[0] != '' )
		{
			foreach($tags as $tagName)
			{
				$yep->tag(strtolower($tagName));
			}
		}
		return response()->json(['success' => 1, 'id' => $yep->id]);
	}

	//PUT /yep/{id}
	public function update(Request $request, $id)
	{
		$yep= \App\Yep::find($id);
		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$user = \JWTAuth::parseToken()->toUser();
		
		if($yep -> user_id != $user ->user_id)
		{
			return \App\Errors::forbidden("token mismatch");
		}

		$params = $request->only([
			'title',
			'description',
			'tags',
			'start_time',
			'end_time',
			'tags'	
		]);

		$validator = \Validator::make( $params, [
			'channel_id' => 'integer',
			'title' => 'string|max:100',
			'description' => 'string|max:255',
			'start_time' => 'string|max:255',
			'end_time' => 'string|max:255',
			'tags' => 'string:max:255'	
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		$yep -> fill ($params);

		//Create tags
		$tags = $params['tags'] || '';
		$tags = explode(',',$params['tags']);

		if($tags[0] != '' )
		{
			$yep->untag();
			foreach($tags as $tagName)
			{
				$yep->tag(strtolower($tagName));
			}
		}

		if($yep -> save())
		{
			return response()->json(['success' => 1, 'id' => $yep->id]);
		}
		else
		{
			return response()->json(['success' => 0, 'id' => $yep->id]);
		}
	}

	//GET /yep/{id}
	public function show(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		
		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$user = \JWTAuth::parseToken()->toUser();
		if($user)
		{
			if($user->voted($yep)){
				$yep['voted'] = 1;
			} else {
				$yep['voted'] = 0;
			}
		} else {
				$yep['voted'] = 0;
		}

		$yep['votes'] = $yep->votes()->count();
		$yep['tags'] = $yep->tagNames();
		$yep['user'] = $yep->getUser();
		
		return $yep;
	}

	//Method called on all routes?
	public function all()
	{

	}

	//GET /yep/{id}/similar
	public function similar(Request $request, $id)
	{
		$params = $request->only(
			'quantity'
		);

		$validator = \Validator::make( $params, [
			'quantity' => 'integer'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		if ($params['quantity'])
		{
			$quantity = $params['quantity'];
		} else 
		{
			$quantity = 10;
		}

		$yep= \App\Yep::find($id);
		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$tags = $yep->tagNames();

		$yeps = \App\Yep::withAnyTag($tags)
			->get()
			->take($quantity)
			->filter(function ($currentYep) use ($yep)
				{
					return $currentYep->id != $yep->id;
				})
			->values();

		return response()->json(['yeps' => $yeps]);
	}

	

	//INCREMENT VIEWS ON A YEP
	public function incrementViews(Request $request, $id)
	{
		$yep= \App\Yep::find($id);
		if(! $yep)
		{
			return \App\Errors::notFound("yep not found");
		}
		$yep -> views = $yep ->views + 1;
		$yep -> save();
		return response()->json(['success' => 1, 'id' => $yep->id, 'views'=>$yep->views],200);
		return response()->json(['views' => $yep ->views], 200);
	}

	//VOTE FOR A YEP
	public function vote(Request $request, $id)
	{
		$user = \JWTAuth::parseToken()->toUser();
		$yep = \App\Yep::find($id);
		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$data = $yep->vote($user);
		return response()->json($data, 200);
	}

	//GET THE USERS VOTE ON A YEP	
	public function userVote(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$user = \JWTAuth::parseToken()->toUser();
		$vote = $yep->my_vote($user);
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

		$yep = \App\Yep::find($id);
		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$reporter = \JWTAuth::parseToken()->toUser();
		$data = $yep->report($reporter, $params['reason']);	
		return response()->json(['success' => 1, 'id' => $id]);
	}

	//unneeded methods
	public function votes(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		return response()->json(['votes' => $yep->votes->where('vote',1)->count()], 200);
	}
	public function viewCount(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$views = $yep->views;

		return response()->json(['success' => 1, 'id' => $yep->id, 'views'=>$views],200);
	}
	public function tags(Request $request, $id)
	{
		$yep= \App\Yep::find($id);
		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		return $yep->tagNames();
	}


}
