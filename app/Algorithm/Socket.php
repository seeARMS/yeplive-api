<?php namespace App\Algorithm;

class Socket {
	public static function newYep($yep)
	{
		$data = $yep->toArray();
		$success = self::postSocket('/socket/yeps', $data);
		return $success;
	}

	public static function newView($yep)
	{
		$data = $yep->toArray();
		$success = postSocket('/socket/yeps/views', $data);
		return $success;
	}

	public static function newVote($yep)
	{
		$data = $yep->toArray();
		$success = postSocket('/socket/yeps/votes', $data);
		return $success;
	}

	public static function yepComplete($yep)
	{
		$data = $yep->toArray();
		$success = Socket::postSocket('/socket/yeps/complete', $data);
		return $success;
	}


	public static function postSocket($route, $data)
	{
		$client = new \GuzzleHttp\Client();
		$url = \Config::get('socket.url').$route;
		$response = $client->post($url,  ['json' => $data]);
		if($response->getStatusCode() !== 200){
			return false;
		}
		return true;
	}
}
