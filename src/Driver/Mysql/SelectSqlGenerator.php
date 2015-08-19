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
		
		$parts = [
			"SELECT",
			$this->columnList(),
			"FROM `{$dbName}`.`{$table->name()}` AS `{$table->alias()}`",
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
			
			$lines[] = "{$expr} `{$foreignDbName}`.`{$foreignTable->name()}` AS `{$foreignTable->alias()}` ON ". implode(" AND ", $conditions);
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
}