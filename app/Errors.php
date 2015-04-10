<?php namespace App;


class Errors{

	static function invalid($message = null, $validator = null)
	{
		$statusCode = 400;
		if($validator)
		{
			$messages = $validator->messages()->all();
		} else {
			$messages = [$message];
		}

		return response()->json([
			'statusCode' => $statusCode,
			'error' => 'invalid_params',
			'messages' => $messages
		], $statusCode);
	}

	static function unauthorized($message = null,$validator = null)
	{
		$statusCode = 401;

		if($validator)
		{
			$messages = $validator->messages()->all();
		} else {
			$messages = [$message];
		}
		
		return response()->json([
			'statusCode' => $statusCode,
			'error' => 'unauthorized',
			'messages' =>$messages
		], $statusCode);
	}

	static function forbidden($message = null, $validator = null)
	{
		$statusCode = 403;

		if($validator)
		{
			$messages = $validator->messages()->all();
		} else {
			$messages = [$message];
		}
		
		return response()->json([
			'statusCode' => $statusCode,
			'error' => 'forbidden',
			'messages' => $messages
		], $statusCode);

	}

	static function notFound($message = null, $validator = null)
	{
		$statusCode = 404;

		if($validator)
		{
			$messages = $validator->messages()->all();
		} else {
			$messages = [$message];
		}
		
		return response()->json([
			'statusCode' => $statusCode,
			'error' => 'not_found',
			'messages' => $messages
		], $statusCode);
	}
}

