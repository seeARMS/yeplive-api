<?php

return [
	'thumbnail' => [
		'host' => 'http://52.10.133.244:8086'
	],
	'rtmp' => [
		'stream' => 'rtmp://'.env('WOWZA_EDGE').'liveedge/_definst_/mp4:',//'rtmp://wowza-edge-prod-beta.elasticbeanstalk.com/liveedge/_definst_/mp4:',
		'hdfvr' => '',
		'vod' => 'rtmp://52.10.133.244/vods3/_definst_/&mp4:amazons3/dev-wowza/',
		'hlslive_web' => 'http://52.10.133.244/testing-env-yy.elasticbeanstalk.com:1935/',
//		'upload_mobile' => 'rtsp://54.149.243.236:1935/liveorigin/mp4:',//'rtsp://52.10.133.244:1935/test/mp4:',
		'upload_mobile' => 'rtsp://'.env('WOWZA_ORIGIN').':1935/liveorigin/',//'rtsp://52.10.133.244:1935/test/mp4:',
		'stream_mp4' => 'https://52.10.133.244/dev-wowza/'
	],
	'cloudfront' => [
		'static' => 'http://dwvjlx2oulfs.cloudfront.net/'
	]
];
