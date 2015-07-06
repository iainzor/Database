<?php
namespace Database\Query;

use Database\Table\Column;

class GroupExpr
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
	 * Get or set the column to be grouped
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
}