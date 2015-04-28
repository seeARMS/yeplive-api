<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YepsController extends Controller {

	public function __construct()
	{
	}

	public function getYepPage(Request $request, $name)
	{
		$userAgent = $request->header('USER_AGENT');
		if (in_array($userAgent, [
  'facebookexternalhit/1.1 (+https://www.facebook.com/externalhit_uatext.php)',
  'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'
		])){
			$yep = \App\Yep::where('stream_name','=',$name)->get()->first();
			if($yep)
			{
			return view('yeps.meta',$yep);
			}
			return view('yeps.meta');
			
		}
		else
		{
			$yep = \App\Yep::where('stream_name','=',$name)->get()->first();
			if($yep)
			{
			return view('yeps.meta',$yep);
			}
			return view('yeps.meta');
			return redirect()->to('http://yeplive.com?yep='.$name);
		}
	}

	//GET /yeps
	public function index(Request $request)
	{
//\Debugbar::startMeasure('render','Time for rendering');
//\Debugbar::stopMeasure('render');
		return 'test';
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
		$yeps = [];
		$yeps = \App\Yep::queryYeps($params);
		$yeps->each(function($yep){
			$yep['vote_count'] = $yep->votes->count();
			$yep['tags'] = $yep->tagNames();
		});

/*
		foreach(\App\Yep::with('user','votes')->get() as $yep){
			$yep['votes'] = $yep->votes()->count();
			array_push($yeps, $yep);
		}
		return $yeps;
		$yeps = \App\Yep::queryYeps($params);
*/

/*		
		foreach($yeps->with('user', 'votes', 'tagNames') as $yep) {
            $yep['votes'] = $yep->votes()->count();
            $yep['tags'] = $yep->tagNames;
            $yep['user'] = $yep->user;
		
		 }
*/

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
			'tags'
		);

		\Log::info('REQUEST');
		\Log::info($params);
	
		$validator = \Validator::make( $params, [
			'channel_id' => 'integer',
			'title' => 'string|max:100',
			'latitude' => 'required|numeric',
			'longitude' => 'required|numeric',
			'description' => 'string|max:255',
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
		\Log::info($yep);

		//Create tags
		$tags = explode(',',$params['tags']);

		if($tags[0] != '' )
		{
			foreach($tags as $tagName)
			{
				$yep->tag(strtolower($tagName));
			}
		}

		$yep->start_time = time();

		$yep->stream_name = $yep->id . "-" .  strval(time());

		$yep->upload_url = \Config::get('wowza.rtmp.upload_mobile').$yep->stream_name;

		$yep->vod_enable = false;

		$yep->stream_url = \Config::get('wowza.rtmp.test').$yep->stream_name;
//		$yep->stream_mobile_url = \Config::get('wowza.rtmp.test').$yep->stream_name."/playlist.m3u8";
		$yep->stream_mobile_url = 'http://54.149.106.109:1935/hdfvr/'.$yep->stream_name."/playlist.m3u8";

		if($request->has('staging')){
			$yep->staging = true;
		};

		$yep -> save();

		return response()->json(['success' => 1, 'id' => $yep->id, 'upload_url' => $yep->upload_url]);
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
			'latitude',
			'longitude'
		]);

		$validator = \Validator::make( $params, [
			'title' => 'string|max:100',
			'description' => 'string|max:255',
			'tags' => 'string:max:255',
			'latitude' => 'numeric',
			'longitude' => 'numeric'	
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		
		if($request->has('title'))
		{
			$yep -> title = $params['title'];
		}

		if($request->has('description'))
		{
			$yep -> description = $params['description'];
		}

		if($request->has('latitude'))
		{
			$yep -> latitude = $params['latitude'];
		}
	
		if($request->has('longitude'))
		{
			$yep -> longitude = $params['longitude'];
		}


		//Create tags
		$tags = $params['tags'] || '';
		$tags = explode(',', $params['tags']);

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

	public function delete(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		if(! $yep)
		{
			\App\Errors::notFound();	
		}

		$user = \JWTAuth::parseToken()->toUser();

		if($user->user_id != $yep->user_id)
		{
			return \App\Errors::forbidden("you can't do that");	
		}
		$yep->delete();

		return response()->json(['success'=>1, 'id'=>$id], 200);
	}

	public function unstage(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$user = \JWTAuth::parseToken()->toUser();
		if($user->user_id != $yep->user_id)
		{
			return \App\Errors::forbidden("you can't do that");	
		}
		$yep->staging = false;
		$yep->save();

		return response()->json(['success'=>1, 'id'=>$id], 200);

	}

	//GET /yep/{id}
	public function show(Request $request, $id)
	{
		$yep = \App\Yep::find($id);
		
		if (! $yep )
		{
			return \App\Errors::notFound('yep not found');
		}
		try{
			$user = \JWTAuth::parseToken()->toUser();
if($user)
		{
			$vote = $user->voted($yep);
			if($vote && $vote->vote == 1){
				$yep->voted = 1;
			} else {
				$yep->voted = 0;
			}
		} else {
				$yep->voted = 0;
		}
		} catch(\Exception $e){
				$yep->voted = 0;
				dd($yep);
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

	public function streamComplete(Request $request, $id){
		$yep = \App\Yep::find($id);
		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		$user = \JWTAuth::parseToken()->toUser();
		if($user->user_id != $yep->user_id)
		{
			return \App\Errors::forbidden("you can't do that");	
		}

		$yep -> vod_enable = true;
//		$yep -> vod_path = "rtmp://54.149.106.109/vods3/_definst_/&mp4:amazons3/dev-wowza/".$yep->stream_name.".mp4";
//		$yep->vod_mobile_path = "http://54.149.106.109:1935/vods3/_definst_/amazons3/dev-wowza/".$yep->stream_name."/playlist.m3u8";
		$yep -> vod_path = \Config::get('wowza.cloudfront.static').$yep->stream_name.".mp4";
		$yep -> vod_mobile_path = \Config::get('wowza.rtmp.vod').$yep->stream_name."/playlist.m3u8";
		$yep -> end_time = time();

		$yep -> save();

		return response()->json(["success" => 1, "id" => $yep->id], 200);
		
	}
}
