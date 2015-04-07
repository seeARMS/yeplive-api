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
		
	}

	public function vote($user)
	{
		if($user)
		{
			$vote = Vote::where('user_id',$user->id)
				->where('program_id', $this->id)->get(); 
			if(count($vote) != 0)
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
					'user_id' => $user->id,
					'program_id' => $this->id,
					'vote' => true
				];
				$currentVote = Vote::create($voteParams);
				return ['vote' => $currentVote->vote];
			}
		}	
	}

	public function my_vote($user)
	{
		return Vote::where('user_id',$user->id)
				->where('program_id', $this->id)->get(); 
	}
}
