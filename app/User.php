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
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

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
		else if ($this->bannedUntil < \Carbon\Carbon::now())
		{
			return true;
		}
		return false;
	}

}
