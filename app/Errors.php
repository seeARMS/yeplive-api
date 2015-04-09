<?php namespace App;

class Errors{

	static function invalid($validator = null, $messages = [])
	{
		$statusCode = 400;
		if($validator)
		{
			$messages = $validator->messages()->all();
		}

		return [
			'statusCode' => $statusCode,
			'error' => 'invalid_params',
			'messages' => $messages
		];
	}

	static function unauthorized($validator = null, $messages = [])
	{
		$statusCode = 401;
		
		return [
			'statusCode' => $statusCode,
			'error' => 'unauthorized',
			'messages' => $messages
		];
	}

	static function notFound($validator = null)
	{
		$statusCode = 404;

		return [
			'statusCode' => $statusCode,
			'error' => 'not_found'
		];
	}
}

