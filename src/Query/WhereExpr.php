<?php
namespace Database\Query;

use Database\PDO,
	Database\Table\Column;

class WhereExpr
{
	/**
	 * @var AbstractQuery
	 */
	private $query;
	
	/**
	 * @var mixed
	 */
	private $expr;
	
	/**
	 * Constructor
	 * 
	 * @param mixed $expr
	 */
	public function __construct($expr)
	{
		$this->expr = $expr;
	}
	
	/**
	 * @param \Database\PDO
	 * @return string
	 */
	public function toString(PDO $db)
	{
		if (is_array($this->expr)) {
			return $this->_processArray($this->expr, $db);
		}
		
		return $this->expr;
	}
	
	/**
	 * Process an expression made of array parts
	 * 
	 * @param array $parts
	 * @param PDO $db
	 * @return array
	 * @throws \Exception
	 */
	private function _processArray(array $parts, PDO $db)
	{
		if (count($parts) < 2) {
			throw new \Exception("Can't build WHERE expression with less than 2 parts: ". json_encode($parts));
		}
		
		$column = $this->_columnName($this->expr[0]);
		$operator = $this->expr[1];
		$result = [$column, $operator];
		
		if (isset($this->expr[2])) {
			$value = $this->expr[2];
			if (substr($value, 0, 1) !== ":") {
				$value = $db->quote($value);
			}
			$result[] = $value;
		}

		return implode(" ", $result);
	}
	
	/**
	 * Generate a fully qualified column name
	 * If $str = "mytable.foo" then the method will return "`mytable`.`foo`"
	 * If no table is specified, the table from the query provided to the constructor will be used
	 * 
	 * @param string|Column $column
	 * @return string
	 */
	private function _columnName($column)
	{
		if ($column instanceof Column) {
			$tableName = $column->table() ? $column->table()->alias() : $this->query->table()->alias();
			$columnName = $column->name();
			return "`". $tableName ."`.`". $columnName ."`";
		} else if (preg_match("/^`?([a-z0-9-_]+)`?\.?`?([a-z0-9-_]+)?`?$/i", $column, $matches)) {
			if (empty($matches[2])) {
				$columnName = $matches[1];
				$tableName = $this->query->table()->alias();
			} else {
				$tableName = $matches[1];
				$columnName = $matches[2];
			}
			
			return "`{$tableName}`.`{$columnName}`";
		}
		
		return $column;
	}
}