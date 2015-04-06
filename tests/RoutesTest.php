<?php
class RoutesTest extends TestCase{
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
}
?>
