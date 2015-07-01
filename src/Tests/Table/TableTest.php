<?php
namespace Database\Tests\Table;

use Database\Table\GenericTable;

class TableTest extends \PHPUnit_Framework_TestCase
{
	public function testGenericTable()
	{
		$table = new GenericTable("my_table");
		
		$this->assertEquals("my_table", $table->name());
		$this->assertEquals("my_table", $table->alias());
	}
}