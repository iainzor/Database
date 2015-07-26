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
		if (!isset($this->columns[$name])) {
			$this->columns[$name] = new Column($name);
		}
		return $this->columns[$name];
	}
	
	/**
	 * Parse a single row according to the defined structure
	 * 
	 * @param array|object $row
	 * @return array|object
	 */
	public function parseRow($row, RelationMap $map)
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
			} else if ($map->relationExists($name)) {
				var_dump($name);
				$relation = $map->relation($name);
				$structure = $relation->reference()->structure();
				$parsed[$alias] = $structure->parseRow($value, $relation->relationMap());
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
	public function parseRowset(array $rows, RelationMap $map)
	{
		foreach ($rows as $i => $row) {
			$rows[$i] = $this->parseRow($row, $map);
		}
		return $rows;
	}
}