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

	public function newMessage(Request $request, $id)
	{
		$user = \JWTAuth::parseToken()->toUser();

		if(! $user){
			return \App\Errors::invalid('invalid token');	
		}
		
		$params = $request->only(
			'message'
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

}
