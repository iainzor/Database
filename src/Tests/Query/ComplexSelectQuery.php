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
		$servers = new Table\GenericTable("servers");
		
		$query = new Query\SelectQuery(TestDb::pdo());
		$query->from($players);
		$query->join($servers, "id")->on($players, "serverId");
		$query->where([
			"id > :maxId",
			"name LIKE :name"
		]);
		$query->orWhere([
			"role" => "admin"
		]);
		
		return $query;
	}
}