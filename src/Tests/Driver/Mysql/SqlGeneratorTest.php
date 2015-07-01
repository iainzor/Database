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
		
		$this->assertNotEmpty($cleaned);
	}
}