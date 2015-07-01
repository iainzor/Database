<?php
namespace Database\Query;

use Database\Table\AbstractTable,
	Database\Table\GenericTable;

class JoinExpr
{
	/**
	 * @var AbstractTable
	 */
	private $foreignTable;
	
	/**
	 * @var AbstractTable
	 */
	private $localTable;
	
	/**
	 * @var array
	 */
	private $foreignKeys = [];
	
	/**
	 * @var array
	 */
	private $localKeys = [];
	
	/**
	 * @var int
	 */
	private $type = QueryInterface::JOIN_DEFAULT;
	
	/**
	 * Constructor
	 * 
	 * @param string|array|AbstractTable $foreignTable
	 * @param string|array $foreignKeys
	 * @param int $type
	 */
	public function __construct($foreignTable, $foreignKeys, $type = QueryInterface::JOIN_DEFAULT) 
	{
		$this->foreignTable($foreignTable);
		$this->foreignKeys($foreignKeys);
		$this->type($type);
	}
	
	/**
	 * Define what table and columns should be joined to
	 * 
	 * @param string|array|AbstractTable $localTable
	 * @param string|array $localKeys
	 */
	public function on($localTable, $localKeys)
	{
		$this->localTable($localTable);
		$this->localKeys($localKeys);
	}
	
	/**
	 * Get or set the foreign table used for the join
	 * 
	 * @param string|array|AbstractTable $table
	 * @return AbstractTable
	 */
	public function foreignTable($table = null)
	{
		if ($table !== null) {
			$this->foreignTable = $this->_createTable($table);
		}
		return $this->foreignTable;
	}
	
	/**
	 * Get or set the local table that is being joined onto
	 * 
	 * @param string|array|AbstractTable $table
	 * @return AbstractTable
	 */
	public function localTable($table = null)
	{
		if ($table !== null) {
			$this->localTable = $this->_createTable($table);
		}
		return $this->localTable;
	}
	
	/**
	 * Get or set the keys used to reference the foreign table
	 *  
	 * @param string|array $keys
	 * @return array
	 */
	public function foreignKeys($keys = null)
	{
		if ($keys !== null) {
			$this->foreignKeys = is_array($keys) ? $keys : [$keys];
		}
		return $this->foreignKeys;
	}
	
	/**
	 * Get or set the keys referenced by the local table
	 * 
	 * @param string|array $keys
	 * @return array
	 */
	public function localKeys($keys = null)
	{
		if ($keys !== null) {
			$this->localKeys = is_array($keys) ? $keys : [$keys];
		}
		return $this->localKeys;
	}
	
	/**
	 * Get or set the join type
	 * 
	 * @param int $type
	 * @return int
	 */
	public function type($type = null)
	{
		if ($type !== null) {
			$this->type = (int) $type;
		}
		return $this->type;
	}
	
	/**
	 * Create a table from the argument provided
	 * 
	 * @param string|array|AbstractTable $table
	 * @return AbstractTable
	 * @throws \UnexpectedValueException
	 */
	private function _createTable($table)
	{
		if (is_string($table)) {
			$table = new GenericTable($table);
		} else if (is_array($table)) {
			$name = array_shift($table);
			$alias = empty($table) ? $name : array_shift($table);
			$table = new GenericTable($name);
			$table->alias($alias);
		}
		
		if (!($table instanceof AbstractTable)) {
			throw new \UnexpectedValueException("Table must be a string, array or instance of \\Database\\Table\\AbstractTable");
		}
		
		return $table;
	}
}