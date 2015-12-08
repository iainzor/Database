<?php
namespace Database\Tests\Driver\Mysql;

use Database\Driver\Mysql\DriverFactory,
	Database\Table\Structure,
	Database\Table\GenericTable,
	Database\Tests\TestDb;

class StructurePopulateTest extends \PHPUnit_Framework_TestCase
{
	public function testPopulateStructure()
	{
		$pdo = TestDb::pdo();
		$table = new GenericTable("games", $pdo);
		$factory = new DriverFactory();
		$structure = new Structure();
		$factory->populateStructure($pdo, $table, $structure);
		
		$this->assertCount(2, $structure->columns());
	}
}