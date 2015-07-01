<?php
namespace Database\Tests\Query;

use Database\Query\SelectQuery;

class SelectQueryTest extends \PHPUnit_Framework_Testcase
{
	public function testInitialConditions()
	{
		$query = new SelectQuery();
		$query->from("datacenters");
		
		$this->assertEquals("datacenters", $query->table()->name());
	}
	
	public function testFromWithArray()
	{
		$query = new SelectQuery();
		$query->from(["datacenters", "datacenter"]);
		
		$this->assertEquals("datacenters", $query->table()->name());
		$this->assertEquals("datacenter", $query->table()->alias());
	}
	
	public function testWhere()
	{
		$query = new SelectQuery();
		$query->from("datacenters")->where([
			"id" => 1
		]);
		
		$this->assertCount(1, $query->whereGroups());
	}
	
	public function testGroupBy()
	{
		$query = new SelectQuery();
		$query->from("datacenters")->groupBy("name");
		
		$this->assertCount(1, $query->groupings());
	}
	
	public function testOrderBy()
	{
		$query = new SelectQuery();
		$query->from("datacenters")->orderBy([
			"displayName" => SelectQuery::SORT_ASC
		]);
		
		$this->assertCount(1, $query->orderings());
	}
	
	public function testLimit()
	{
		$query = new SelectQuery();
		$query->from("datacenters")->limit(1);
		
		$this->assertEquals(1, $query->maxResults());
		$this->assertEquals(0, $query->resultOffset());
	}
}