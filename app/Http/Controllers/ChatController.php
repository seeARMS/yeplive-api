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

		$messages = \App\Message::where('channel_id', '=', $yep->id)->take(25)->get();
	
		return response()->json($messages, 200);
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

		$messages = \App\Message::where('channel_id', '=', $yep->id)->get();
	
		return response()->json($messages, 200);	

	}

	public function messages(Request $request, $id)
	{
		//$yep= \App\Yep::where('channel_id', '=', $id)->get()-first();
		$yep = \App\Yep::find($id);

		$messages = \App\Message::where('channel_id', '=', $id)->get();
	
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

	public function compileMessages(Request $request, $user_id, $channel_id)
	{
		
		// Check if user is the owner of this channel
		// Only channel owner can compile chat messages
		// Assuming channel_id is yep_id (This may have to be changed later on)
		$yep = \App\Yep::find($channel_id);

		if( !$yep || $yep->user_id != $user_id )
		{
			return response()->json(['error' => 'unauthorized'], 401);
		}

		$messageObj = $request->only(
			'messages'
		);
		
		$messageObj = json_decode($messageObj['messages']);

		try {

			foreach($messageObj as $message)
			{
					$params['sender_id']	= $message->sender_id;
					$params['message']		= $message->message;
					$params['timestamp']	= $message->timestamp;
					$params['display_name'] = $message->display_name;
					$params['channel_id']	= $channel_id;
					\App\Message::create($params);
			}

		} catch (\Exception $e) {
			return response()->json(['error' => 'invalid input'], 401);
		}
		
		return response()->json(['success' => '1'], 200);
	}

}
