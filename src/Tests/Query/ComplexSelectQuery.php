<?php
namespace Database\Tests\Query;

use Database\Table,
	Database\Query,
	Database\Tests\TestDb;

class ComplexSelectQuery
{
	public static function create()
	{
		$players = new Table\GenericTable("players");
		$query = new Query\SelectQuery(TestDb::pdo());
		$query->from($players);
		$query->where([
			"id > :maxId",
			"name LIKE :name"
		]);
		
		return $query;
	}
}