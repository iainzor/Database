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
				  . "WHERE (`players`.`id` > :maxId AND `players`.`name` LIKE :name AND `servers`.`id` IS NOT NULL) OR (`players`.`role` = 'admin') "
				  //. "GROUP BY `servers`.`id` "
				  . "ORDER BY `servers`.`name` ASC, `players`.`name` DESC";
		
		$this->assertEquals($expected, $cleaned);
	}
}