<?php
namespace Database\Driver\Mysql;

use Database\Driver\DriverFactoryInterface,
	Database\Table,
	Database\PDO;

class DriverFactory implements DriverFactoryInterface
{
	/**
	 * @return \Database\Driver\Mysql\SqlGenerator
	 */
	public function sqlGenerator() 
	{
		return new SqlGenerator();
	}
	
	/**
	 * @param PDO $db
	 * @param \Database\Table\AbstractTable $table
	 * @param \Database\Table\Structure $structure
	 */
	public function populateStructure(PDO $db, Table\AbstractTable $table, Table\Structure $structure) 
	{
		$info = $db->fetchAll("DESCRIBE `". $table->name() ."`");
		
		foreach ($info as $field) {
			$name = $field["Field"];
			$type = preg_replace("/^([^\(]+).*$/", "$1", $field["Type"]);
			
			$structure->column($name, [
				"type" => $type
			]);
		}
	}
}