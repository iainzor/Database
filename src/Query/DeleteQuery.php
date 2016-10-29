<?php
namespace Database\Query;

class DeleteQuery extends AbstractQuery 
{
	use Traits\WhereTrait,
		Traits\OrderByTrait,
		Traits\LimitTrait;
	
	/**
	 * Set the table to delete from
	 * 
	 * @param mixed $table
	 * @return \Database\Table\AbstractTable
	 */
	public function from($table)
	{
		return $this->table($table);
	}
	
	/**
	 * Execute the DELETE query
	 * 
	 * @return int The number of rows that were deleted
	 */
	public function execute(array $params = [])
	{
		$factory = $this->db()->driverFactory();
		$sql = $factory->sqlGenerator()->generate($this);
		
		return $this->db()->exec($sql, $params);
	}
}