<?php namespace App\Algorithm;

class ProS3 {
	static function storeThumbnail($user, $file)
	{
		$name = strval(time()) . $file->getClientOriginalName();
		\Storage::put($name, \File::get($file));
		return "https://s3-us-west-2.amazonaws.com/yeplive-api-dev/".$name;
	}
}
