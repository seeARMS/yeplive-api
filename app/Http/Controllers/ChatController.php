<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ChatController extends Controller {
	public function connection(Request $request, $id)
	{
		//$yep= \App\Yep::where('channel_id', '=', $id)->get()-first();
		$yep = \App\Yep::find($id);

		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		
		$yep -> connect_count++;
	
		$yep->save();

		//$messages = \App\Message::where('channel_id', '=', $yep->id)->take(25)->get();
	
		//return response()->json($messages, 200);
		return response()->json({"success": 1});
	}

	public function disconnection(Request $request, $id)
	{
		//$yep= \App\Yep::where('channel_id', '=', $id)->get()-first();
		$yep = \App\Yep::find($id);

		if(! $yep)
		{
			return \App\Errors::notFound('yep not found');
		}
		

		$yep->connect_count--;

		$yep->save();

		//$messages = \App\Message::where('channel_id', '=', $yep->id)->get();
	
		//return response()->json($messages, 200);
		return response()->json({"success": 1});	

	}

	public function messages(Request $request, $yep_id)
	{
		//$yep= \App\Yep::where('channel_id', '=', $id)->get()-first();
		$yep = \App\Yep::find($yep_id);

		if( !$yep )
		{
			return \App\Errors::notFound('yep not found');
		}

		$messages = \App\Comments::where('yep_id', '=', $yep_id)->get();
	
		return response()->json($messages, 200);	
	}

	/*
	public function newMessage(Request $request, $id)
	{
		$user = \JWTAuth::parseToken()->toUser();

		if(! $user){
			return \App\Errors::invalid('invalid token');	
		}
		
		$params = $request->only(
			'message',
			'sender_id',

		);
	
		$validator = \Validator::make( $params, [
			'message' => 'string|max:255',
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		if(! $params['message'])
		{
			return \App\Errors::invalid('message cannot be null');
		}

		$params['user_id'] = $user->user_id;
		$params['channel_id'] = $id;
		$params['display_name'] = $user->display_name;

		$message = \App\Message::create($params);
		
		return response()->json(['success' => 1, 'id' => $message->id]);
	}
	*/

	public function compileMessages(Request $request, $user_id, $yep_id)
	{
		// Only channel owner can compile chat messages
		$yep = \App\Yep::find($yep_id);

		if( !$yep || $yep->user_id != $user_id )
		{
			return \App\Errors::notFound('yep not found');
		}

		$messageObj = $request->only(
			'messages'
		);
		
		$messageObj = json_decode($messageObj['messages']);

		try {

			foreach($messageObj as $message)
			{
					$params['user_id']		= $message->user_id;
					$params['comment']		= $message->comment;
					$params['created_at']	= $message->created_at;
					$params['yep_id']		= $yep_id;
					\App\Message::create($params);
			}

		} catch (\Exception $e) {
			return \App\Errors::invalid('invalid inputs');
		}
		
		return response()->json({"success": 1});
	}

}
