<?php
namespace Database\Table;

use Database\Query\SelectQuery,
	Database\PDO;

class VirtualTable extends AbstractTable
{
	/**
	 * @var string
	 */
	private $tableName;
	
	/**
	 * @var SelectQuery
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param string $tableName
	 * @param SelectQuery $query
	 */
	public function __construct($tableName, SelectQuery $query, PDO $db = null)
	{
		if ($db === null) {
			$db = $query->db();
		}
		
		parent::__construct($db);
		
		$this->tableName = $tableName;
		$this->query = $query;
		
		$this->initStructure();
	}
	
	private function initStructure()
	{
		$structure = new Structure();
		foreach ($this->query->columns() as $columnName => $columnAlias) {
			if (!is_numeric($columnName)) {
				$columnName = $columnAlias;
			}
			
			$structure->column($columnName, [
				"type" => "varchar"
			]);
		}
		$this->structure($structure);
	}
	
	/**
	 * @return string
	 */
	public function defaultName() { return $this->tableName; }
	
	/**
	 * Get or set the query used to build the virtual table
	 * 
	 * @param SelectQuery $query
	 * @return SelectQuery
	 */
	public function query(SelectQuery $query = null)
	{
		if ($query !== null) {
			$this->query = $query;
		}
		return $this->query;
	}
}