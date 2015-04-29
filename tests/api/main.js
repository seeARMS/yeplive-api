var frisby = require('frisby');

var URL = 'http://development-vriepmhkv2.elasticbeanstalk.com/api/v1';

console.log(URL+'/yeps');

frisby.create('GET all yeps')
	.get(URL+'/yeps')
	.expectStatus(400)
	.expectJSONTypes({
		yeps:Array
	})
	.expectJSON({
	})
	.afterJSON(function(data){
		
		})
.toss();
