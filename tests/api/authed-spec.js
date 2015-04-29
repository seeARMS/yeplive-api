var frisby = require('frisby');

var URL = 'http://development-vriepmhkv2.elasticbeanstalk.com/api/v1';




frisby.create('GET all yeps')
	.get(URL+'/yeps')
	.expectStatus(200)
	.expectJSONTypes({
		yeps: Array
	})
	.expectJSON({
	})
	.afterJSON(function(data){
		
	 })
	.toss();

frisby.create('POST new yep')
	
