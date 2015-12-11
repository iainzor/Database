<?php
namespace Database\Driver\Mysql;

use Database\Query\UpdateQuery;

class UpdateSqlGenerator
{
	/**
	 * @var UpdateQuery
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param UpdateQuery $query
	 */
	public function __construct(UpdateQuery $query)
	{
		$this->query = $query;
	}
	
	public function generate()
	{
		$table = $this->query->table();
		$values = $this->query->values();
		
		if (!$table) {
			throw new \Exception("No table has been provided to the UpdateQuery");
		}
		
		if (!count($values)) {
			throw new \Exception("No values have been specified to update");
		}
		
		$whereGenerator = new WhereClauseGenerator($table, $this->query->whereGroups());
		$orderGenerator = new OrderClauseGenerator($table, $this->query->orderings());
		$limitGenerator = new LimitClauseGenerator($table, $this->query->maxResults(), $this->query->resultOffset());
		
		$setClause = $this->generateSetClause($values);
		$whereClause = $whereGenerator->generate();
		$orderClause = $orderGenerator->generate();
		$limitClause = $limitGenerator->generate();
		
		$parts = [
			"UPDATE `{$table->name()}`",
			$setClause,
			$whereClause,
			$orderClause,
			$limitClause
		];
			
		return implode(" ", $parts);
	}
	
	/**
	 * Generate the SET clause of the UPDATE statement
	 * 
	 * @param array $values
	 * @return string
	 */
	private function generateSetClause(array $values)
	{
		$sets = [];
		$db = $this->query->db();
		$structure = $this->query->table()->structure();
		
		foreach ($values as $name => $value) {
			if ($structure->isColumn($name)) {
				$sets[] = "`{$name}` = ". $db->quote($value);
			}
		}
		
		return "SET ". implode(", ", $sets);
	}
}