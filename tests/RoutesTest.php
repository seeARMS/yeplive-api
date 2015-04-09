<?php
class RoutesTest extends TestCase{

	public function setUp()
	{
		parent::setUp(); // Don't forget this!
		//clear all databases
		\App\User::truncate();
		\App\Program::truncate();
	}

	public function testRootRoute()
	{
		$response = $this->call('GET', '/api/v1');
		$this->assertEquals(200, $response->getStatusCode());
	}

	public function testCreateUser()
	{
		$userData = [
			'email' => 'test@gmail.com',
			'password' => 'password'
		];
		$response = $this->call('POST', '/api/v1/user', $userData);
		$json = json_decode($response->getContent());
		$this->assertEquals($json->email, 'test@gmail.com');
		$this->assertEquals(200, $response->getStatusCode());	

		$token = $json->success->token;
		$headers = [
			'HTTP_Authorization' => 'Bearer '.$token
		];	
		$response = $this->call('GET', '/api/v1/authenticate', [],[],[], $headers);
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('POST', '/api/v1/authenticate', $userData);
		$this->assertEquals(200, $response->getStatusCode());
		$json = json_decode($response->getContent());
		$newToken = $json->success->token;

		$response = $this->call('GET', '/api/v1/authenticate', [],[],[], $headers);
		$this->assertEquals(200, $response->getStatusCode());
		$newHeaders = [
			'HTTP_Authorization' => 'Bearer '.$newToken
		];	
		$response = $this->call('GET', '/api/v1/authenticate', [],[],[], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/api/v1/program', [],[],[], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());

		$programData = [
			'channel_id' => 1,
			'title' => 'a test program',
			'description' => 'this is a test program',
			'latitude' => 123,
			'longitude' => -12,
			'location' => 'test location',
			'tags' => 'these,are,test,tags',
			'start_time' => '1',
			'end_time' => '1'
		];

		$response = $this->call('POST', '/api/v1/program', $programData ,[],[], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/api/v1/program', [],[],[],$newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		$programs = json_decode($response->getContent());
		
		$program_id = $programs->programs[0]->id;		

		$response = $this->call('GET', '/api/v1/program/'.$program_id, [], [], [], $newHeaders);
		$this->assertEquals(200, $response->getStatusCode());
		
		
		
	}

/*
	public function testMobileRoutes()
	{
		$response = $this->call('GET','/ajaxCreateChatResponseNotifications.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxGetSimilarVideos.php');
		$this->assertEquals(200, $response->getStatusCode());

//		$response = $this->call('GET', '/ajaxBecomeFan.php');
//		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxGetTags.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxVideoViewerCount.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxSetVote.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxGetVotes.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxGetNegVotes.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxGetVote.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxReportVideo.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxUpdateProgramInfo.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxAddProgram.php');
		$this->assertEquals(200, $response->getStatusCode());

//		$response = $this->call('POST', '/ajaxUploadThumbnail.php');
//		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxUpdateUserpic.php');
//		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxGetIsFollow.php');
		$this->assertEquals(200, $response->getStatusCode());

		$response = $this->call('GET', '/ajaxUpdateUserSettings.php');
		$this->assertEquals(200, $response->getStatusCode());
	}	
*/
}
?>
