<?php
namespace Database\Tests\Table;

use Database\Table\Column;

class ColumnTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialConditions()
	{
		$column = new Column("my_column");
		
		$this->assertEquals("my_column", $column->name());
		$this->assertEquals("my_column", $column->alias());
	}
	
	public function testFactoryMethod()
	{
		$column = Column::factory([
			"name" => "my_column",
			"type" => "varchar",
			"length" => 8
		]);
		
		$this->assertInstanceOf("\\Database\\Table\\ValueType\\VarcharType", $column->type());
		$this->assertEquals(8, strlen($column->parseValue("this is a long column value")));
	}
}