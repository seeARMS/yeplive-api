<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {

	//
	protected $fillable = [
		'sender_id',
		'channel_id',
		'display_name',
		'message',
		'timestamp'
	];


	/*
	protected $hidden = [
		'id',
		'updated_at',
		'channel_id'
	];
	*/
}
