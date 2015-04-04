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

Route::get('/', 'WelcomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//Transfering routes
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
