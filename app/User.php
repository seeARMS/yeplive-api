<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'yeplive_users';
	protected $primaryKey = 'user_id';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password', 'facebook_id', 'facebook_access_token','picture_path' ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password',
		'remember_token',
		'email',
		'name',
		'activation_key',
		'url',
		'facebook_id',
		'facebook_email',
		'facebook_access_token',
		'facebook_picture',
		'twitter_id',
		'twitter_name',	
		'twitter_oauth_token',
		'twitter_oauth_token_secret',
		'facebook_friends',
		'google_name',
		'google_access_token',
		'google_email',
		'google_picture',
		'created_at',
		'updated_at',
		'push_notifications',
		'device_token',
		'bannedPermanently',
		'bannedUntil'
	];


	protected $attributes = [
		'push_notifications' => 1
	];

	public function settings()
	{
		return $this->hasOne('App\Setting');
	}

	public function reports()
	{
		return $this->hasMany('App\Report');
	}

	public function votes()
	{
		return $this->hasMany('App\Vote');
	}

	public function voted($yep)
	{
		return Vote::where('user_id',$user->user_id)
			->where('yep_id', $yep->id)->get(); 
	}


	public function programs()
	{
		return $this->hasMany('App\Program');
	}

	//check if user is banned
	public function isBanned()
	{
		if($this->bannedPermanently)
		{
			return true;
		}
		else if ($this->bannedUntil > \Carbon\Carbon::now())
		{
			return true;
		}
		return false;
	}

}
