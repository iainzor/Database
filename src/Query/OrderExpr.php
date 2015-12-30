<?php
namespace Database\Query;

use Database\Table\Column,
	Database\Table\ColumnExpr;

class OrderExpr
{
	/**
	 * @var AbstractQuery
	 */
	private $query;
	
	/**
	 * @var ColumnExpr
	 */
	private $column;
	
	/**
	 * @var int
	 */
	private $direction = QueryInterface::SORT_ASC;
	
	/**
	 * Constructor
	 * 
	 * @param \Database\Query\AbstractQuery $query
	 * @param string|Column|ColumnExpr $column
	 */
	public function __construct(AbstractQuery $query, $column)
	{
		$this->query = $query;
		
		$this->column($column);
	}
	
	/**
	 * Get or set the column to be ordered
	 * 
	 * @param string|Column|ColumnExpr $column
	 * @return ColumnExpr
	 * @throws \UnexpectedValueException
	 */
	public function column($column = null)
	{
		if ($column !== null) {
			if (is_string($column)) {
				$expr = new ColumnExpr($column);
			} else if ($column instanceof Column) {
				$table = $column->table() ?: $this->query->table();
				$expr = new ColumnExpr($table->alias().".".$column->name());
			} else if ($column instanceof ColumnExpr) {
				$expr = $column;
			}
			
			if (!isset($expr)) {
				throw new \UnexpectedValueException("Unexpected column value provided for the order expression");
			}
			
			$this->column = $expr;
		}
		return $this->column;
	}
	
	public function asc() { $this->direction = QueryInterface::SORT_ASC; }
	public function desc() { $this->direction = QueryInterface::SORT_DESC; }
	public function direction() { return $this->direction; }
}