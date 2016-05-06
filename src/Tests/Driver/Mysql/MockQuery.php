<?php
namespace Database\Tests\Driver\Mysql;

use Database\Table,
	Database\Query,
	Database\Tests\TestDb,
	Database\Table\Structure;

class MockQuery
{
	public static function create()
	{
		$db = TestDb::pdo();
		$players = new Table\GenericTable("players", $db);
		$players->structure(new Structure([
			"role" => [
				"type" => "varchar"
			]
		]));
		
		$servers = new Table\GenericTable("servers", $db);
		$servers->structure(new Structure());
		
		$suspensions = new Table\GenericTable("suspensions", $db);
		$suspensions->structure(new Structure());
		
		$query = new Query\SelectQuery(TestDb::pdo());
		$query->from($players);
		$query->columns(["*"]);
		$query->join($servers, "id", "serverId");
		$query->leftJoin($suspensions, "playerId", "id", [
			"id" => "suspensionId",
			"date"
		]);
		$query->where([
			["id", ">", ":maxId"],
			["name", "LIKE", ":name"],
			[$servers->column("id"), "IS NOT NULL"]
		]);
		$query->orWhere([
			"role" => "admin"
		]);
		$query->groupBy($servers->column("id"));
		$query->orderBy($servers->column("name"))->asc();
		$query->orderBy("name")->desc();
		$query->limit(100, 50);
		
		return $query;
	}
}