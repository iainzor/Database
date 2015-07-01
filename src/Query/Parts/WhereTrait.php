<?php
namespace Database\Query\Parts;

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
	 */
	public function where($conditions, $compare = QueryInterface::COMPARE_AND)
	{
		if (!is_array($conditions)) {
			$conditions = [$conditions];
		}
		
		$this->whereGroups[] = new WhereGroup($conditions, $compare);
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