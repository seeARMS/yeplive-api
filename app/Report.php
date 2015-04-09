<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {
	protected $filable = [
		'reported_id',	
		'reporter_id',
		'program_id',
		'reason'
	];

	//
	public user()
	{
		return $this->belongsTo('App\User', 'reported_id');
	}

}
