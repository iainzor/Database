<?php
namespace Database\Driver\Mysql;

use Database\Driver\SqlGeneratorInterface,
	Database\Query,
	Database\PDO;

class SqlGenerator implements SqlGeneratorInterface 
{
	/**
	 * Generate a SQL statement for the query provided
	 * 
	 * @param \Database\Query\AbstractQuery $query
	 * @return string
	 * @throws \Exception
	 */
	public function generate(Query\AbstractQuery $query)
	{
		if ($query instanceof Query\SelectQuery) {
			return $this->generateSelect($query);
		}
		
		throw new \Exception("Could not generate a SQL statement using the query '". get_class($query) ."'");
	}
	
	/**
	 * Generate a SELECT statement
	 * 
	 * @param \Database\Query\SelectQuery $query
	 * @return string
	 */
	public function generateSelect(Query\SelectQuery $query)
	{
		$table = $query->table();
		$parts = [
			"SELECT",
			$this->columnList($query),
			"FROM `{$table->name()}` AS `{$table->alias()}`",
			$this->joinClause($query),
			$this->whereClause($query->whereGroups(), $query->db()),
			$this->groupClause($query->groupings()),
			$this->orderClause($query->orderings()),
			$this->limitClause($query->maxResults(), $query->resultOffset())
		];
			
		$sql = implode("\n", $parts);
		
		return $sql;
	}
	
	/**
	 * @param \Database\Query\WhereGroup[] $whereGroups
	 * @param \Database\PDO $db
	 * @return string
	 */
	private function whereClause(array $whereGroups, PDO $db)
	{
		$groups = [];
		foreach ($whereGroups as $i => $whereGroup) {
			$group = [];
			foreach ($whereGroup->exprs() as $expr) {
				$column = $expr->column();
				$operator = $expr->operator();
				$value = $expr->value();
				
				
				
				$group[] = $expr->toString($db);
			}
			
			if (count($group)) {
				if (count($groups) > 0) {
					$groups[] = $this->compareGlue($whereGroup->linkCompare());
				}
				
				$glue = $this->compareGlue($whereGroup->compare());
				$groups[] = "(". implode(" {$glue} ", $group) .")";
			}
		}
		return count($groups) ? "WHERE ". implode(" ", $groups) : null;
	}
	
	/**
	 * @param \Database\Query\SelectQuery $query
	 * @return string
	 */
	private function joinClause(Query\SelectQuery $query)
	{
		$lines = [];
		foreach ($query->joins() as $join) {
			$foreignTable = $join->foreignTable();
			$foreignKeys = $join->foreignKeys();
			$localTable = empty($join->localTable()) ? $query->table() : $join->localTable();
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
				case Query\QueryInterface::JOIN_LEFT:
					$expr = "LEFT JOIN";
					break;
				case Query\QueryInterface::JOIN_RIGHT:
					$expr = "RIGHT JOIN";
					break;
				case Query\QueryInterface::JOIN_DEFAULT:
				default:
					$expr = "JOIN";
					break;
			}
			
			$lines[] = "{$expr} `{$foreignTable->name()}` AS `{$foreignTable->alias()}` ON ". implode(" AND ", $conditions);
		}
		
		return implode("\n", $lines);
	}
	
	/**
	 * Generate a GROUP BY clause for a collection of GroupExpr instances
	 * 
	 * @param Query\GroupExpr[] $groupings
	 * @return string
	 */
	private function groupClause(array $groupings)
	{
		$parts = [];
		foreach ($groupings as $expr) {
			$column = $expr->column();
			$table = $column->table();
			$parts[] = sprintf("`%s`.`%s`", $table->alias(), $column->name());
		}
		
		return count($parts) ? "GROUP BY ". implode(", ", $parts) : null;
	}
	
	/**
	 * Generate an order clause for a collection of order expressions
	 * 
	 * @param Query\OrderExpr[] $orderings
	 * @return string
	 */
	private function orderClause(array $orderings)
	{
		$parts = [];
		foreach ($orderings as $expr) {
			$column = $expr->column();
			$table = $column->table();
			
			switch ($expr->direction()) {
				case Query\QueryInterface::SORT_DESC:
					$dir = "DESC";
					break;
				case Query\QueryInterface::SORT_ASC:
				default:
					$dir = "ASC";
					break;
			}
			
			$parts[] = sprintf("`%s`.`%s` %s", $table->alias(), $column->name(), $dir);
		}
		
		return count($parts) ? "ORDER BY ". implode(", ", $parts) : null;
	}
	
	/**
	 * Generate a LIMIT clause 
	 * 
	 * @param int $maxResults
	 * @param int $resultOffset
	 * @return string
	 */
	private function limitClause($maxResults, $resultOffset)
	{
		if ($maxResults < 1) {
			return null;
		}
		
		$clause = "LIMIT ". $maxResults;
		if ($resultOffset > 0) {
			$clause .= " OFFSET ". $resultOffset;
		}
		return $clause;
	}
	
	/**
	 * Generate a list of columns depending on the query type
	 * 
	 * @param \Database\Query\AbstractQuery $query
	 * @return string
	 */
	private function columnList(Query\AbstractQuery $query)
	{
		return "*";
	}
	
	/**
	 * Return the string equivalent of a compare type
	 * 
	 * @param int $compare
	 * @return string
	 */
	private function compareGlue($compare)
	{
		switch ($compare) {
			case Query\QueryInterface::COMPARE_OR:
				return "OR";
			case Query\QueryInterface::COMPARE_AND:
			default:
				return "AND";
		}
	}
}