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

}
