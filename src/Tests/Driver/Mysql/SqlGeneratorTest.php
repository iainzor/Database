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
		$expected = "SELECT `players`.*, `suspensions`.`id` AS `suspensionId`, `suspensions`.`date` AS `date` "
				  . "FROM `bliss_database_tests`.`players` AS `players` "
				  . "JOIN `bliss_database_tests`.`servers` AS `servers` ON (`players`.`serverId` = `servers`.`id`) "
				  . "LEFT JOIN `bliss_database_tests`.`suspensions` AS `suspensions` ON (`players`.`id` = `suspensions`.`playerId`) "
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
	
	public function testCreateUpdateStatement()
	{
		$factory = new DriverFactory();
		$db = TestDb::pdo();
		$query = new Query\UpdateQuery($db);
		$query->table("my_table");
		$query->values([
			"foo" => "bar",
			"bar" => "baz"
		]);
		$query->where(["id" => 123]);
		$sql = $factory->sqlGenerator()->generate($query);
		$cleaned = $this->_clean($sql);
		$expected = "UPDATE `my_table` "
				  . "SET `foo` = 'bar', `bar` = 'baz' "
				  . "WHERE (`my_table`.`id` = '123')";
		
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
		return preg_replace("/\n/", " ", 
			preg_replace("/\s{2,}/", " ", trim($sql))
		);
	}
}