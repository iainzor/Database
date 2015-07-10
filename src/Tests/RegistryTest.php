<?php
namespace Database\Tests;

use Database\Registry,
	Database\Config;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaultConnection()
	{
		$testDb = TestDb::pdo();
		$registry = new Registry();
		$registry->set("my-connection", $testDb);
		
		$db = $registry->defaultConnection();
		
		$this->assertEquals($testDb, $db);
	}
	
	public function testDefaultConnectionByName()
	{
		$testDb = TestDb::pdo();
		$registry = new Registry();
		$registry->set("my-connection", $testDb);
		
		$db = $registry->get(Config::DEFAULT_CONNECTION);
		
		$this->assertEquals($testDb, $db);
	}
}