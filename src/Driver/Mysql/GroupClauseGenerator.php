<?php
namespace Database\Driver\Mysql;

use Database\Table\AbstractTable,
	Database\Table\Column,
	Database\Table\ColumnExpr,
	Database\Query\GroupExpr;

class GroupClauseGenerator
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * @var GroupExpr[]
	 */
	private $groupings = [];
	
	/**
	 * Constructor
	 * 
	 * @param AbstractTable $table
	 * @param GroupExpr[] $groupings
	 */
	public function __construct(AbstractTable $table, array $groupings = []) 
	{
		$this->table = $table;
		$this->groupings = $groupings;
	}
	
	/**
	 * Generate the GROUP BY clause
	 * 
	 * @return string
	 * @throws \InvalidArgumentException
	 * @throws \UnexpectedValueException
	 */
	public function generate()
	{
		$parts = [];
		$whereGroups = [];
		foreach ($this->groupings as $expr) {
			if (!($expr instanceof GroupExpr)) {
				throw new \InvalidArgumentException("Group expression must be an instance of GroupExpr");
			}
			
			$column = $expr->column();
			$whereGroups = array_merge($whereGroups, $expr->whereGroups());
			
			if ($column instanceof Column) {
				$table = $column->table();
				$parts[] = sprintf("`%s`.`%s`", $table->alias(), $column->name());
			} else if ($column instanceof ColumnExpr) {
				$parts[] = $column->expr();
			} else {
				throw new \UnexpectedValueException("Unexpected column type");
			}
		}
		
		$clause = null;
		if (count($parts)) {
			$clause = "GROUP BY ". implode(", ", $parts);
		
			if (count($whereGroups)) {
				$whereGenerator = new WhereClauseGenerator($this->table, $whereGroups);
				$clause .= " ". $whereGenerator->generate("HAVING");
			}
		}
		
		return $clause;
	}
}