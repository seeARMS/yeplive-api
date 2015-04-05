<?php
const DB_HOST = "yeplive-dev.czc6detrzhw4.us-west-2.rds.amazonaws.com:3306";
const DOMAIN_ROOT = "example.com";
const DB_USER = "root";
const DB_PASSWORD = "rootroot";
const DB_NAME = "test";
class DatabaseTest extends TestCase{
	public function testDatabaseConnection()
	{
		//Make connection time 5 seconds
		ini_set("mysql.connect_timeout","5");
		error_reporting(E_ALL ^ E_DEPRECATED);
		$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
		$this->assertNotNull($link);
	}
}
?>
