<?php
//require require __DIR__.'/../bootstrap/autoload.php';
//$app = require __DIR__.'/../bootstrap/app.php';
class ProgramTest extends TestCase{
	public function testCreateProgram()
	{
		$this->createApplication();
		echo \App\User;
		$user_data = [
			'name' => 'testuser',
			'email' => 'email@email.com',
			'password' => 'test',
			'activation_key' => 'testkey',
			'url' => 'http://testurl.com',
			'display_name' => 'testusername',
			'piscure_path' => 'http://testpicturepath.com',	
			'status'=>'teststatus'
		];

//		$user = User::create($user_data);
		$program_data = [
			'channel_id' => 10, 
			//'user_id' => $user->id,
			'title' => 'test title',
			'image_path' => 'http://test.com/nice.jpg',	
			'vod_enable' => false,
			'vod_path' => 'test/path',	
			'latitdue' => 31.1,
			'longitude' => 123,
			'start_time' => 'test_start',
			'end_time'=>'test_end',
			'description'=>'this is a test program',
			'connect_count'=>2,
			'isMobile'=>false	
		];
		$program = \App\Program::create($program_data);
		$this->assertNotNull($program);
	}
}

