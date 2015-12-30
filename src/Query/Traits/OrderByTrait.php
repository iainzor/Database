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
	 * @param string|Column|ColumnExpr $column
	 * @return OrderExpr
	 */
	public function orderBy($column)
	{
		$expr = new OrderExpr($this, $column);
		$columnExpr = $expr->column();
		$key = $columnExpr->expr();
		
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