<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model {


	public $timestamps = false;
	//
	protected $fillable = [
		'user_id',
		'yep_id',
		'comment',
		'created_time',
		'updated_time'
	];




	/*
	protected $hidden = [
		'id',
		'updated_at',
		'channel_id'
	];
	*/
}
