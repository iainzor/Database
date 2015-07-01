<?php
namespace Database\Tests\Relation;

use Database\Table\GenericTable,
	Database\Relation\RelationMap;

class RelationTest extends \PHPUnit_Framework_TestCase
{
	public function testHasOne()
	{
		$players = new GenericTable("players");
		$servers = new GenericTable("servers");
		
		$map = new RelationMap($players);
		$map->hasOne("server", $servers, "serverId", "id");
		
		$this->assertCount(1, $map->relations());
	}
}