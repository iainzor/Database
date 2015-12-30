<?php
namespace Database\Driver\Mysql;

use Database\Table\AbstractTable,
	Database\Query\AbstractQuery,
	Database\Query\OrderExpr;

class OrderClauseGenerator
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * @var OrderExpr[]
	 */
	private $orderings = [];
	
	/**
	 * Constructor
	 * 
	 * @param AbstractTable $table
	 * @param array $orderings
	 */
	public function __construct(AbstractTable $table, array $orderings)
	{
		$this->table = $table;
		$this->orderings = $orderings;
	}
	
	/**
	 * Generate the ORDER clause for a SQL statement
	 * 
	 * @return string
	 */
	public function generate()
	{
		$parts = [];
		foreach ($this->orderings as $expr) {
			$column = $expr->column();
			
			switch ($expr->direction()) {
				case AbstractQuery::SORT_DESC:
					$dir = "DESC";
					break;
				case AbstractQuery::SORT_ASC:
				default:
					$dir = "ASC";
					break;
			}
			
			$parts[] = sprintf("%s %s", $column->expr(), $dir);
		}
		
		return count($parts) ? "ORDER BY ". implode(", ", $parts) : null;
	}
}