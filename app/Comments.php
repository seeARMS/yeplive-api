<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model {

	//
	protected $fillable = [
		'user_id',
		'yep_id',
		'comment',
		'created_at',
		'updated_at'
	];


	/*
	protected $hidden = [
		'id',
		'updated_at',
		'channel_id'
	];
	*/
}
