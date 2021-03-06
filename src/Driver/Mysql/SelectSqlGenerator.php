<?php
namespace Database\Driver\Mysql;

use Database\Query\SelectQuery,
	Database\Table\AbstractTable,
	Database\Table\VirtualTable,
	Database\Table\ColumnExpr,
	Database\Table\Column;

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
		
		$whereGenerator = new WhereClauseGenerator($table, $this->query->whereGroups(), $this->query);
		$orderGenerator = new OrderClauseGenerator($table, $this->query->orderings());
		$groupGenerator = new GroupClauseGenerator($table, $this->query->groupings());
		$limitGenerator = new LimitClauseGenerator($table, $this->query->maxResults(), $this->query->resultOffset());
		
		if (!empty($dbName)) {
			$dbName = "`{$dbName}`.";
		}
		
		$parts = [
			$this->query->calcFoundRows() ? "SELECT SQL_CALC_FOUND_ROWS" : "SELECT",
			$this->columnList(),
			"FROM {$table->fullName(true)} AS `{$table->alias()}`",
			$this->joinClause(),
			$whereGenerator->generate(),
			$groupGenerator->generate(),
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
		} else if ($alias instanceof ColumnExpr) {
			return $alias->expr();
		} else if ($name instanceof Column) {
			$table = $name->table();
			$alias = $name->alias();
			$name = $name->name();
		} else if ($alias instanceof Column) {
			$table = $alias->table();
			$name = $alias->name();
			$alias = $alias->alias();
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
			$localTable = $join->localTable() ? $join->localTable() : $this->query->table();
			$localKeys = $join->localKeys();
			$conditions = [];
			$isVirtual = $foreignTable instanceof VirtualTable;
			
			foreach ($foreignKeys as $i => $foreignKey) {
				if (!isset($localKeys[$i])) {
					continue;
				}
				
				$localKey = $localKeys[$i];
				
				if (!($foreignKey instanceof Column)) {
					$foreignKey = $foreignTable->column($foreignKey);
				}
				
				if (!($localKey instanceof Column)) {
					$localKey = $localTable->column($localKey);
				}
				
				$foreignPath = $foreignKey->fullName(true);
				if ($foreignTable->name() !== $foreignTable->alias() || $isVirtual) {
					$foreignPath = "`{$foreignTable->alias()}`.`". $foreignKey->name() ."`";
				}
				
				$join->where($localKey->fullName(true) ." = ". $foreignPath);
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
			$tableExpr = $foreignTable->fullName(true);
			
			if ($foreignTable instanceof VirtualTable) {
				$tableExpr = "(". $foreignTable->query()->generateSql() .")";
			}
		
			$lines[] = "{$expr} {$tableExpr} AS `{$foreignTable->alias()}` {$whereClause}";
		}
		
		return implode("\n", $lines);
	}
}