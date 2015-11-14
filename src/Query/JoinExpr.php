<?php
namespace Database\Query;

use Database\Table\AbstractTable,
	Database\Table\GenericTable;

class JoinExpr
{
	use Traits\WhereTrait;
	
	/**
	 * @var AbstractQuery
	 */
	private $query;
	
	/**
	 * @var AbstractTable
	 */
	private $foreignTable;
	
	/**
	 * @var array
	 */
	private $foreignKeys = [];
	
	/**
	 * @var AbstractTable
	 */
	private $localTable;
	
	/**
	 * @var array
	 */
	private $localKeys = [];
	
	/**
	 * @var array
	 */
	private $columns = [];
	
	/**
	 * @var int
	 */
	private $type = QueryInterface::JOIN_DEFAULT;
	
	/**
	 * Constructor
	 * 
	 * @param AbstractQuery $query The base query the join expression is for
	 * @param string|array|AbstractTable $foreignTable
	 * @param string|array $foreignKeys
	 * @param string|array $localKeys
	 * @param array $columns
	 * @param int $type
	 */
	public function __construct(AbstractQuery $query, $foreignTable, $foreignKeys, $localKeys, array $columns = null, $type = QueryInterface::JOIN_DEFAULT) 
	{
		$this->query = $query;
		
		$this->foreignTable($foreignTable);
		$this->foreignKeys($foreignKeys);
		$this->localKeys($localKeys);
		$this->columns($columns);
		$this->type($type);
	}
	
	/**
	 * Define what table and columns should be joined to
	 * 
	 * @param string|array|AbstractTable $localTable
	 * @param string|array $localKeys
	 * @return JoinExpr
	 */
	public function on($localTable, $localKeys)
	{
		$this->localTable($localTable);
		$this->localKeys($localKeys);
		
		return $this;
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
		if (!$this->localTable) {
			$this->localTable = $this->query->table();
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
	 * Get or set the columns to be selected from the foreign table
	 * 
	 * @param array $columns
	 * @return array
	 */
	public function columns(array $columns = null)
	{
		if ($columns !== null) {
			$this->columns = $columns;
		}
		return $this->columns;
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
	 * Create a table from the argument provided.  If the table provided
	 * matches the base query's table, that table will be returned instead
	 * 
	 * @param string|array|AbstractTable $table
	 * @return AbstractTable
	 * @throws \UnexpectedValueException
	 */
	private function _createTable($table)
	{
		if (is_string($table)) {
			$table = new GenericTable($table, $this->query->db());
		} else if (is_array($table)) {
			$name = array_shift($table);
			$alias = empty($table) ? $name : array_shift($table);
			$table = new GenericTable($name, $this->query->db());
			$table->alias($alias);
		}
		
		if (!($table instanceof AbstractTable)) {
			throw new \UnexpectedValueException("Table must be a string, array or instance of \\Database\\Table\\AbstractTable");
		}
		
		if ($table->alias() === $this->query->table()->alias()) {
			return $this->query->table();
		}
		
		return $table;
	}
}