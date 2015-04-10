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
		'uses' => 'UsersController@auth'
	]);

	// Generate a login URL
	Route::get('facebook/login', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
	{
		// Send an array of permissions to request
		$login_url = $fb->getLoginUrl(['email','public_profile', 'user_friends']);
		echo '<a href="' . $login_url . '">Login with Facebook</a>';
	});

	Route::get('facebook/callback', [
		'uses' => 'UsersController@facebookCallback'
	]);

	Route::get('twitter/login', [
		'uses' => 'UsersController@loginTwitter'	
	]);

	Route::get('twitter/callback', [	
		'uses' => 'UsersController@twitterCallback'
	]);

	Route::get('google/login', [
		'uses' => 'UsersController@loginGoogle'	
	]);

	Route::get('google/callback', [	
		'uses' => 'UsersController@googleCallback'
	]);

	/*
	 * Authenticate a user
	 */
	Route::post('auth', [
		'uses' => 'UsersController@authenticate'
	]);
	/*
	 * User Sign up
	 */
	Route::post('users', [
		'uses' => 'UsersController@signup'
	]);

	//ALL THESE ROUTES REQURE A JWT TOKEN
	//disabled
	Route::group(['middleware' => 'jwt.auth'], function(){
		//RESTful yeps 
		Route::resource('yeps', 'YepsController',
			['except' => ['destroy', 'create', 'edit']]);
		Route::get('yeps/{id}/similar', [
			'uses' => 'YepsController@similar'
		]);
		Route::post('yeps/{id}/views', [
			'uses' => 'YepsController@incrementViews'
		]);
		Route::post('yeps/{id}/votes',[
			'uses' => 'YepsController@vote'
		]);
		Route::post('yeps/{id}/reports',[
			'uses' => 'YepsController@report'
		]);
		/* SIMPLIFYING API INFO NOW IN SHOW/INDEX
		Route::get('yeps/{id}/tags', [
			'uses' => 'YepsController@tags'
		]);
		Route::get('yeps/{id}/views',[
			'uses' => 'YepsController@viewCount'
		]);
		Route::get('yeps/{id}/votes',[
			'uses' => 'YepsController@votes'
		]);
		Route::get('yeps/{id}/votes/my',[
			'uses' => 'YepsController@userVote'
		]);
		*/
		//RESTful Users
		/*
	 	 * Check authentication status by user email and password
	 	 */
		Route::get('auth', [
			'uses' => 'UsersController@checkAuth'
		]);
		/*
	 	 * Show all users
	 	 */
		Route::get('users', [
			'uses' => 'UsersController@showAllUsers'
		]);
		/*
		 *
		 */
		Route::get('users/{id}', [
			'uses' => 'UsersController@getUser'
		]);
		/*
	 	 * User {id} follows a followee
	 	 */
		Route::post('users/{id}/follow', [
			'uses' => 'UsersController@addFollowing'
		]);
		/*
		 * Display all followees followed by follower {id}
		 */
		Route::get('users/{id}/followees',[
			'uses' => 'UsersController@showAllFollowee'
		]);
		/*
		 * Display all followers following followee {id}
		 */
		Route::get('users/{id}/followers',[
			'uses' => 'UsersController@showAllFollower'
		]);

		//These routes all require the {id} to be the user in the json web token
		Route::group(['middleware' => 'jwt.checkUser'], function(){
			//TODO
			Route::post('users/{id}/thumbnail',[
				'uses' => 'UsersController@updateThumbnail'
			]);
			Route::get('users/{id}/settings',[
				'uses' => 'UsersController@getSettings'
			]);
			Route::post('users/{id}/settings',[
				'uses' => 'UsersController@settings'
			]);
			//TODO
			Route::get('users/{id}/friends', [
				'uses' => 'UsersController@friends' 
			]);
			/*
			 * Change password request by user_id {id}
			 */
			Route::post('users/{id}/settings/password',[
				'uses' => 'UsersController@changePassword'
			]);
			/*
			 * Change email request by user_id {id}
			 */
			Route::post('users/{id}/settings/email',[
				'uses' => 'UsersController@changeEmail'
			]);
		});
	});
});
