<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {

	//
	protected $fillable = [
		'message',
		'user_id',
		'channel_id'
	];

	protected $hidden = [
		'id',
		'updated_at',
		'channel_id'
	];

}
