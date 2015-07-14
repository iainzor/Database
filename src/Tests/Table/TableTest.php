<?php
namespace Database\Tests\Table;

use Database\Table\GenericTable,
	Database\Tests\TestDb;

class TableTest extends \PHPUnit_Framework_TestCase
{
	public function testGenericTable()
	{
		$db = TestDb::pdo();
		$table = new GenericTable("my_table", $db);
		
		$this->assertEquals("my_table", $table->name());
		$this->assertEquals("my_table", $table->alias());
	}
}