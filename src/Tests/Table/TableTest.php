<?php
namespace Database\Tests\Table;

use Database\Table\GenericTable,
	Database\Tests\TestDb,
	Database\Registry,
	Database\PDO;

class TableTest extends \PHPUnit_Framework_TestCase
{
	public function testGenericTable()
	{
		$db = TestDb::pdo();
		$table = new GenericTable("my_table", $db);
		
		$this->assertEquals("my_table", $table->name());
		$this->assertEquals("my_table", $table->alias());
	}
	
	public function testDefaultDatabaseConnection()
	{
		$registry = new Registry();
		$defaultDb = TestDb::pdo();
		$registry->defaultConnection($defaultDb);
		
		GenericTable::dbRegistry($registry);
		
		$table = new GenericTable("my_table");
		
		$this->assertEquals($defaultDb, $table->db());
	}
	
	public function testChangeConnectionId()
	{
		$registry = new Registry();
		$defaultDb = TestDb::pdo();
		$secondDb = new PDO("sqlite::memory:");
		
		$registry->defaultConnection($defaultDb);
		$registry->set("memory", $secondDb);
		
		GenericTable::dbRegistry($registry);
		
		$tableA = new GenericTable("my_table");
		$tableB = new GenericTable("my_other_table");
		
		$this->assertEquals($tableA->db(), $tableB->db());
		
		$tableB->connectionId("memory");
		
		$this->assertEquals($secondDb, $tableB->db());
	}
}