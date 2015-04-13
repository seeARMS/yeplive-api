<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	//protected $table = 'yeplive_following';
	//protected $primaryKey = array('user_id', 'follower_id');

	protected $fillable = [
		'follower_id',
		'followee_id'
	];

	public function follower()
	{
		return $this->belongsTo('\App\User', 'follower_id');
	}

	public function followee()
	{
		return $this->belongsTo('\App\User', 'followee_id');
	}
}
