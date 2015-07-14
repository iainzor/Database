<?php
namespace Database\Reference;

use Database\Table\AbstractTable,
	Database\Table\GenericTable,
	Database\Query\SelectQuery,
	Database\PDO;

class TableReference implements ReferenceInterface
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * Constructor
	 * 
	 * @param string|AbstractTable $table
	 * @param PDO $db Required if the $table passed is a string or does not have a database instance assigned
	 */
	public function __construct($table, PDO $db = null)
	{
		$this->table($table, $db);
	}
	
	/**
	 * Get or set the table to reference
	 * 
	 * @param string|AbstractTable $table
	 * @param PDO $db
	 * @return AbstractTable
	 * @throws \Exception
	 */
	public function table($table = null, PDO $db = null)
	{
		if ($table !== null) {
			if (is_string($table)) {
				$table = new GenericTable($table, $db);
			}
			if (!($table instanceof AbstractTable)) {
				throw new \Exception("Table must be a string or an instance of \\Database\\Table\\AbstractTable");
			}
			
			$this->table = $table;
		}
		return $this->table;
	}
	
	/**
	 * Find all results from a table using a set of conditions
	 * 
	 * @param array $conditions
	 * @return array
	 */
	public function findAll(array $conditions) 
	{
		if (!$this->table->db()) {
			throw new \Exception("No database instance has been given to the referenced table");
		}
		
		$query = new SelectQuery($this->table->db());
		$query->from($this->table);
		$query->where($conditions);
		
		return $query->fetchAll();
	}
}