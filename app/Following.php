<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Following extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'yeplive_following';
	//protected $primaryKey = array('user_id', 'follower_id');
}