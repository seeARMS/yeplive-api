<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Yep extends Model{
	use \Conner\Tagging\TaggableTrait;
	use SoftDeletes;

	protected $fillable = [
		'channel_id',
		'title',
		'latitude',
		'longitude',
		'location',
		'description',
		'user_id'
	];

	protected $hidden = [
		'updated_at',
		'isMobile',
		'user_id',
		'upload_url',
		'votes',
		'tagsObj',
		'tags_obj',
		'description',
		'channel_id',
		'location',
		'marker_url'
	];

	 protected $dates = ['deleted_at'];

	protected $attributes = [
		'views' => 0,
		'staging' => false
	];

	public function setTagsFromTitle()
	{
		$title = $this->title;
		preg_match_all('/#([^\s]+)/', $title, $matches);
		foreach($matches[1] as $tag)
		{
			$this->tag(strtolower($tag));
		}
	}
	
	//
	public function user()
	{
		return $this->belongsTo('App\User', 'user_id');
	}

	public function getUser()
	{
		return \App\User::find($this->user_id);
	}

	public function votes()
	{
		return $this->hasMany('App\Vote');
	}

	public function upvotes()
	{
		return \App\Vote::where('yep_id','=',$this->id)
			->where('vote','=','1')
			->count();
	}

	public function tagsObj()
	{
		return $this->morphMany('Conner\Tagging\Tagged','taggable');
	}

	//find yeps based on similar tags
	public function getSimilar()
	{
		$tags = $this->tagNames();
		return $this::withAnyTag($tags);
	}

	public function report($reporter, $reason)
	{
		$reported = \App\User::find($this->user_id);
		$data = [
			'reason' => $reason,
			'reporter_id' => $reporter->user_id,
			'reported_id' => $reported->user_id,
			'yep_id' => $this->id	
		];
		$report = \App\Report::create($data);

		return ['success' => $report];
		
	}
//ALLOW UP TO 5 VOTES
	public function vote($user)
	{
		if($user)
		{
			$vote_count = $user->votes
				->where('yep_id',$this->id)
				->count();

			if($vote_count < 5){
				$voteParams = [
					'yep_id' => $this->id,
					'vote' => 1,
					'user_id' => $user->user_id
				];
				$vote = Vote::create($voteParams);
				return ['id' => $this->id, 'success'=>1, 'vote_count' => $this->upvotes()];
			} else {
				return ['id' => $this->id, 'success'=>0, 'vote_count' => $this->upvotes()];
			}
		}

		/* OLD CODE
		if($user)
		{
			$vote = Vote::where('user_id',$user->user_id)
				->where('yep_id', $this->id)->get(); 
			if($vote->count() != 0)
			{
				$currentVote = $vote->last();
				if($currentVote -> vote == 1)
				{
					$currentVote -> vote = 0;
					$currentVote -> save();
					return ['id' => $this->id, 'success' => 1,'vote' => $currentVote->vote];
				}
				else
				{
					$currentVote -> vote = 1;
					$currentVote -> save();
					return ['id' => $this->id, 'success' => 1,'vote' => $currentVote->vote];
				}
			}
			else
			{
				$voteParams = [
					'user_id' => $user->user_id,
					'yep_id' => $this->id,
					'vote' => 1 
				];
				$currentVote = Vote::create($voteParams);
				return ['id' => $this->id, 'success' => 1,'vote' => $currentVote->vote];
			}
		} else{
			return [ 'error' => 'no user specified'];
		}	
		*/
	}

	public function my_vote($user)
	{	
		return Vote::where('user_id',$user->user_id)
			->where('yep_id', $this->id)->get(); 
	}

	public static function queryYep($id)
	{
		$yep = self::with('user','tagsObj')
			->find($id);
		if(! $yep)
		{
			return null;
		}
		$tags = [];
		foreach($yep->tagsObj as $tag){
			array_push($tags, $tag->tag_name);
		}
		return $yep;
	}

	public static function queryYeps($params)
	{	
		$quantity = $params['quantity'] != null ? $params['quantity'] : 10;

		$timeAgo = \Carbon\Carbon::now()->subDay();

		$yeps = self::with('user','tagsObj','votes')
			->where('staging','=',0)
			->where('created_at','>',$timeAgo)
			->orderBy('vod_enable','asc')
			->orderBy('views','desc')
			->orderBy('created_at','desc')
			->take($quantity)
			->get();

		return $yeps;
		

		$tags = explode(',',$params['tags']);
		if($tags[0] == '')
		{
		}
		return Yep::withAnyTag($tags)->limit($quantity)->where('staging', '=', 0)->get();
	}

	public static function queryLiveYeps($params)
	{
		$quantity = $params['quantity'] != null ? $params['quantity'] : 1000;

		$timeAgo = \Carbon\Carbon::now()->subDay();

		$yeps = self::with('user','tagsObj','votes')
			->where('staging','=',0)
			->where('created_at','>',$timeAgo)
			->where('vod_enable', 0)
			->orderBy('views','desc')
			->orderBy('created_at','desc')
			->take($quantity)
			->get();
	}


	public static function sortingAlgorithm($yeps)
	{
		$yeps->sort(function($a, $b){
			
		});
		return $yeps;
	}	
}
