<?php
namespace Database\Tests\Query;

use Database\Query\SelectQuery,
	Database\Table\GenericTable,
	Database\Tests\TestDb;

class SelectQueryTest extends \PHPUnit_Framework_Testcase
{
	public function testInitialConditions()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from("datacenters");
		
		$this->assertEquals("datacenters", $query->table()->name());
	}
	
	public function testFromWithArray()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from(["datacenters", "datacenter"]);
		
		$this->assertEquals("datacenters", $query->table()->name());
		$this->assertEquals("datacenter", $query->table()->alias());
	}
	
	public function testWhere()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from("datacenters");
		$query->where([
			"id" => 1
		]);
		
		$this->assertCount(1, $query->whereGroups());
	}
	
	public function testGroupBy()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from("datacenters");
		$query->groupBy("name");
		
		$this->assertCount(1, $query->groupings());
	}
	
	public function testOrderBy()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from("datacenters");
		$query->orderBy("displayName")->asc();
		
		$this->assertCount(1, $query->orderings());
	}
	
	public function testLimit()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from("datacenters");
		$query->limit(1);
		
		$this->assertEquals(1, $query->maxResults());
		$this->assertEquals(0, $query->resultOffset());
	}
	
	public function testMatchingTables()
	{
		$query = new SelectQuery(TestDb::pdo());
		$query->from("users");
		$joinExpr = $query->join("roles", "id")->on("users", "roleId");
		
		$this->assertSame($query->table(), $joinExpr->localTable());
	}
	
	public function testQueryWithMapAndStructure()
	{
		$db = TestDb::pdo();
		
		$serverTable = new GenericTable("servers", $db);
		$serverTable->structure()->columns([
			"id" => [
				"alias" => "serverId",
				"type" => "int"
			],
			"gameId" => [
				"alias" => "serverGameId",
				"type" => "int"
			],
			"ip" => [
				"alias" => "serverIp",
				"type" => "varchar"
			]
		]);
		
		$gameTable = new GenericTable("games", $db);
		$gameTable->structure()->columns([
			"id" => [
				"alias" => "gameId",
				"type" => "int"
			],
			"name" => [
				"alias" => "gameName",
				"type" => "varchar"
			]
		]);
		
		$serverQuery = $serverTable->select();
		$serverQuery->hasOne("game", $gameTable, "gameId", "id");
		
		$server = $serverQuery->fetchRow();
		$game = $server->getSet("game");
		
		
		//print_r($server);
		
		//$this->assertTrue(isset($server["serverId"]));
		$this->assertTrue(isset($game));
	}
}