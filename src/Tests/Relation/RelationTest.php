<?php
namespace Database\Tests\Relation;

use Database\Table\GenericTable,
	Database\Query,
	Database\Relation\RelationMap,
	Database\Reference\TableReference,
	Database\Reference\QueryReference,
	Database\Tests\TestDb;

class RelationTest extends \PHPUnit_Framework_TestCase
{
	public function testGenerateMap()
	{
		$contactsQuery = new Query\SelectQuery(TestDb::pdo());
		$usersMap = RelationMap::generate([
			"hasOne" => [
				"client" => [
					"reference" => new TableReference("clients", TestDb::pdo()),
					"localKeys" => "clientId",
					"foreignKeys" => "id",
					"relationMap" => [
						"hasMany" => [
							"contacts" => [
								"reference" => new QueryReference($contactsQuery),
								"localKeys" => "id",
								"foreignKeys" => "resourceId"
							]
						]
					]
				]
			]
		]);
		
		$clientRelation = $usersMap->relation("client");
		
		$this->assertInstanceOf("\\Database\\Relation\\OneToOneRelation", $clientRelation);
		$this->assertNotNull($clientRelation->relation("contacts"));
	}
	
	public function testHasOneForMultipleItems()
	{
		$db = TestDb::pdo();
		$playersQuery = new Query\SelectQuery($db);
		$playersQuery->from("players");
		
		$playersMap = new RelationMap();
		$playersMap->hasOne("server", new TableReference("servers", $db), "serverId", "id");
		
		$players = $playersQuery->fetchAll();
		$mapped = $playersMap->applyToRowset($players);
		
		$this->assertNotEmpty($mapped);
		$this->assertNotNull($mapped[0]["server"]);
	}
}