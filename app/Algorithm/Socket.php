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
		$data = [
			'view_count' => $yep->views,
			'yep_id' => $yep->id
		];
		$success = self::postSocket('/socket/yeps/views', $data);
		return $success;
	}

	public static function yepDelete($yep)
	{
		$data = $yep->toArray();
		$success = self::postSocket('/socket/yeps/delete', $data);
		return $success;
	}

	public static function newVote($yep)
	{
		$data = [
			'vote_count' => $yep->upvotes(),
			'yep_id' => $yep->id
		];
		$success = self::postSocket('/socket/yeps/votes', $data);
		return $success;
	}

	public static function yepComplete($yep)
	{
		$data = $yep->toArray();
		$success = self::postSocket('/socket/yeps/complete', $data);
		return $success;
	}


	public static function postSocket($route, $data)
	{
		$client = new \GuzzleHttp\Client();
		$url = \Config::get('socket.url').$route;
		$response = $client->post($url,  ['json' => $data,
			'timeout' => 3	
		]);
		if($response->getStatusCode() !== 200){
			return false;
		}
		return true;
	}
}
