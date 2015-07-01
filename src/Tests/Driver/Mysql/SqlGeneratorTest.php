<?php
namespace Database\Tests\Driver;

use Database\Driver\Mysql\DriverFactory,
	Database\Tests;

class SqlGeneratorTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateSelectStatement()
	{
		$factory = new DriverFactory();
		$query = Tests\Query\ComplexSelectQuery::create();
		$sql = $factory->sqlGenerator()->generate($query);
		$cleaned = preg_replace("/\n/", " ", $sql);
		$expected = "SELECT * FROM `players` AS `players` "
				  . "JOIN `servers` AS `servers` ON `players`.`serverId` = `servers`.`id` "
				  . "WHERE (id > :maxId AND name LIKE :name) OR (role = 'admin')";
		
		$this->assertEquals($expected, $cleaned);
	}
}