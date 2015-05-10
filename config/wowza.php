<?php

return [
	'thumbnail' => [
		'host' => 'http://52.11.118.105:8086'
	],
	'thumbnail_web' => [
		'host' => 'http://52.24.223.236:8086'
	],
	'rtmp' => [
		'vod' => 'rtmp://52.10.133.244/vods3/_definst_/&mp4:amazons3/dev-wowza/',
		'hlslive_web' => 'http://52.10.133.244/testing-env-yy.elasticbeanstalk.com:1935/',
//		'upload_mobile' => 'rtsp://54.149.243.236:1935/liveorigin/mp4:',//'rtsp://52.10.133.244:1935/test/mp4:',
//		'upload_mobile' => 'rtsp://54.149.243.236:1935/liveorigin/',//'rtsp://52.10.133.244:1935/test/mp4:',
//http://52.11.118.105/
		'upload_mobile' => 'rtsp://52.11.118.105:1935/cf/',
		'upload_web' => 'rtmp://52.24.79.120/liveorigin/',//'rtsp://52.10.133.244:1935/test/mp4:',
		'stream_mp4' => 'https://52.10.133.244/dev-wowza/'

	],
	'rtsp' => [
		'android' => 'rtsp://52.24.36.82/liveedge/_definst_/'
	],
	'android' => [
		'rtsp' => 'rtsp://52.24.36.82/liveedge/_definst_/',
		'rtsp_backup' => 'rtsp://54.149.243.236/liveorigin/',
		'hls_backup' => 'http://54.149.243.236/liveorigin/',
		'rtmp_backup' => 'rtmp://54.149.243.236/liveorigin/',
		'hls' => 'http://52.24.36.82/liveedge/'
	],
	'web' => [
		'rtsp' => 'rstp://52.24.223.236/hdfvr/_definst_/',
		'hls' => 'http://52.24.223.236:1935/hdfvr/',
		'streamer' => 'http://52.24.223.236'	
	],
	'cloudfront_stream' => [
		//http://[buttfront-domain-name]/[application]/[appInstance]/[streamName]/playlist.m3u8
		'hls' => 'http://dsk99qsb45q1n.cloudfront.net/cf/',
		//http://[buttfront-domain-name]/[application]/[appInstance]/[streamName]/manifest.f4m
	],
	'cloudfront' => [
		'static' => 'http://dsk99qsb45q1n.cloudfront.net/'
	],
	's3' => [
		'static' => 'http://s3-us-west-2.amazonaws.com/dev-wowza/'
	]
];
