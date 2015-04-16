<?php 
//RESTful API
//ALL {id} tags only accept [0-9]+ see app/providers/RouteServiceProvider.php
Route::group(array('prefix' => 'api/v1'), function()
{ 
	//Testing routes
	if (\App::environment('local'))
	{
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

		Route::group(array('prefix' => 'test'), function()
		{
		/*create/sign up as a user for testing*/
			Route::post('auth', [
				'uses' => 'UsersController@authenticate'
			]);

		});
	}
	
	/*
	 * API Health Check
	 */
	Route::get('/', function(){
		return response()->json(['name' => 'Yeplive Web API', 'version' => '1.0']);
	});

	// Internal apis 
	Route::group(['prefix' => 'internal'], function(){
		//communicate with socket.io chat server
		Route::post('chat/{id}/connect', [
			'uses' => 'ChatController@connection'
		]);
		Route::post('chat/{id}/disconnect', [
			'uses' => 'ChatController@disconnection'
		]);
		Route::get('chat/{id}/messages', [
			'uses' => 'ChatController@messages'
		]);

		/* Moved down under jwt.checkUser
		Route::post('chat/{id}/messages',[
			'uses' => 'ChatController@newMessage'
		]);
		*/
	});

	Route::post('auth/social',[
		'uses' => 'UsersController@socialAuth'
	]);

	Route::get('yeps', [
		'uses' => 'YepsController@index'
	]);
	Route::get('yeps/{id}', [
		'uses' => 'YepsController@show'
	]);

	Route::get('yeps/{id}/similar', [
		'uses' => 'YepsController@similar'
	]);

	// ALL THESE ROUTES REQURE A JWT TOKEN
	Route::group(['middleware' => 'jwt.auth'], function(){
		//RESTful yeps 
		Route::post('yeps', [
			'uses' => 'YepsController@store'
		]);
		Route::put('yeps', [
			'uses' => 'YepsController@update'
		]);
	
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
		Route::post('yeps/{id}/complete',[
			'uses' => 'YepsController@streamComplete'
		]);
		/*
		Route::get('yeps/{id}/votes',[
			'uses' => 'YepsController@userVote'	
		]);
		*/
		///RESTful Users
		/*
	 	 * Check authentication status by user email and password
	 	 */
		Route::get('me', [
			'uses' => 'UsersController@checkAuth'
		]);
		/*
	 	 * Show all users
		Route::get('users', [
			'uses' => 'UsersController@showAllUsers'
		]);
	 	 */
		/*
		 *
		 */
		Route::get('users/{id}', [
			'uses' => 'UsersController@getUser'
		]);
		Route::get('users/{id}/yeps',[
			'uses' => 'UsersController@getYeps'
		]);
		Route::get('users/{id}/followers',[
			'uses' => 'UsersController@getFollowers'
		]);
		Route::post('users/{id}/followers', [
			'uses' => 'UsersController@follow'
		]);
		Route::delete('users/{id}/followers', [
			'uses' => 'UsersController@unfollow'
		]);
		Route::get('users/{id}/following',[
			'uses' => 'UsersController@getFollowing'
		]);
		/*
	 	 * User {id} follows a followee
		Route::post('users/{id}/follow', [
			'uses' => 'UsersController@addFollowing'
		]);
	 	 */
		/*
		 * Display all followees followed by follower {id}
		Route::get('users/{id}/followees',[
			'uses' => 'UsersController@showAllFollowee'
		]);
		 */
		/*
		 * Display all followers following followee {id}
		Route::get('users/{id}/followers',[
			'uses' => 'UsersController@showAllFollower'
		]);
		 */

		//These routes all require the {id} to be the user in the json web token
		Route::group(['middleware' => 'jwt.checkUser'], function(){
			Route::post('users/{id}/picture',[
				'uses' => 'UsersController@updatePicture'
			]);
			Route::get('users/{id}/settings',[
				'uses' => 'UsersController@getSettings'
			]);
			Route::post('users/{id}/settings',[
				'uses' => 'UsersController@settings'
			]);
			/*
			Route::get('users/{id}/friends', [
				'uses' => 'UsersController@friends' 
			]);
			*/
			/*
			 * Change password request by user_id {id}
			Route::post('users/{id}/settings/password',[
				'uses' => 'UsersController@changePassword'
			]);
			 */
			/*
			 * Change email request by user_id {id}
			 */
			Route::post('users/{id}/settings/email',[
				'uses' => 'UsersController@changeEmail'
			]);

			/*
			 * User add a comment to a channel
			 */
			Route::post('user/{id}/comment/{channel_id}',[
				'uses' => 'CommentController@addComment'
			]);

			Route::group(['prefix' => 'internal'], function(){
				/*
				 * Ideally, we should allow anonymous guest to live record videos
				 * They will then be prompted to sign in with thier social accounts
				 * Then their chat messages will be stored in db, otherwise, no messages are stored
				 * This api will be called from only registered users
				 * This should be called asynchronously
				 */
				Route::post('user/{id}/chat/{channel_id}/messages',[
					'uses' => 'ChatController@compileMessages'
				]);
			});
		});
	});

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
});
