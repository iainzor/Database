<?php
namespace Database\Tests\Driver\Mysql;

use Database\Driver\Mysql\DriverFactory;

class SqlGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateSelectStatement()
	{
		$factory = new DriverFactory();
		$query = MockQuery::create();
		$sql = $factory->sqlGenerator()->generate($query);
		$cleaned = preg_replace("/\n/", " ", $sql);
		$expected = "SELECT * FROM `players` AS `players` "
				  . "JOIN `servers` AS `servers` ON `players`.`serverId` = `servers`.`id` "
				  . "LEFT JOIN `suspensions` AS `suspensions` ON `players`.`id` = `suspensions`.`playerId` "
				  . "WHERE (id > :maxId AND name LIKE :name) OR (role = 'admin') "
				  . "ORDER BY `players`.`name` ASC";
		
		$this->assertEquals($expected, $cleaned);
	}
}