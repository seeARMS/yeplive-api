<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model {
	protected $fillable = [
		'program_id',
		'user_id',
		'vote'
	];
	//

}
