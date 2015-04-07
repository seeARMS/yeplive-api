<?php 
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', 'WelcomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
*/

//RESTful API
//ALL {id} tags only accept [0-9]+ see app/providers/RouteServiceProvider.php
Route::group(array('prefix' => 'api/v1'), function()
{ 
	Route::post('authenticate', [
		'uses' => 'UserController@authenticate'
	]);
	Route::post('user', [
		'uses' => 'UserController@store'
	]);
	
	//ALL THESE ROUTES REQURE A JWT TOKEN
	Route::group(['middleware' => 'jwt.auth'], function(){
		//Check authentication
		Route::get('authenticate', [
			'uses' => 'UserController@check_auth'
		]);	
		//RESTful Programs
		Route::resource('program', 'ProgramController',
			['except' => ['destroy', 'create', 'edit']]);
		Route::get('program/{id}/similar', [
			'uses' => 'ProgramController@similar'
		]);
		Route::get('program/{id}/tags', [
			'uses' => 'ProgramController@tags'
		]);
		//TODO
		Route::get('program/{id}/views',[
			'uses' => 'ProgramController@viewCount'
		]);
		//TODO
		Route::get('program/{id}/votes',[
			'uses' => 'ProgramController@votes'
		]);
		//TODO
		Route::get('program/{id}/negative_votes',[

		]);
		//TODO
		Route::get('program/{id}/my_vote',[
			'uses' => 'ProgramController@userVote'
		]);
		//TODO
		Route::post('program/{id}/vote',[
			'uses' => 'ProgramController@vote'
		]);
		//TODO
		Route::post('program/{id}/report',[

		]);
		//RESTful Users
		Route::get('user', [
			'users' => 'UserController@show'
		]);
		//TODO
		Route::get('user/{id}/is_follow',[

		]);
		//TODO
		Route::post('user/{id}/become_fan',[

		]);
		//TODO
		Route::post('user/{id}/thumbnail',[

		]);
		//TODO
		Route::post('user/{id}/settings',[

		]);
	});
});


//LEAVING OLD WORDPRESS ROUTES IN FOR REFERENCE
/*
//Transfering routes
//XMLRPCRoute
Route::get('xmlrpc.php', [
	'uses' => 'XMLRPCController@index'
]);
//Directly handled HTTP Requests
//PARAMS:
//program_id, user_sender_id, action
Route::get('ajaxCreateChatResponseNotifications.php', function()
{
	return 'TO BE IMPLIMENTED';
});
//PARAMS:
//program_id, user_id
Route::get('ajaxGetSimilarVideos.php',[
	'uses' => 'ProgramController@get_similar_videos'
]); 
//PARAMS:
//user_id, loginUserId
Route::get('ajaxBecomeFan.php',[
	'uses' => 'UserController@become_fan'
]);
//PARAMS:
//program_id
Route::get('ajaxGetTags.php',[ 
	'uses' => 'ProgramController@get_tags'
]);

//PARAMS:
//program_id
Route::get('ajaxVideoViewerCount.php',[ 
	'uses' => 'ProgramController@get_video_view_count'
]);
//PARAMS:
//program_id, user_id, vote
Route::get('ajaxSetVote.php',[ 
	'uses' => 'ProgramController@set_vote'
]);
//PARAMS:
//program_id
Route::get('ajaxGetVotes.php',[ 
	'uses' => 'ProgramController@get_votes'
]);
//PARAMS:
//program_id
Route::get('ajaxGetNegVotes.php',[ 
	'uses' => 'ProgramController@get_neg_votes'
]);
//PARAMS:
//program_id, user_id
Route::get('ajaxGetVote.php',[ 
	'uses' => 'ProgramController@get_user_vote'
]);
//PARAMS:
//program_id, reporter, reported, videoURL, reason
Route::get('ajaxReportVideo.php',[ 
	'uses' => 'ProgramController@report_video'
]);
//PARAMS:
//program_id, title, description, latitude, longitude, location, thumbnail_path, tags
Route::get('ajaxUpdateProgramInfo.php',[ 
	'uses' => 'ProgramController@update_program_info'
]);
//PARAMS
//channel_id (always 40), userid, title, description, latitude, longitude, location, tags, imgurl, vod_enable
Route::get('ajaxAddProgram.php',[ 
	'uses' => 'ProgramController@add_program'
]);
//PARAMS
//name, filename 
Route::post('ajaxUploadThumbnail.php',[ 
	'uses' => 'UserController@upload_thumbnail'
]);
//PARAMS
//user_id, userpic
Route::get('ajaxUpdateUserpic.php',[ 
	'uses' => 'UserController@update_user_pic'
]);
//PARAMS
//pan_id, user_id
Route::get('ajaxGetIsFollow.php',[ 
	'uses' => 'UserController@is_follow'
]);
//PARAMS
//chat_response_notifications
Route::get('ajaxUpdateUserSettings.php',[ 
	'uses' => 'UserController@update_user_settings'
]);
*/
