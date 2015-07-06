<?php
namespace Database\Tests\Relation;

use Database\Table\GenericTable,
	Database\Query,
	Database\Relation\RelationMap,
	Database\Tests\TestDb;

class RelationTest extends \PHPUnit_Framework_TestCase
{
	public function testHasOne()
	{
		$db = TestDb::pdo();
		$servers = new GenericTable("servers");
		
		$map = new RelationMap($db);
		$map->hasOne("server", $servers, "serverId", "id");
		
		$this->assertCount(1, $map->relations());
	}
	
	public function testApplyToRow()
	{
		$playersTable = new GenericTable("players");
		$serversTable = new GenericTable("servers");
		$gamesTable = new GenericTable("games");
		
		$db = TestDb::pdo();
		$query = new Query\SelectQuery($db);
		$query->from($serversTable);
		$query->where([
			"id" => 1
		]);
		
		$map = new RelationMap($db);
		$map->hasMany("players", $playersTable, "id", "serverId");
		$map->hasOne("game", $gamesTable, "gameId", "id");
		
		$server = $map->applyToRow(
			$query->fetchRow()
		);
		
		$this->assertCount(2, $server["players"]);
		$this->assertNotNull($server["game"]);
	}
	
	public function testApplyToRowset()
	{
		$playersTable = new GenericTable("players");
		$serversTable = new GenericTable("servers");
		$db = TestDb::pdo();
		
		$query = new Query\SelectQuery($db);
		$query->from($playersTable);
		
		$players = $query->fetchAll();
		$map = new RelationMap($db);
		$map->hasOne("server", $serversTable, "serverId", "id");
		
		$mapped = $map->applyToRowset($players);
	}
}