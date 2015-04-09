<?php 

//RESTful API
//ALL {id} tags only accept [0-9]+ see app/providers/RouteServiceProvider.php
Route::group(array('prefix' => 'api/v1'), function()
{ 
	/*
	 * API Health Check
	 */
	Route::get('/', function(){
		return response()->json(['name' => 'Yeplive Web API', 'version' => '1.0']);
	});

	Route::post('auth/mobile',[
		'uses' => 'UserController@auth'
	]);

	// Generate a login URL
	Route::get('facebook/login', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		// Send an array of permissions to request
		$login_url = $fb->getLoginUrl(['email','public_profile', 'user_friends']);
		echo '<a href="' . $login_url . '">Login with Facebook</a>';
	});

	Route::get('facebook/callback', [
		'uses' => 'UserController@facebookCallback'
	]);

	Route::get('twitter/login', [
		'uses' => 'UserController@loginTwitter'	
	]);

	Route::get('twitter/callback', [	
		'uses' => 'UserController@twitterCallback'
	]);

	Route::get('google/login', [
		'uses' => 'UserController@loginGoogle'	
	]);

	Route::get('google/callback', [	
		'uses' => 'UserController@googleCallback'
	]);

	/*
	 * Authenticate a user
	 */
	Route::post('auth', [
		'uses' => 'UserController@authenticate'
	]);
	/*
	 * User Sign up
	 */
	Route::post('users', [
		'uses' => 'UserController@signup'
	]);

	//ALL THESE ROUTES REQURE A JWT TOKEN
	Route::group(['middleware' => 'jwt.auth'], function(){
		//RESTful Programs
		Route::resource('programs', 'ProgramController',
			['except' => ['destroy', 'create', 'edit']]);
		Route::get('programs/{id}/similar', [
			'uses' => 'ProgramController@similar'
		]);
		Route::get('programs/{id}/tags', [
			'uses' => 'ProgramController@tags'
		]);
		Route::get('programs/{id}/views',[
			'uses' => 'ProgramController@viewCount'
		]);
		Route::post('programs/{id}/views', [
			'uses' => 'ProgramController@incrementViews'
		]);
		Route::get('programs/{id}/votes',[
			'uses' => 'ProgramController@votes'
		]);
		Route::get('programs/{id}/votes/my',[
			'uses' => 'ProgramController@userVote'
		]);
		//TODO
		Route::post('programs/{id}/votes',[
			'uses' => 'ProgramController@vote'
		]);
		//TODO
		Route::post('programs/{id}/reports',[
			'uses' => 'ProgramController@report'
		]);
		//RESTful Users
		/*
	 	 * Check authentication status by user email and password
	 	 */
		Route::get('auth', [
			'uses' => 'UserController@checkAuth'
		]);
		/*
	 	 * Show all users
	 	 */
		Route::get('users', [
			'uses' => 'UserController@showAllUsers'
		]);
		/*
		 *
		 */
		Route::get('users/{id}', [
			'uses' => 'UserController@getUser'
		]);
		/*
	 	 * User {id} follows a followee
	 	 */
		Route::post('users/{id}/follow', [
			'uses' => 'UserController@addFollowing'
		]);
		/*
		 * Display all followees followed by follower {id}
		 */
		Route::get('users/{id}/followees',[
			'uses' => 'UserController@showAllFollowee'
		]);
		/*
		 * Display all followers following followee {id}
		 */
		Route::get('users/{id}/followers',[
			'uses' => 'UserController@showAllFollower'
		]);

		//These routes all require the {id} to be the user in the json web token
		Route::group(['middleware' => 'jwt.checkUser'], function(){
			//TODO
			Route::post('users/{id}/thumbnail',[
				'uses' => 'UserController@updateThumbnail'
			]);
			Route::get('users/{id}/settings',[
				'uses' => 'UserController@getSettings'
			]);
			Route::post('users/{id}/settings',[
				'uses' => 'UserController@settings'
			]);
			//TODO
			Route::get('users/{id}/friends', [
				'uses' => 'UserController@friends' 
			]);
			/*
			 * Change password request by user_id {id}
			 */
			Route::post('users/{id}/settings/password',[
				'uses' => 'UserController@changePassword'
			]);
			/*
			 * Change email request by user_id {id}
			 */
			Route::post('users/{id}/settings/email',[
				'uses' => 'UserController@changeEmail'
			]);
		});
	});
});
