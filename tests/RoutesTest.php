<?php
class RoutesTest extends TestCase{

	public function setUp()
	{
		parent::setUp(); // Don't forget this!
		//clear all databases
		$tableNames = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
		foreach ($tableNames as $name) {
    //if you don't want to truncate migrations
    if ($name == 'migrations') {
        continue;
    }
    DB::table($name)->truncate();
}
	
	}

	public function testRootRoute()
	{
		$response = $this->call('GET', '/api/v1');
		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testAllRoutes()
	{
		$response = $this->call('POST', '/api/v1/auth/mobile');
		$this->assertEquals($response->getStatusCode(), 400);

		$userData = [
			'email' => 'test@gmail.com',
			'password' => 'password'
		];

		$response = $this->call('POST', '/api/v1/users', $userData);
		$json = json_decode($response->getContent());
		$this->assertEquals(200, $response->getStatusCode());	
		$this->assertEquals($json->email, 'test@gmail.com');

		$token = $json->success->token;
		$headers = [
			'HTTP_Authorization' => 'Bearer '.$token
		];	
		$response = $this->call('GET', '/api/v1/auth', [],[],[], $headers);
		$this->assertEquals(200, $response->getStatusCode());


		$response = $this->call('POST', '/api/v1/auth', $userData);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$newToken = $json->success->token;

		$response = $this->call('GET', '/api/v1/auth', [],[],[], $headers);
		$this->assertEquals(200, $response->getStatusCode());
		$newHeaders = [
			'HTTP_Authorization' => 'Bearer '.$newToken
		];	
		$json = json_decode($response->getContent());
		$userId = $json->user_id;

		$response = $this->call('GET', '/api/v1/auth', [],[],[], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/api/v1/yeps', [],[],[], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());

		$yepData = [
			'channel_id' => 1,
			'title' => 'a test Yep',
			'description' => 'this is a test Yep',
			'latitude' => 123,
			'longitude' => -12,
			'location' => 'test location',
			'tags' => 'these,are,test,tags',
			'start_time' => '1',
			'end_time' => '1'
		];

		$response = $this->call('POST', '/api/v1/yeps', $yepData ,[],[], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/api/v1/yeps', [],[],[],$newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$Yeps = json_decode($response->getContent());
		
		$yep_id = $Yeps->yeps[0]->id;		

		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id, [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		
		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id, [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(0, $json->votes);
/*
		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id.'/votes/my', [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(0, $json->vote);
*/

		$response = $this->call('POST', '/api/v1/yeps/'.$yep_id.'/votes', [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(1, $json->vote);

		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id, [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(1, $json->votes);
/*
		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id.'/votes/my', [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(1, $json->vote);
*/

		$response = $this->call('POST', '/api/v1/yeps/'.$yep_id.'/votes', [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(0, $json->vote);

		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id, [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(0, $json->vote);

		$response = $this->call('POST', '/api/v1/yeps/'.$yep_id.'/votes', [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals(1, $json->vote);

		/*
		 * TEST VIEWS & VIEW COUNT
		 */
		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id.'/views', [], [], [], $newHeaders);
		$json = json_decode($response->getContent());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals(0, $json->views);

		$response = $this->call('POST', '/api/v1/yeps/'.$yep_id.'/views', [], [], [], $newHeaders);
		$json = json_decode($response->getContent());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals(1, $json->views);
			
		$response = $this->call('GET', '/api/v1/yeps/'.$yep_id.'/views', [], [], [], $newHeaders);
		$json = json_decode($response->getContent());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals(1, $json->views);

	/*
	 * TEST REPORTING
	 */
		$reportData = [
			'reason' => 'Innapropriate Video'
		];
		$response = $this->call('POST', '/api/v1/yeps/'.$yep_id.'/reports', $reportData, [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());



			
		$response = $this->call('GET', '/api/v1/users/'.$userId, [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		
		$response = $this->call('GET', '/api/v1/users/'.$userId.'/settings', [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$this->assertEquals($json->push_notifications, 1);

		$settingsData = [
			'push_notifications' => '0',
			'device_token' => 'token'
		];
		$response = $this->call('POST', '/api/v1/users/'.$userId.'/settings', $settingsData, [], [], $newHeaders);
		$this->assertEquals($response->getStatusCode(), 200);
		$json = json_decode($response->getContent());

	}

}
?>
