<?php
namespace Database\Driver\Mysql;

use Database\Query\DeleteQuery;

class DeleteSqlGenerator
{
	/**
	 * @var DeleteQuery
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param DeleteQuery $query
	 */
	public function __construct(DeleteQuery $query)
	{
		$this->query = $query;
	}
	
	/**
	 * Generate a DELETE SQL statement 
	 * 
	 * @return string
	 * @throws \Exception
	 */
	public function generate()
	{
		$table = $this->query->table();
		if (!$table) {
			throw new \Exception("No table has been given to the DeleteQuery");
		}
		
		$whereGenerator = new WhereClauseGenerator($table, $this->query->whereGroups());
		$limitGenerator = new LimitClauseGenerator($table, $this->query->maxResults(), $this->query->resultOffset());
		$orderGenerator = new OrderClauseGenerator($table, $this->query->orderings());
		
		$parts = [
			"DELETE FROM {$table->fullName(true)}",
			$whereGenerator->generate(),
			$orderGenerator->generate(),
			$limitGenerator->generate()
		];
			
		return implode(" ", $parts);
	}
}