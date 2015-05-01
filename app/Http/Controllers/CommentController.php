<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CommentController extends Controller {

	public function addComment(Request $request, $id, $channel_id)
	{
		$params = $request->only(
			'timestamp',
			'message'
		);

		$user = \App\User::find($id);

		$params['channel_id'] = $channel_id;
		$params['display_name'] = $user->display_name;
		$params['sender_id'] = $id;

		try {
			\App\Comments::create($params);
		} catch ( \Comments $e){
			return response()->json(['error' => 'invalid input'], 401);
		}

		return response()->json(['success' => '1'], 200);
	}

	public function getComments(Request $request, $yep_id)
	{

		$yep = \App\Yep::find($yep_id);

		if(!$yep)
		{
			return \App\Errors::notFound('yep not found');
		}

		$commnets = \DB::table('yeplive_users')
					->select('comments.created_at',
								'comments.updated_at',
								'comments.comment',
								'yeplive_users.user_id',
								'yeplive_users.name',
								'yeplive_users.display_name',
								'yeplive_users.picture_path')
					->join('comments', function($join) use ($yep_id)
					{
						$join->on('comments.user_id', '=', 'yeplive_users.user_id')
							 ->where('comments.yep_id', '=', $yep_id);
					})->orderBy('comments.created_at', 'desc')->get();

		return response()->json(['success' => 1, 'comments' => $commnets]);

	}

}
