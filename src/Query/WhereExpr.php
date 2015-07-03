<?php
namespace Database\Query;

use Database\PDO,
	Database\Table\AbstractTable;

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
	 * @param AbstractQuery $query
	 * @param mixed $expr
	 */
	public function __construct(AbstractQuery $query, $expr)
	{
		$this->query = $query;
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
	
	private function _processArray(array $parts, PDO $db)
	{
		if (count($parts) < 2) {
			throw new \Exception("Can't build WHERE expression with less than 2 parts: ". json_encode($parts));
		}
		
		$column = $this->_columnName($this->expr[0]);
		$operator = isset($this->expr[2]) ? $this->expr[1] : "=";
		$value = isset($this->expr[2]) ? $this->expr[2] : $this->expr[1];

		if (substr($value, 0, 1) !== ":") {
			$value = $db->quote($value);
		}

		return implode(" ", [$column, $operator, $value]);
	}
	
	/**
	 * Generate a fully qualified column name
	 * If $str = "mytable.foo" then the method will return "`mytable`.`foo`"
	 * If no table is specified, the table from the query provided to the constructor will be used
	 * 
	 * @param string $str
	 * @return string
	 */
	private function _columnName($str)
	{
		if (preg_match("/^`?([^`]+)`?\.?`?([^`]+)?`?$/", $str, $matches)) {
			if (empty($matches[2])) {
				$columnName = $matches[1];
				$tableName = $this->query->table()->alias();
			} else {
				$tableName = $matches[1];
				$columnName = $matches[2];
			}
			
			$str = "`{$tableName}`.`{$columnName}`";
		}
		
		return $str;
	}
}