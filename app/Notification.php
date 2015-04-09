<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {

	//
	protected $fillable = [
		'device_token',
		'user_id',
		'messsage'
	];

}
