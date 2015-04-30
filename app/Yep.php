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
		'staging',
		'upload_url',
		'votes'
	];

	 protected $dates = ['deleted_at'];

	protected $attributes = [
		'views' => 0,
		'staging' => false
	];
	
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

	public function tags()
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

	public function vote($user)
	{
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
	}

	public function my_vote($user)
	{	
		return Vote::where('user_id',$user->user_id)
			->where('yep_id', $this->id)->get(); 
	}

	static function queryYeps($params)
	{	
		$quantity = $params['quantity'] != null ? $params['quantity'] : 10;
		$tags = explode(',',$params['tags']);
		if($tags[0] == '')
		{
			return Yep::with('user')->orderBy('vod_enable','asc')->orderBy('created_at','desc')->take($quantity)->get();
		}
		return Yep::withAnyTag($tags)->limit($quantity)->where('staging', '=', 0)->get();
	}
}
