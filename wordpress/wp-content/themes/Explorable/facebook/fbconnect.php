<?php

include ('facebook_lib/facebook.php');

class Fbconnect extends Facebook{
	
	public $user = null;
	public $user_id = null;
	public $fb = false;
	public $fbSession = false;
	public $appkey = 0;
	
	public function Fbconnect()
	{
		$config = array (
				'appId' => '1432005050393473',
				'secret' => '7a7d10a0ffc99f1ed1514df06fbf951e'
		);
		
		parent::__construct($config);
		
		$this->user_id = $this->getUser();
		$me = null;
		if ($this->user_id)
		{
			try {
				$me = $this->api('/me');
				$this->user = $me;
			} catch (FacebookApiException $e) {
				error_log($e);
			}
		}
		
	}
	
	public function send_back($value) 
	{
		return $value;
	}
	
	public function test()
	{
		$ci =& get_instance();
		
		$ci->load->helper('url');
		
		echo base_url();
	}
}