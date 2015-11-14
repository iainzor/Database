<?php
namespace Database\Query;

use Database\Table,
	Database\PDO;

abstract class AbstractQuery implements QueryInterface
{
	/**
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * @var \Database\Table\AbstractTable
	 */
	protected $table;
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db($db);
	}
	
	/**
	 * Get or set the PDO instance
	 * 
	 * @param PDO $db
	 * @return PDO
	 */
	public function db(PDO $db = null)
	{
		if ($db) {
			$this->db = $db;
		}
		return $this->db;
	}
	
	/**
	 * Get or set the table to run the query on
	 * 
	 * @param mixed $table	This can either be a string of the table's name, an array as [tableName, tableAlias] 
	 *						or an instance of \Database\Table\AbstractTable
	 * @return \Database\Table\AbstractTable
	 */
	public function table($table = null)
	{
		if ($table !== null) {
			if ($table instanceof Table\AbstractTable) {
				$this->table = $table;
			} else if (is_string($table)) {
				$this->table = new Table\GenericTable($table, $this->db());
			} else if (is_array($table)) {
				$tableName = $table[0];
				$tableAlias = isset($table[1]) ? $table[1] : null;
				$this->table = new Table\GenericTable($tableName, $this->db());
				$this->table->alias($tableAlias);
			} else {
				throw new \InvalidArgumentException("Could not create table instance from passed value");
			}
			
			if (!$this->table->db()) {
				$this->table->db($this->db());
			}
		}
		
		return $this->table;
	}
	
	/**
	 * Generate a SQL statement from the query
	 * 
	 * @throws \Exception
	 * @return string
	 */
	public function generateSQL()
	{
		if (!$this->db()) {
			throw new \Exception("No database instance has been given to the query");
		}
		
		$driverFactory = $this->db()->driverFactory();
		return $driverFactory->sqlGenerator()->generate($this);
	}
}