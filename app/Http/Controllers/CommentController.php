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

	public function getComment(Request $request, $channel_id)
	{
		$comments = \App\Message::where('channel_id', '=', $channel_id)->orderBy('timestamp')->get();

		// Assuming channel_id is yep_id (This may have to be changed later on)
		$yep = \App\Yep::find($channel_id);

		if(!$yep)
		{
			return response()->json(['error' => 'channel_not_found'], 401);
		}

		return response()->json($comments, 200);
	}

}
