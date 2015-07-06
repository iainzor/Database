<?php
namespace Database\Query\Traits;

use Database\Query\GroupExpr,
	Database\Table\Column;

trait GroupByTrait
{
	/**
	 * @var GroupExpr[]
	 */
	private $groupings = [];
	
	/**
	 * Add a column to group results by
	 * 
	 * @param string|Column $column
	 */
	public function groupBy($column)
	{
		$expr = new GroupExpr($this, $column);
		$key = $expr->column()->table()->name() .".". $expr->column()->name();
		$this->groupings[$key] = $expr;
	}
	
	/**
	 * Get all available groupings for the query
	 * 
	 * @return GroupExpr[]
	 */
	public function groupings()
	{
		return $this->groupings;
	}
}