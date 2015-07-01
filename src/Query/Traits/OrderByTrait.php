<?php
namespace Database\Query\Traits;

use Database\Query\QueryInterface;

trait OrderByTrait
{
	private $orderings = [];
	
	/**
	 * Add one or more orderings to the query
	 * 
	 * @param string|array $column	Either a column name or an array as [columnName => direction]
	 * @param int $dir				Direction of the ordering.  If first parameter is an array, this is ignored
	 */
	public function orderBy($column, $dir = QueryInterface::SORT_ASC)
	{
		if (is_array($column)) {
			foreach ($column as $name => $dir) {
				$this->orderBy($name, $dir);
			}
		} else {
			$this->orderings[$column] = $dir;
		}
	}
	
	/**
	 * Get all available orderings for the query
	 * 
	 * @return string
	 */
	public function orderings()
	{
		return $this->orderings;
	}
}