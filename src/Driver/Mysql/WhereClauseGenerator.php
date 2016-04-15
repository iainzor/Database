<?php
namespace Database\Driver\Mysql;

use Database\PDO,
	Database\Table\AbstractTable,
	Database\Table\Column,
	Database\Query\WhereGroup,
	Database\Query\WhereExpr,
	Database\Query\QueryInterface,
	Database\Query\SelectQuery;

class WhereClauseGenerator
{
	/**
	 * @var AbstractTable
	 */
	private $baseTable;
	
	/**
	 * @var SelectQuery
	 */
	private $baseQuery;
	
	/**
	 * @var WhereGroup[]
	 */
	private $whereGroups = [];
			
	/**
	 * Constructor
	 * 
	 * @param AbstractTable $baseTable
	 * @param WhereGroup[] $whereGroups
	 * @param SelectQuery $baseQuery
	 */
	public function __construct(AbstractTable $baseTable, array $whereGroups, SelectQuery $baseQuery = null)
	{
		$this->baseTable = $baseTable;
		$this->baseQuery = $baseQuery;
		$this->whereGroups = $whereGroups;
	}
	
	/**
	 * Generate the WHERE clause string
	 * 
	 * @return string
	 */
	public function generate($prepend = "WHERE")
	{
		$groups = [];
		foreach ($this->whereGroups as $whereGroup) {
			$group = $this->generateGroup($whereGroup, (count($groups) > 0));
			
			if ($group) {
				$groups[] = $group;
			}
		}
		
		return count($groups) ? "{$prepend} ". implode(" ", $groups) : null;
	}
	
	/**
	 * Generate string for a group of WHERE expressions
	 * 
	 * @param WhereGroup $group
	 * @param boolean $includeLinkCompator
	 * @return string
	 */
	public function generateGroup(WhereGroup $group, $includeLinkCompator = false)
	{
		$conditions = [];
		foreach ($group->exprs() as $expr) {
			$condition = $this->generateExpr($expr);
			
			if ($condition) {
				$conditions[] = $condition;
			}
		}
		
		if (count($conditions) > 0) {
			$parts = [];
			if ($includeLinkCompator) {
				$parts[] = $this->comparator($group->linkCompare());
			}
			$parts[] = "(". implode(" ". $this->comparator($group->compare()) ." ", $conditions) .")";
			
			return implode(" ", $parts);
		}
		
		return null;
	}
	
	/**
	 * Generate a string for a single WHERE expression
	 * 
	 * @param WhereExpr $expr
	 * @return string
	 */
	public function generateExpr(WhereExpr $expr)
	{
		if ($expr->isString()) {
			return $expr->expr();
		} else if ($expr->isCompound()) {
			return $this->generateCompoundExpr($expr->column(), $expr->operator(), $expr->value());
		}
	}
	
	/**
	 * Generate a WHERE expression from multiple parts
	 * 
	 * @param mixed $column
	 * @param mixed $operator
	 * @param mixed $value
	 * @return string
	 */
	public function generateCompoundExpr($column, $operator, $value)
	{
		$columnName = $this->qualifyColumn($column);
		
		if ($operator) {
			$operatorSymbol = $this->operatorSymbol($operator);
			
			if (is_array($value)) {
				$parts = [];
				$values = array_map([$this->baseTable->db(), "quote"], array_unique($value));
				
				if ($operator === QueryInterface::OP_EQUAL_TO) {
					$parts[] = $columnName ." IN (". implode(",", $values) .")";
				} else if ($operator === QueryInterface::OP_NOT_EQUAL_TO) {
					$parts[] = $columnName ." NOT IN (". implode(",", $values) .")";
				} else {
					foreach ($values as $v) {
						$parts[] = $columnName ." {$operatorSymbol} ". $this->parseValue($v, $this->baseTable->db());
					}
				}
				
				return "(". implode(" OR ", $parts) .")";
			} else if ($value !== null) {
				return "{$columnName} {$operatorSymbol} ". $this->parseValue($value, $this->baseTable->db());
			} else {
				return $columnName ." ". $operatorSymbol;
			}
		}
		
		return null;
	}
	
	/**
	 * Return the string equivalent of a compare type
	 * 
	 * @param int $compare
	 * @return string
	 */
	private function comparator($compare)
	{
		switch ($compare) {
			case QueryInterface::COMPARE_OR:
				return "OR";
			case QueryInterface::COMPARE_AND:
			default:
				return "AND";
		}
	}
	
	/**
	 * Generate a full qualitied column name
	 * 
	 * @param array|string|Column $column
	 * @return string
	 */
	private function qualifyColumn($column)
	{
		if ($column instanceof Column) {
			return "`". $column->table()->alias() ."`.`". $column->name() ."`";
		} else if (is_array($column)) {
			$column = implode(".", $column);
		}
		
		if (is_string($column) && preg_match("/^`?([a-z0-9-_]+)`?\.?`?([a-z0-9-_]+)?`?$/i", $column, $matches)) {
			if (empty($matches[2]) && $this->baseQuery) {
				$column = $this->baseQuery->findColumn($column);
				$tableName = $column->table()->alias();
				$columnName = $column->name();
			} else {
				$tableName = !empty($matches[2]) ? $matches[1] : $this->baseTable->alias();
				$columnName = !empty($matches[2]) ? $matches[2] : $matches[1];
			}
			
			return "`{$tableName}`.`{$columnName}`";
		}
		
		return null;
	}
	
	/**
	 * Get the correct symbol for the operator passed
	 * 
	 * @param mixed $operator
	 * @return string
	 */
	private function operatorSymbol($operator)
	{
		switch ($operator) {
			case QueryInterface::OP_EQUAL_TO:
				return "=";
			case QueryInterface::OP_NOT_EQUAL_TO:
				return "!=";
			case QueryInterface::OP_GREATER_THAN:
				return ">";
			case QueryInterface::OP_GREATER_OR_EQUAL_TO:
				return ">=";
			case QueryInterface::OP_LESS_THAN:
				return "<";
			case QueryInterface::OP_LESS_OR_EQUAL_TO:
				return "<=";
			case QueryInterface::OP_CONTAINS:
				return "LIKE";
			default: 
				return $operator;
		}
	}
	
	/**
	 * Parse a value for the query
	 * 
	 * @param string $value
	 * @param PDO $db
	 * @return string
	 */
	public function parseValue($value, PDO $db)
	{
		if (substr($value, 0, 1) === ":") {
			return $value;
		} else {
			return $db->quote($value);
		}
	}
}