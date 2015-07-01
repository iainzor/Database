<?php
namespace Database\Query;

class SelectQuery extends AbstractQuery
{
	use Parts\WhereTrait, Parts\GroupByTrait, Parts\OrderByTrait, Parts\LimitTrait;
	
	/**
	 * Set the table to select from
	 * 
	 * @param mixed $table
	 * @see AbstractQuery::table()
	 * @return \Database\Query\SelectQuery
	 */
	public function from($table)
	{
		$this->table($table);
		
		return $this;
	}
}