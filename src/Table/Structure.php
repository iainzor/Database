<?php
namespace Database\Table;

use Database\Relation\RelationMap;

class Structure 
{
	/**
	 * @var Column
	 */
	private $columns = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $columns
	 */
	public function __construct(array $columns = null)
	{
		$this->columns($columns);
	}
	
	/**
	 * Get or set the columns of the structure
	 * If setting, it will remove any columns currently defined
	 * 
	 * @param array $columns
	 * @return Column[]
	 */
	public function columns(array $columns = null)
	{
		if ($columns !== null) {
			$this->columns = [];
			
			foreach ($columns as $name => $config) {
				$this->column($name, $config);
			}
		}
		return $this->columns;
	}
	
	/**
	 * Get or set a column's configuration
	 * 
	 * @param string $name
	 * @param array $config
	 * @return Column
	 */
	public function column($name, array $config = null)
	{
		if ($config !== null) {
			$config["name"] = $name;
			$column = Column::factory($config);
			$this->columns[$name] = $column;
		}
		
		foreach ($this->columns as $column) {
			if ($column->name() == $name || $column->alias() == $name) {
				return $column;
			}
		}
		
		$this->columns[$name] = new Column($name);
		
		return $this->columns[$name];
	}
	
	/**
	 * Check if a column exists in the structure.  If no columns
	 * have been defined, this method will always return TRUE
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function isColumn($name)
	{
		if (count($this->columns) === 0) {
			return true;
		}
		foreach ($this->columns as $column) {
			if ($column->name() == $name || $column->alias() == $name) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Parse a single row according to the defined structure
	 * 
	 * @param array|object $row
	 * @param RelationMap $map
	 * @return array|object
	 */
	public function parseRow($row, RelationMap $map = null)
	{
		if (empty($this->columns)) {
			return $row;
		}
		
		$parsed = [];
		foreach ($row as $name => $value) {
			if (isset($this->columns[$name])) {
				$column = $this->columns[$name];
				$alias = $column->alias();
				$parsed[$alias] = $column->parseValue($value); 
			} else if ($map && $map->relationExists($name)) {
				$relation = $map->relation($name);
				$structure = $relation->reference()->structure();
				$parsed[$name] = $structure->parseRow($value, $relation->relationMap());
			} else {
				$parsed[$name] = $value;
			}
		}
		
		if (is_object($row)) {
			return (object) $parsed;
		}
		return $parsed;
	}
	
	/**
	 * Parse a set of rows according to the defined structure
	 * 
	 * @param array $rows
	 * @return array
	 */
	public function parseRowset(array $rows, RelationMap $map = null)
	{
		foreach ($rows as $i => $row) {
			$rows[$i] = $this->parseRow($row, $map);
		}
		return $rows;
	}
}