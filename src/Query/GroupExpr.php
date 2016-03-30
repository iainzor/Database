<?php
namespace Database\Query;

use Database\Table\Column,
	Database\Table\ColumnExpr;

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
	 * @param string|Column|ColumnExpr $column
	 */
	public function __construct(AbstractQuery $query, $column)
	{
		$this->query = $query;
		
		$this->column($column);
	}
	
	/**
	 * Get or set the column to be grouped
	 * 
	 * @param string|Column|ColumnExpr $column
	 * @return Column|ColumnExpr
	 * @throws \UnexpectedValueException
	 */
	public function column($column = null)
	{
		if ($column !== null) {
			if (is_string($column)) {
				$column = new Column($column, $this->query->table());
			} else if (is_array($column)) {
				$tableName = array_shift($column);
				if (count($column) > 0 && $this->query instanceof SelectQuery) {
					$columnName = array_shift($column);
					$table = $this->query->findJoinedTable($tableName);
				} else {
					$columnName = $tableName;
					$table = $this->query->table();
				}
				$column = new Column($columnName, $table);
			}
			
			if (!($column instanceof Column) && !($column instanceof ColumnExpr)) {
				throw new \UnexpectedValueException("Column must be a name of a column or instance of Column or ColumnExpr");
			}
			
			$this->column = $column;
		}
		return $this->column;
	}
}