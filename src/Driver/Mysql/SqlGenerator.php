<?php
namespace Database\Driver\Mysql;

use Database\Driver\SqlGeneratorInterface,
	Database\Query;

class SqlGenerator implements SqlGeneratorInterface 
{
	/**
	 * @param \Database\Query\AbstractQuery $query
	 * @return string
	 * @throws \Exception
	 */
	public function generate(Query\AbstractQuery $query)
	{
		if ($query instanceof Query\SelectQuery) {
			return $this->generateSelect($query);
		}
		
		throw new \Exception("Could not generate a SQL statement using the query '". get_class($query));
	}
	
	public function generateSelect(Query\SelectQuery $query)
	{
		$table = $query->table();
		$parts = [
			"SELECT",
			$this->columnList($query),
			"FROM `{$table->name()}` AS `{$table->alias()}`",
			$this->whereClause($query->whereGroups())
		];
			
		$sql = implode("\n", $parts);
		
		return $sql;
	}
	
	/**
	 * @param \Database\Query\WhereGroup[] $whereGroups
	 * @return string
	 */
	private function whereClause(array $whereGroups)
	{
		$groups = [];
		foreach ($whereGroups as $whereGroup) {
			$group = [];
			foreach ($whereGroup->exprs() as $expr) {
				$group[] = $expr->toString();
			}
			
			switch ($whereGroup->compare()) {
				case Query\QueryInterface::COMPARE_OR:
					$glue = " OR ";
					break;
				case Query\QueryInterface::COMPARE_AND:
				default:
					$glue = " AND ";
					break;
			}
			
			if (count($group)) {
				$groups[] = "(". implode($glue, $group) .")";
			}
		}
		return count($groups) ? "WHERE ". implode(" AND ", $groups) : null;
	}
	
	private function columnList(Query\AbstractQuery $query)
	{
		return "*";
	}
}