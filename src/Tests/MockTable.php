<?php
namespace Database\Tests;

use Database\Table\AbstractTable,
	Database\Table\Structure,
	Database\PDO;

class MockTable extends AbstractTable
{
	public function defaultName() { return "my_table"; }
	
	public static function factory(PDO $db)
	{
		$table = new MockTable($db);
		$structure = new Structure([
			"foo" => [
				"type" => "varchar"
			],
			"bar" => [
				"type" => "varchar"
			],
			"baz" => [
				"type" => "varchar"
			]
		]);
		$table->structure($structure);
		
		return $table;
	}
}