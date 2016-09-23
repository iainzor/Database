<?php
namespace Database\Query;

use Database\Table,
	Database\PDO,
	Database\Model,
	Bliss\Component;

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
	 * @param mixed $table
	 * @return \Database\Table\AbstractTable
	 * @see \Database\Table\AbstractTable::factory()
	 */
	public function table($table = null)
	{
		if ($table !== null) {
			$this->table = Table\AbstractTable::factory($table, $this->db);
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
		if ($driverFactory) {
			return $driverFactory->sqlGenerator()->generate($this);
		}
	}
	
	/**
	 * Generate a new model instance from a single row of data
	 * 
	 * @param array|Model\AbstractModel $data
	 * @param boolean $ignoreStructure Whether to ignore the base table's structure when parsing values
	 * @return Model\AbstractModel
	 */
	public function generateModel($data, $ignoreStructure = false)
	{
		$table = $this->table();
		$data = Component::convertValueToArray($data);
		
		if (!$table) {
			throw new \Exception("Cannot generate a model without a table instance");
		}
		
		$structure = $table->structure();
		$parsed = [];
		
		foreach ($data as $name => $value) {
			if ($structure->isColumn($name) || $ignoreStructure) {
				$column = $structure->column($name);
				$parsed[$column->alias()] = $value;
			}
		}

		if ($table instanceof Model\ModelGeneratorInterface) {
			return $table->generateModel($parsed);
		} else {
			$model = new Model\GenericModel($table->alias());
			return Model\GenericModel::populate($model, $parsed);
		}
	}
	
	/**
	 * Attempt to find a column in the query
	 * 
	 * @param string $columnName
	 * @return Table\Column
	 */
	public function findColumn($columnName) 
	{
		$column = $this->_findColumn($columnName, $this->table());
		if (!$column && $this instanceof SelectQuery) {
			foreach ($this->joins() as $join) {
				$column = $this->_findColumn($columnName, $join->foreignTable());
				if ($column) {
					break;
				}
			}
		}
		
		if (!$column) {
			$column = new Table\Column($columnName, $this->table());
		}
		
		return $column;
	}
	
	/**
	 * Attempt to find a column in a table
	 * 
	 * @param string $columnName
	 * @param Table\AbstractTable $table
	 * @return Column|false
	 */
	private function _findColumn($columnName, Table\AbstractTable $table) 
	{
		$columns = $table->structure()->columns();
		$col = false;
		
		foreach ($columns as $column) {
			$name = null;
			$alias = null;
			
			if ($column instanceof Table\Column) {
				$name = $column->name();
				$alias = $column->alias();
				$_col = $column;
			} else if (is_string($column)) {
				$name = $column;
				$alias = $name;
				$_col = new Table\Column($name, $table);
			} else if (is_array($column)) {
				$keys = array_keys($column);
				
				if (!is_numeric($keys[0])) {
					$name = $keys[0];
					$alias = $column[$name];
				} else {
					$name = $column[0];
					$alias = $name;
				}
			} else {
				continue;
			}
			
			if ($columnName === $name || $columnName === $alias) {
				$col = $_col;
				break;
			}
		}
		
		if ($col !== false) {
			$col->table($table);
		}
		
		return $col;
	}
}