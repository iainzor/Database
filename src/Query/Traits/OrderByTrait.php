<?php
namespace Database\Query\Traits;

use Database\Query\OrderExpr,
	Database\Table\Column;

trait OrderByTrait
{
	/**
	 * @var OrderExpr[]
	 */
	private $orderings = [];
	
	/**
	 * Add a new order expression to the query
	 * 
	 * @param string|Column $column
	 * @return OrderExpr
	 */
	public function orderBy($column)
	{
		$expr = new OrderExpr($this, $column);
		$column = $expr->column();
		$table = $column->table();
		$key = $table->alias() .".". $column->name();
		
		$this->orderings[$key] = $expr;
		
		return $expr;
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