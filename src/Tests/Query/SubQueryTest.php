<?php
namespace Database\Tests\Query;

use Database\Query\SelectQuery,
	Database\Table\VirtualTable,
	Database\Tests\TestDb;

class SubQueryTest extends \PHPUnit_Framework_TestCase
{
	public function testSubQueryAsJoinedTable()
	{
		$db = TestDb::pdo();
		
		$baseQuery = new SelectQuery($db);
		$baseQuery->from("users");
		
		$subQuery = new SelectQuery($db);
		$subQuery->from("admins");
		$subQuery->columns(["id", "userId"]);
		
		$baseQuery->join(new VirtualTable("admins", $subQuery), "userId", "id", [
			"id" => "adminId"
		]);
		
		
	}
}