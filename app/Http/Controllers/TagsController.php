<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TagsController extends Controller {

	public function getTagsInArea(Request $request)
	{
		$params = $request->only([
			'latitude',
			'longitude'
		]);

	
		$validator = \Validator::make( $params, [
			'latitude' => 'required|numeric',
			'longitude' => 'required|numeric',
		]);

		if($validator -> fails())
		{
			return \App\Errors::invalid(null, $validator);
		}

		$tags = [];


		$latitudeRange = [
			$params['latitude'] - 1,
			$params['latitude'] + 1	
		];

		$longitudeRange = [
			$params['longitude'] - 1,
			$params['latitude'] + 1
		];


		$yeps = \App\Yep::with('tags')
			->whereBetween('latitude', $latitudeRange)
			->whereBetween('longitude', $longitudeRange)
			->get();

		foreach($yeps as $yep)
		{
			foreach($yep['tags'] as $tag){
				if(! array_key_exists($tag->tag_slug,$tags)){
					$tags[$tag->tag_slug] = 0;
				}
				$tags[$tag->tag_slug] += 1;
			}
		}
	
		return $tags;	

	}

}
