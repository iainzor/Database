<?php
namespace Database\Driver\Mysql;

use Database\Query\SelectQuery,
	Database\Table\AbstractTable,
	Database\Table\ColumnExpr;

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
		$dbName = $table->db()->schemaName();
		
		$whereGenerator = new WhereClauseGenerator($table, $this->query->whereGroups());
		$orderGenerator = new OrderClauseGenerator($table, $this->query->orderings());
		$limitGenerator = new LimitClauseGenerator($table, $this->query->maxResults(), $this->query->resultOffset());
		
		if (!empty($dbName)) {
			$dbName = "`{$dbName}`.";
		}
		
		$parts = [
			"SELECT",
			$this->columnList(),
			//"FROM {$dbName}`{$table->name()}` AS `{$table->alias()}`",
			"FROM {$table->fullName(true)} AS `{$table->alias()}`",
			$this->joinClause(),
			$whereGenerator->generate(),
			$this->groupClause(),
			$orderGenerator->generate(),
			$limitGenerator->generate()
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
		$table = $this->query->table();
		$joins = $this->query->joins();
		$fields = [];
		
		foreach ($this->query->columns() as $name => $alias) {
			$fields[] = $this->_columnName($table, $name, $alias);
		}
		foreach ($joins as $join) {
			$foreignTable = $join->foreignTable();
			foreach ($join->columns() as $name => $alias) {
				$fields[] = $this->_columnName($foreignTable, $name, $alias);
			}
		}
		
		return count($fields) ? implode(", ", $fields) : "*";
	}
	
	/**
	 * Parse a column name for the SQL statement
	 * 
	 * @param AbstractTable $table
	 * @param mixed $name
	 * @param string $alias
	 * @return string
	 */
	private function _columnName(AbstractTable $table, $name, $alias)
	{
		if (is_numeric($name)) {
			$name = $alias;
		}
		
		if ($name instanceof ColumnExpr) {
			return $name->expr();
		}
		
		$parts = [
			"`{$table->alias()}`." . (($name === "*") ? "*" : "`{$name}`")
		];
		if ($alias !== "*") {
			$parts[] = "AS `{$alias}`";
		}
		
		return implode(" ", $parts);
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
			$foreignDbName = $foreignTable->db()->schemaName();
			$foreignKeys = $join->foreignKeys();
			$localTable = empty($join->localTable()) ? $this->query->table() : $join->localTable();
			$localKeys = $join->localKeys();
			$conditions = [];
			
			foreach ($foreignKeys as $i => $foreignKey) {
				if (!isset($localKeys[$i])) {
					continue;
				}
				$localKey = $localKeys[$i];
				$join->where("{$localTable->fullName(true)}.`{$localKey}` = {$foreignTable->fullName(true)}.`{$foreignKey}`");
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
			
			$whereClauseGenerator = new WhereClauseGenerator($foreignTable, $join->whereGroups());
			$whereClause = $whereClauseGenerator->generate("ON");
		
			$lines[] = "{$expr} {$foreignTable->fullName(true)} AS `{$foreignTable->alias()}` {$whereClause}";
		}
		
		return implode("\n", $lines);
	}
	
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
}