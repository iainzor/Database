<?php
namespace Database\Driver\Mysql;

use Database\Query\SelectQuery;

class SelectSqlGenerator
{
	/**
	 * @var SelectQuery
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param SelectQuery $query
	 */
	public function __construct(SelectQuery $query)
	{
		$this->query = $query;
	}
	
	/**
	 * Generate a new SELECT statement
	 * 
	 * @return string
	 */
	public function generate()
	{
		$table = $this->query->table();
		$whereClause = new WhereClauseGenerator($table, $this->query->whereGroups());
		$parts = [
			"SELECT",
			$this->columnList(),
			"FROM `{$table->name()}` AS `{$table->alias()}`",
			$this->joinClause(),
			$whereClause->generate(),
			$this->groupClause(),
			$this->orderClause(),
			$this->limitClause()
		];
			
		$sql = implode("\n", $parts);
		
		return $sql;
	}
	
	/**
	 * Generate a list of columns for the select statement
	 * 
	 * @return string
	 */
	private function columnList()
	{
		return "*";
	}
	
	/**
	 * Generate JOIN clauses for each join in the query
	 * 
	 * @return string
	 */
	private function joinClause()
	{
		$lines = [];
		foreach ($this->query->joins() as $join) {
			$foreignTable = $join->foreignTable();
			$foreignKeys = $join->foreignKeys();
			$localTable = empty($join->localTable()) ? $this->query->table() : $join->localTable();
			$localKeys = $join->localKeys();
			$conditions = [];
			
			foreach ($foreignKeys as $i => $foreignKey) {
				if (!isset($localKeys[$i])) {
					continue;
				}
				$localKey = $localKeys[$i];
				$conditions[] = "`{$localTable->alias()}`.`{$localKey}` = `{$foreignTable->alias()}`.`{$foreignKey}`";
			}
			
			if (empty($conditions)) {
				throw new \Exception("No conditions found for join: {$localTable->name()} -> {$foreignTable->name()}");
			}
			
			switch ($join->type()) {
				case SelectQuery::JOIN_LEFT:
					$expr = "LEFT JOIN";
					break;
				case SelectQuery::JOIN_RIGHT:
					$expr = "RIGHT JOIN";
					break;
				case SelectQuery::JOIN_DEFAULT:
				default:
					$expr = "JOIN";
					break;
			}
			
			$lines[] = "{$expr} `{$foreignTable->name()}` AS `{$foreignTable->alias()}` ON ". implode(" AND ", $conditions);
		}
		
		return implode("\n", $lines);
	}
	
	/**
	 * Generate the WHERE clause of the query
	 * 
	 * @return string
	 */
	public function whereClause()
	{}
	
	/**
	 * Generate a GROUP BY clause for the query
	 * 
	 * @return string
	 */
	private function groupClause()
	{
		$parts = [];
		foreach ($this->query->groupings() as $expr) {
			$column = $expr->column();
			$table = $column->table();
			$parts[] = sprintf("`%s`.`%s`", $table->alias(), $column->name());
		}
		
		return count($parts) ? "GROUP BY ". implode(", ", $parts) : null;
	}
	
	/**
	 * Generate an ORDER clause the query
	 * 
	 * @return string
	 */
	private function orderClause()
	{
		$parts = [];
		foreach ($this->query->orderings() as $expr) {
			$column = $expr->column();
			$table = $column->table();
			
			switch ($expr->direction()) {
				case SelectQuery::SORT_DESC:
					$dir = "DESC";
					break;
				case SelectQuery::SORT_ASC:
				default:
					$dir = "ASC";
					break;
			}
			
			$parts[] = sprintf("`%s`.`%s` %s", $table->alias(), $column->name(), $dir);
		}
		
		return count($parts) ? "ORDER BY ". implode(", ", $parts) : null;
	}
	
	/**
	 * Generate a LIMIT clause for the query
	 * 
	 * @param int $maxResults
	 * @param int $resultOffset
	 * @return string
	 */
	private function limitClause()
	{
		$maxResults = $this->query->maxResults(); 
		$resultOffset = $this->query->resultOffset();
		
		if ($maxResults < 1) {
			return null;
		}
		
		$clause = "LIMIT ". $maxResults;
		if ($resultOffset > 0) {
			$clause .= " OFFSET ". $resultOffset;
		}
		return $clause;
	}
}