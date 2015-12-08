<?php
namespace Database\Driver;

use Database\Table,
	Database\PDO;

interface DriverFactoryInterface
{
	/**
	 * @return SqlGeneratorInterface
	 */
	public function sqlGenerator();
	
	/**
	 * @param \Database\PDO
	 * @param \Database\Table\AbstractTable $table
	 * @param \Database\Table\Structure $structure
	 */
	public function populateStructure(PDO $db, Table\AbstractTable $table, Table\Structure $structure);
}