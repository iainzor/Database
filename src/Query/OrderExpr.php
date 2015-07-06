<?php
namespace Database\Query;

use Database\Table\Column;

class OrderExpr
{
	/**
	 * @var AbstractQuery
	 */
	private $query;
	
	/**
	 * @var Column
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
	 * @param string|Column $column
	 */
	public function __construct(AbstractQuery $query, $column)
	{
		$this->query = $query;
		
		$this->column($column);
	}
	
	/**
	 * Get or set the column to be ordered
	 * 
	 * @param string|Column $column
	 * @return Column
	 * @throws \UnexpectedValueException
	 */
	public function column($column = null)
	{
		if ($column !== null) {
			if (is_string($column)) {
				$column = new Column($column, $this->query->table());
			}
			
			if (!($column instanceof Column)) {
				throw new \UnexpectedValueException("Column must be a name of a column or instance of \\Database\\Table\\Column");
			}
			
			$this->column = $column;
		}
		return $this->column;
	}
	
	public function asc() { $this->direction = QueryInterface::SORT_ASC; }
	public function desc() { $this->direction = QueryInterface::SORT_DESC; }
	public function direction() { return $this->direction; }
}