<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CommentController extends Controller {

	public function addComment(Request $request, $yep_id)
	{
		$params = $request->only(
			'created_time',
			'comment'
		);

		$validator = \Validator::make( $params, [
			'created_time' => 'integer',
			'comment' => 'string'
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		$user = \JWTAuth::parseToken()->toUser();

		$comment['yep_id'] = (int)$yep_id;
		$comment['user_id'] = $user->user_id;
		$comment['comment'] = $params['comment'];
		$comment['created_time'] = (int)$params['created_time'];

		try {
			\App\Comments::create($comment);
		} catch ( \Comments $e){
			return \App\Errors::invalid('invalid input');
		}

		$comment['display_name'] = $user->display_name;

		return response()->json(['success' => '1', 'comment' => $comment]);
	}

	public function getComments(Request $request, $yep_id)
	{

		$yep = \App\Yep::find($yep_id);

		if(!$yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$commnets = \DB::table('yeplive_users')
					->select('comments.created_time',
								'comments.updated_time',
								'comments.comment',
								'yeplive_users.user_id',
								'yeplive_users.name',
								'yeplive_users.display_name',
								'yeplive_users.picture_path')
					->join('comments', function($join) use ($yep_id)
					{
						$join->on('comments.user_id', '=', 'yeplive_users.user_id')
							 ->where('comments.yep_id', '=', $yep_id);
					})->orderBy('comments.created_time', 'desc')->get();

		return response()->json(['success' => 1, 'comments' => $commnets]);

	}

}
