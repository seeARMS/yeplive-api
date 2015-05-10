<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YepsController extends Controller {

	public function __construct()
	{
	}

	//Method called on all routes?
	public function all()
	{

	}


	//FOR LOADER.IO LOAD TESTING
	public function loader(Request $request){
		\Bugsnag::notifyError('ErrorType', 'Test Error');
		return 'loaderio-d9d729e1f9c98b37dcec64365e3dd5e3';
	}

	public function controlPanel()
	{
		return view('control-panel');
	}

	public function getYepPage(Request $request, $hash)
	{
		$id = \App\Algorithm\ProHash::toID($hash);

		$url = \Config::get('webclient.root').'/watch/'.$id;

		return redirect()->to($url);
	}


	public function showAll()
	{
		$yeps = \App\Yep::all();
		return $yeps;
	}

	public function getYepByHash(Request $request)
	{
		$params = $request -> only('hash');

		$validator = \Validator::make( $params, [
			'hash' => 'string'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		$id = \App\Algorithm\ProHash::toID($params['hash']);

		$yep = \App\Yep::find($id);

		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		return response()->json(['yep'=>$yep]);
	
	}

	public function index(Request $request)
	{
		$params = $request->only(
			'tags',
			'quantity'
		);

		$validator = \Validator::make( $params, [
			'tags' => 'string|max:255',
			'quantity' => 'integer'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		if($params['quantity'])
		{
			$yeps = \App\Yep::queryYeps($params);
			foreach($yeps as $yep){
				$tags = [];
				foreach($yep->tagsObj as $tag){
					array_push($tags, $tag->tag_name);
				}
				$yep->vote_count = $yep->votes->count();
				$yep->tags = $tags;
			}
		} else  {
			$yeps = \Cache::rememberForever('yeps', function() use ($params)
			{
				$params['quantity'] = 100000;
				$res = \App\Yep::queryYeps($params);
				foreach($res as $yep){
					$tags = [];
					foreach($yep->tagsObj as $tag){
						array_push($tags, $tag->tag_name);
					}
					$yep->tags = $tags;
					$yep->vote_count = $yep->upvotes();
				}
				return $res;
			});
		}


		return response()->json(['yeps' => $yeps], 200);
	}

	//POST /yeps
	public function store(Request $request)
	{
		$params = $request->only(
			'is_web',
			'channel_id',
			'title',
			'latitude',
			'longitude',
			'location',
			'description',
			'tags'
		);

	
		$validator = \Validator::make( $params, [
			'is_web' => 'boolean',
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


		/*
		$tags = explode(',',$params['tags']);

		if($tags[0] != '' )
		{
			foreach($tags as $tagName)
			{
				$yep->tag(strtolower($tagName));
			}
		}
		*/

		$yep->start_time = time();

		$yep->stream_name = $yep->id . "-" .  strval(time());

		$yep->is_custom_marker = 0;

		$yep->url_hash = \App\Algorithm\ProHash::toHash($yep->id);
	
		$yep->vod_enable = false;

		if(! $params['is_web'])
		{
			$yep->is_web = 0;
			$yep->upload_url = \Config::get('wowza.rtmp.upload_mobile').$yep->stream_name;
			//$yep->stream_url = \Config::get('wowza.android.rtsp').$yep->stream_name;
			$yep->stream_url = \Config::get('wowza.android.rtsp_backup').$yep->stream_name;
			$yep->stream_hls = \Config::get('wowza.android.rtmp_backup').$yep->stream_name;			
		} else {
			$yep->is_web = 1;
			$yep->stream_url = \Config::get('wowza.web.rtsp').$yep->stream_name;
			$yep->stream_hls = \Config::get('wowza.web.hls').$yep->stream_name.'/playlist.m3u8';
		}

		$yep->stream_mobile_url = '';

		if($request->has('staging')){
			$yep->staging = $request->input('staging');
		};

		$yep->setTagsFromTitle();

		$yep -> save();

		$shareUrl = \Config::get('webserver.alias').\App\Algorithm\ProHash::toHash($yep->id);


//		$response = \Event::fire(new \App\Events\YepCreated($yep));

//		event(new \App\Events\YepCreated($yep));

		try{	
		\Cache::forget('yeps');
		} catch(\Exception $e){
		}
		
		try{
			$success = \App\Algorithm\Socket::newYep($yep);
		} catch (\Exception $e){
		}

		return response()->json(['success' => 1,
			'id' => $yep->id,
			 'upload_url' => $yep->upload_url,
			 'stream_name' => $yep->stream_name,
			 'share_url' => $shareUrl
		]);
	}


	public function addVideoThumbnail(Request $request, $id)
	{
		$params = $request -> only ('portrait');

		$validator = \Validator::make( $params, [
			'portrait' => 'boolean'	
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}
	
		$user = \JWTAuth::parseToken()->toUser();

		if($user -> isBanned()){
			return \App\Errors::forbidden("user is banned");
		}

		$yep= \App\Yep::queryYep($id);

		if(!$yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$width = '427';
		$height = '240';
			
		if($yep -> is_web)
		{
			$application_name = 'hdfvr';
		} else {
			$application_name = 'liveorigin';
		}
		
		$stream_name = $yep -> stream_name;
		$yep_id = $yep -> id;

		if($yep -> is_web){
			$imagePath = \Config::get('wowza.thumbnail_web.host').'/transcoderthumbnail?application='.$application_name.'&streamname='.$stream_name.'&format=jpeg&size='.$width.'x'.$height;
		} else {	
			$imagePath = \Config::get('wowza.thumbnail.host').'/transcoderthumbnail?application='.$application_name.'&streamname='.$stream_name.'&format=jpeg&size='.$width.'x'.$height;
		}
	
		$fileName = $stream_name.'-thumbnail.jpg';

		$imageUrl = \App\Algorithm\ProS3::storeYepThumbnail($imagePath, $fileName);

		$yep -> image_path = $imageUrl;
/*
		if(! $params['portrait'])
		{
			$yep->portrait = false;
		} else {
			$yep -> portrait = $params['portrait'];
		}
*/
		$yep -> save();

		try{
			$success = \App\Algorithm\Socket::newYep($yep);
		} catch (\Exception $e){
		}

		return response()->json(['success' => 1, 'image_url' => $imageUrl, 'id' => $yep_id]);
	}


	//PUT /yep/{id}
	public function update(Request $request, $id)
	{
//		$yep= \App\Yep::find($id);
		$yep = \App\Yep::queryYep($id);

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
			'longitude',
			'staging',
			'portrait'
		]);

		$validator = \Validator::make( $params, [
			'title' => 'string|max:100',
			'description' => 'string|max:255',
			'tags' => 'string:max:255',
			'latitude' => 'numeric',
			'longitude' => 'numeric',
			'staging' => 'boolean',
			'portrait' => 'boolean'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		if($request->has('staging'))
		{
			$yep -> staging = $params['staging'];
		}
		
		if($request->has('title'))
		{
			$yep -> title = $params['title'];
			$yep -> setTagsFromTitle();
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
		if($request->has('portrait'))
		{
			$yep -> portrait = $params['portrait'];
		}

/*
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
		*/

		try{
			$success = \App\Algorithm\Socket::newYep($yep);
		} catch (\Exception $e){
		}

		try{	
		\Cache::forget('yeps');
		} catch(\Exception $e){
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
		$yep = \App\Yep::queryYep($id);

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

		try{ 
			\App\Algorithm\Socket::yepDelete($yep);
		} catch (\Exception $e){}

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

		try
		{
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
		}
		
		$yep['vote_count'] = $yep->upvotes();
		$yep['tags'] = $yep->tagNames();
		$yep['user'] = $yep->getUser();
		
		return $yep;
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
		
		try{ 
			\App\Algorithm\Socket::newView($yep);
		} catch (\Exception $e){}
	
		return response()->json(['success' => 1, 'id' => $yep->id, 'views'=>$yep->views],200);
	}

	//VOTE FOR A YEP
	public function vote(Request $request, $id)
	{
		$user = \JWTAuth::parseToken()->toUser();

		$yep = \App\Yep::queryYep($id);
		$yep['vote_count'] = $yep->upvotes();

		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$data = $yep->vote($user);

		try{
			\App\Algorithm\Socket::newVote($yep);
		} catch (\Exception $e){

		}

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
		$yep = \App\Yep::find($id);

		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		return $yep->tagNames();
	}

	public function streamComplete(Request $request, $id){
		$yep = \App\Yep::queryYep($id);

		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$user = \JWTAuth::parseToken()->toUser();

		if($user->user_id != $yep->user_id)
		{
			return \App\Errors::forbidden("you can't do that");	
		}

		self::completeYep($yep);
		
		return response()->json(["success" => 1, "id" => $yep->id], 200);		
	}

	public function forceComplete(Request $request, $id)
	{
		$key = $request->input('key');

		if($key != 'evaniscool')
		{
			return \App\Errors::notFound();
		}

		$yep = \App\Yep::find($id);

		if (! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		self::completeYep($yep);

		return response()->json(["success" => 1, "id" => $yep->id], 200);		

	}

	//USED BY LAMBDA
	public function getYepByName(Request $request)
	{
		$params = $request->only('name');

		$validator = \Validator::make( $params, [
			'name' => 'string',
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		$yep = \App\Yep::where('stream_name','=',$params['name'])->first();

		return response()->json(["yep"=>$yep]);
	}
	
	public function stageYep(Request $request, $id)
	{
		$yep = \App\Yep::find($id);

		$key = $request->input('key');

		if($key != 'evaniscool')
		{
			return \App\Errors::notFound();
		}

		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		if($request->has('staging')){
			$yep -> staging = $request->input('staging');
		} else {
			$yep -> staging = 1;
		}


		$yep->save();
	
		return response()->json(["success" => 1, "id" => $id]);
	}

	private function completeYep($yep)
	{
		$yep -> vod_enable = true;

		$yep -> vod_path = \Config::get('wowza.cloudfront.static').$yep->stream_name.".mp4";

		$yep -> vod_fallback = \Config::get('wowza.s3.static').$yep->stream_name.".mp4";

		$yep -> vod_mobile_path = \Config::get('wowza.cloudfront.static').$yep->stream_name."/playlist.m3u8";


		$yep -> end_time = time();

		$yep -> save();

		try{	
		\Cache::forget('yeps');
		} catch(\Exception $e){
		}

		try {
			$success = \App\Algorithm\Socket::yepComplete($yep);
		} catch (\Exception $e) {
		}

		return true;
	}

	private function sortYeps($yeps)
	{
		
	}
}
