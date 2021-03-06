<?php
namespace Database\Tests\Table;

use Database\Table\Structure,
	Database\Table\GenericTable,
	Database\Relation\RelationMap,
	Database\Tests\TestDb;

class StructureTest extends \PHPUnit_Framework_TestCase
{
	public function testInitialConditions()
	{
		$structure = new Structure();
		$data = [
			"foo" => "bar"
		];
		$parsed = $structure->parseRow($data);
		
		$this->assertEquals($data["foo"], $parsed["foo"]);
	}
	
	public function testValueConversion()
	{
		$structure = new Structure();
		$structure->column("id", [
			"type" => "int"
		]);
		$data = [
			"id" => "123"
		];
		$parsed = $structure->parseRow($data);
		
		$this->assertSame(123, $parsed["id"]);
	}
	
	public function testColumnAlias()
	{
		$structure = new Structure();
		$structure->column("first_name", [
			"alias" => "firstName"
		]);
		$parsed = $structure->parseRow([
			"first_name" => "Iain"
		]);
		
		$this->assertTrue(isset($parsed["firstName"]));
		$this->assertFalse(isset($parsed["first_name"]));
	}
	
	public function testMultipleColumns()
	{
		$structure = new Structure();
		$structure->columns([
			"id" => [
				"type" => "int"
			],
			"first_name" => [
				"alias" => "firstName"
			],
			"last_name" => [
				"alias" => "lastName"
			]
		]);
		$parsed = $structure->parseRow([
			"id" => "123",
			"first_name" => "Iain",
			"last_name" => "Edminster"
		]);
		
		$this->assertSame(123, $parsed["id"]);
		$this->assertEquals("Iain", $parsed["firstName"]);
	}
	
	public function testPreserveRelations()
	{
		$barStruct = new Structure([
			"id" => [
				"type" => "int"
			],
			"foo_id" => [
				"alias" => "fooId",
				"type" => "int"
			]
		]);
		$db = TestDb::pdo();
		$foos = new GenericTable("foos", $db);
		$barMap = new RelationMap();
		$barMap->hasOne("foo", $foos, "foo_id", "id");
		
		$data = $barStruct->parseRow([
			"id" => "123",
			"foo_id" => "321",
			"foo" => [
				"id" => 321,
				"bar" => "baz"
			]
		], $barMap);
		
		//var_dump($data);
		//exit;
		
		$this->assertSame($data["fooId"], $data["foo"]["id"]);
	}
}