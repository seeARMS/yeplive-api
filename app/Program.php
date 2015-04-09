<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model{
	use \Conner\Tagging\TaggableTrait;
	protected $fillable = [
		'channel_id',
		'title',
		'latitude',
		'longitude',
		'location',
		'description',
		'start_time',
		'end_time'
	];

	protected $attributes = [
		'views' => 0
	];
	
	//
	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function votes()
	{
		return $this->hasMany('App\Vote');
	}

	//find programs based on similar tags
	public function getSimilar()
	{
		$tags = $this->tagNames();
		return $this::withAnyTag($tags);
	}

	public function report($reporter, $reason)
	{
		$reported = \App\User::find($this->user_id);
		return $this;
		return $reported;
		$data = [
			'reason' => $reason,
			'reporter_id' => $reporter->user_id,
			'reported_id' => $reported->user_id,
			'program_id' => $this->id	
		];
		$report = \App\Report::create($data);

		return ['success' => $report];
		
	}

	public function vote($user)
	{
		if($user)
		{
			$vote = Vote::where('user_id',$user->user_id)
				->where('program_id', $this->id)->get(); 
			if($vote->count() != 0)
			{
				$currentVote = $vote->last();
				if($currentVote -> vote == 1)
				{
					$currentVote -> vote = 0;
					$currentVote -> save();
					return ['vote' => $currentVote -> vote];
				}
				else
				{
					$currentVote -> vote = 1;
					$currentVote -> save();
					return ['vote' => $currentVote -> vote];	
				}
			}
			else
			{
				$voteParams = [
					'user_id' => $user->user_id,
					'program_id' => $this->id,
					'vote' => true
				];
				$currentVote = Vote::create($voteParams);
				return ['vote' => $currentVote->vote];
			}
		} else{
			return [ 'error' => 'no user specified'];
		}	
	}

	public function my_vote($user)
	{	
		return Vote::where('user_id',$user->user_id)
			->where('program_id', $this->id)->get(); 
	}
}
