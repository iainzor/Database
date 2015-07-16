<?php
namespace Database\Tests\Driver\Mysql;

use Database\Driver\Mysql\DriverFactory,
	Database\Query,
	Database\Tests\TestDb;

class SqlGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateSelectStatement()
	{
		$factory = new DriverFactory();
		$query = MockQuery::create();
		$sql = $factory->sqlGenerator()->generate($query);
		$cleaned = $this->_clean($sql);
		$expected = "SELECT * FROM `players` AS `players` "
				  . "JOIN `servers` AS `servers` ON `players`.`serverId` = `servers`.`id` "
				  . "LEFT JOIN `suspensions` AS `suspensions` ON `players`.`id` = `suspensions`.`playerId` "
				  . "WHERE (`players`.`id` > :maxId AND `players`.`name` LIKE :name AND `servers`.`id` IS NOT NULL) OR (`players`.`role` = 'admin') "
				  . "GROUP BY `servers`.`id` "
				  . "ORDER BY `servers`.`name` ASC, `players`.`name` DESC "
				  . "LIMIT 100 OFFSET 50";
		
		$this->assertEquals($expected, $cleaned);
	}
	
	public function testCreateInsertStatement()
	{
		$factory = new DriverFactory();
		$db = TestDb::pdo();
		$query = new Query\InsertQuery($db);
		$query->into("my_table");
		$query->rows([
			[
				"foo" => "bar",
				"bar" => "baz"
			], [
				"foo" => "baz",
				"bar" => "foo",
				"baz" => "blah"
			]
		]);
		$query->onDuplicateKeyUpdate([
			"foo",
			"bar" => "foo",
			"baz" => null
		]);
		$sql = $factory->sqlGenerator()->generate($query);
		$cleaned = $this->_clean($sql);
		$expected = "INSERT INTO `my_table` "
				  . "(`foo`, `bar`, `baz`) VALUES "
				  . "('bar', 'baz', NULL), ('baz', 'foo', 'blah') "
				  . "ON DUPLICATE KEY UPDATE "
				  . "`foo` = VALUES(`foo`), `bar` = 'foo', `baz` = NULL";
		
		$this->assertEquals($expected, $cleaned);
	}
	
	/**
	 * Clean a SQL string
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function _clean($sql)
	{
		return preg_replace("/\n/", " ", $sql);
	}
}