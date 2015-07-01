<?php
namespace Database\Query\Traits;

use Database\Query\QueryInterface,
	Database\Query\WhereGroup;

trait WhereTrait
{
	/**
	 * @var WhereGroup[]
	 */
	private $whereGroups = [];
	
	/**
	 * Add one or more conditions for a query
	 * 
	 * @param array $conditions
	 * @param int $compare
	 * @param int $linkCompare How the group should be linked with other groups in the query
	 */
	public function where($conditions, $compare = QueryInterface::COMPARE_AND, $linkCompare = QueryInterface::COMPARE_AND)
	{
		if (!is_array($conditions)) {
			$conditions = [$conditions];
		}
		
		$this->whereGroups[] = new WhereGroup($conditions, $compare, $linkCompare);
	}
	
	/**
	 * Shorthand for adding a new where group linked with OR
	 * 
	 * @param array $conditions
	 * @param int $compare
	 */
	public function orWhere($conditions, $compare = QueryInterface::COMPARE_AND)
	{
		$this->where($conditions, $compare, QueryInterface::COMPARE_OR);
	}
	
	/**
	 * Shorthand for adding a new where group linked with AND
	 * 
	 * @param array $conditions
	 * @param int $compare
	 */
	public function andWhere($conditions, $compare = QueryInterface::COMPARE_AND)
	{
		$this->where($conditions, $compare, QueryInterface::COMPARE_AND);
	}
	
	/**
	 * Get all available where groups
	 * 
	 * @return WhereGroup[]
	 */
	public function whereGroups()
	{
		return $this->whereGroups;
	}
}