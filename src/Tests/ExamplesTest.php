<?php
namespace Database\Tests;

use Database\PDO,
	Database\Query;

class ExamplesTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \Database\PDO
	 */
	private $db;
	
	public function setUp()
	{
		$this->db = new PDO("mysql:host=127.0.0.1;dbname=bliss_database_tests", "root", null, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		]);
	}
	
	public function testClassicPDO()
	{
		$statement = $this->db->prepare("SELECT * FROM `players` WHERE `id` = :id");
		$statement->execute([
			":id" => 1
		]);
		$player = $statement->fetch(PDO::FETCH_ASSOC);
		
		$this->assertEquals("iain.zor", $player["name"]);
	}
	
	public function testExtendedPDO()
	{
		$player = $this->db->fetchRow("SELECT * FROM `players` WHERE `id` = :id", [
			":id" => 1
		], PDO::FETCH_OBJ);
		
		$this->assertEquals("iain.zor", $player->name);
	}
	
	public function testBasicSelectQuery()
	{
		$query = new Query\SelectQuery($this->db);
		$query->from("players");
		$query->where([
			"id" => ":id"
		]);
		
		$players = $query->fetchAll([
			":id" => 1
		], PDO::FETCH_OBJ);
		
		$this->assertNotEmpty($players);
		
		$iainzor = $players[0];
		
		$this->assertEquals("iain.zor", $iainzor->name);
	}
}