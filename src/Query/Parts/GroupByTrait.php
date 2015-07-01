<?php
namespace Database\Query\Parts;

trait GroupByTrait
{
	/**
	 * @var array
	 */
	private $groupings = [];
	
	/**
	 * Add one or more columns to group by
	 * 
	 * @param string $columns
	 */
	public function groupBy($columns)
	{
		if (!is_array($columns)) {
			$columns = [$columns];
		}
		
		$this->groupings += $columns;
		
		return $this;
	}
	
	/**
	 * Get all available groupings for the query
	 * 
	 * @return array
	 */
	public function groupings()
	{
		return $this->groupings;
	}
}