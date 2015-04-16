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
			\App\Message::create($params);
		} catch ( \Exception $e){
			return response()->json(['error' => 'invalid input'], 401);
		}
		
		return response()->json(['success' => '1'], 200);
	}

}
