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
	
	public function testConnectionFromConfig()
	{
		$registry = new Registry();
		$registry->set("my-connection", [
			Config::CONF_DSN => "mysql:host=127.0.0.1;dbname=bliss_database_tests",
			Config::CONF_USER => "root"
		]);
		
		$db = $registry->get("my-connection");
		
		$this->assertInstanceOf("\\Database\\PDO", $db);
	}
	
	public function testDefaultConnectionFromConfig()
	{
		$registry = new Registry();
		$registry->defaultConnection([
			Config::CONF_DSN => "mysql:host=127.0.0.1;dbname=bliss_database_tests",
			Config::CONF_USER => "root"
		]);
		
		$db = $registry->defaultConnection();
		
		$this->assertInstanceOf("\\Database\\PDO", $db);
	}
}