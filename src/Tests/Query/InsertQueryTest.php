<?php
namespace Database\Tests\Query;

use Database\Query\InsertQuery,
	Database\Tests\TestDb;

class InsertQueryTest extends \PHPUnit_Framework_Testcase
{
	public function testBasic()
	{
		$db = TestDb::pdo();
		$query = new InsertQuery($db);
		$query->into("my_table");
		$query->addRow([
			"foo" => "bar"
		]);
		
		$this->assertInstanceOf("\\Database\\Table\\GenericTable", $query->table());
		$this->assertCount(1, $query->rows());
	}
}