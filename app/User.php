<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract{

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
	protected $fillable = [
		'name', 
		'email', 
		'password', 
		'facebook_id', 
		'facebook_access_token',
		'picture_path', 
		'display_name', 
		'twitter_oauth_token',
		'twitter_oauth_token_secret',
		'twitter_id',
		'google_id',
		'google_access_token'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password',
		'yeps',
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
		'google_id',
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

	public function followerCount()
	{
		return \App\Follower::where('followee_id', '=', $this->user_id)->count();
	}

	public function followingCount()
	{
		return \App\Follower::where('follower_id', '=', $this->user_id)->count();
	}

	public function followers()
	{
		$followers = \App\Follower::where('followee_id', '=', $this->user_id)->get();
		$users = [];
		foreach ($followers as $follower)
		{
			array_push($users, \App\User::find($follower->follower_id));
		}
		return $users;
//		return $this->hasManyThrough('App\User','App\Follower','followee_id', 'follower_id');
	}

	public function following()
	{
		$following = \App\Follower::where('follower_id', '=', $this->user_id)->get();
		$users = [];
		foreach ($following as $follower)
		{
			array_push($users, \App\User::find($follower->followee_id));
		}
		return $users;	
		//return $this->hasManyThrough('App\User','App\Follower','followee_id', 'user_id');
	}

	public function voted($yep)
	{
		return Vote::where('user_id',$this->user_id)
			->where('yep_id', $yep->id)->get()->first(); 
	}


	public function yeps()
	{
		return $this->hasMany('App\Yep');
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

	public function isGoogleAuthed()
	{
		$token = $this->google_access_token;
		$id = $this->google_id;
		if($token && $id)
		{
			$client = new \Google_Client();
			$access_token_json = json_encode([
				"access_token" =>$token,
				"token_type"=>"Bearer",
				"expires_in"=>3600,
				"id_token"=>$token,
				"created"=>time(),
				"refresh_token"=>''
			]);
			$client->setAccessToken($access_token_json);
			$plus = new \Google_Service_Plus($client);
			try {
				$person = $plus->people->get('me');
			} catch(Exception $e){
				return false;
				dd($e);
			}
			$profileID = $person->id;
			if($id != $profileID)
			{
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function shareGoogle()
	{

	}

	public function getGoogleFriends()
	{
		if(! $this->isGoogleAuthed()){
			return false;
		}

		$token = $this->google_access_token;
		$id = $this->google_id;
		if($token && $id)
		{
			$client = new \Google_Client();
			$access_token_json = json_encode([
				"access_token" =>$token,
				"token_type"=>"Bearer",
				"expires_in"=>3600,
				"id_token"=>$token,
				"created"=>time(),
				"refresh_token"=>''
			]);
			$client->setAccessToken($access_token_json);
			$plus = new \Google_Service_Plus($client);
			try {
				//$friends = $plus->people->listPeople('me','connected');
				$friends = $plus->people->listPeople('me','visible');
			} catch(Exception $e){
				return false;
				dd($e);
			}
			return $friends->getItmes();
			$googleFriends = [];
		} else {
			return false;
		}

	}

	public function isFacebookAuthed(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		$token = $this->facebook_access_token;
		$id = $this->facebook_id;
	
		if($token && $id)
		{
			$fb->setDefaultAccessToken($token);
			try {
				$response = $fb->get('/me?fields=id,email,first_name,picture.type(large)');
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				return false;
			}
			$data = $response->getGraphObject();
			$facebookId = $data->getProperty('id');
			if($facebookId != $id)
			{
				return false;
			}	
			return true;
		}
		else 
		{
			return false;
		}
	}

	//TODO
	public function shareFacebook($fb)
	{
		$fb->setDefaultAccessToken($this->facebook_access_token);
			try {
				$response = $fb->post('/me/feed', [
					"message" =>time(),
					"link" => "http://development-vriepmhkv2.elasticbeanstalk.com/api"
				]);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				return false;
			}
			$data = $response->getGraphObject();
			return true;
	}

	public function getFacebookFriends($fb)
	{
		$fb->setDefaultAccessToken($this->facebook_access_token);

		try {
			$response = $fb->get('/me/friends');
		} catch (Facebook\Exceptions\FacebookSDKException $e) {
				return \Errors\invalid('invalid facebook token');
				dd($e->getMessage());
			}
		$data = $response->getGraphList()->AsArray();
		$users = [];
		foreach($data as $friend)
		{
			$userFriend = \App\User::where('facebook_id', '=', $friend["id"])->get()->first();
			if($userFriend)
			{
				array_push($users, $userFriend);
			}
		}
		return $users;

	}

	public function isTwitterAuthed()
	{
		$token = $this->twitter_oauth_token;
		$secret = $this->twitter_oauth_token_secret;
		$id = $this->twitter_id;
		if($token && $secret && $id)
		{
			$twitter_token = [
				'token' => $this->twitter_oauth_token,
				'secret' => $this->twitter_oauth_token_secret 
			];
			\Twitter::reconfig($twitter_token);
			$credentials = \Twitter::getCredentials();
			if($id != $credentials->id){
				return false;
			}
			return true;
		} else {
			return false;
		}
	}

	public function shareTwitter()
	{
		if(! $this->isTwitterAuthed())
		{
			return false;
		}

		$twitter_token = [
			'token' => $this->twitter_oauth_token,
			'secret' => $this->twitter_oauth_token_secret 
		];

		\Twitter::reconfig($twitter_token);

		//Create Yeplive Tweet
		try{
			$response = \Twitter::postTweet([
				'status' => "~~~Yeplive~~~\nStreaming now!\n #yeplive yplv.tv/".time()
			]);
			} catch(Exception $e) {
				return false;
			}

		return  $response;
	}

	public function getTwitterFriends()
	{
		if(! $this->isTwitterAuthed())
		{
			return false;
		}

		$twitter_token = [
			'token' => $this->twitter_oauth_token,
			'secret' => $this->twitter_oauth_token_secret 
		];

		\Twitter::reconfig($twitter_token);

		//Create Yeplive Tweet
		try{
			$response = \Twitter::get('friends/list');
			} catch(Exception $e) {
				return false;
			}
		dd($response);
		return  $response;

	}

	public function getAllFriends($fb)
	{
		$facebookFriends = $this->getFacebookFriends($fb);
		$googleFriends = $this->getGoogleFriends();
		$twitterFriends = $this->getTwitterFriends();

		return [
			'facebook' => $facebookFriends ,
			'google' => $googleFriends ,
			'twitter' => $twitterFriends 
		];
	}

}
