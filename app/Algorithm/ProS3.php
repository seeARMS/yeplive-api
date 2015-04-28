<?php namespace App\Algorithm;

class ProS3 {
	static function storeThumbnail($user, $file)
	{
		$name = strval(time()) . $file->getClientOriginalName();
		\Storage::put($name, \File::get($file));
		return "https://s3-us-west-2.amazonaws.com/yeplive-api-dev/".$name;
	}

	static function storeYepThumbnail($path, $name)
	{
		$username = 'wowza';
		$password = 'i-d9d19e10';
		
		$process = curl_init();
	
		curl_setopt($process, CURLOPT_URL, $path);
		curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST ) ;
		curl_setopt($process, CURLOPT_USERPWD, $username.':'.$password);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		$res = curl_exec($process);
		curl_close($process);
		\Storage::put($name, $res);
		
		return "https://s3-us-west-2.amazonaws.com/yeplive-api-dev/".$name;
	}
}
