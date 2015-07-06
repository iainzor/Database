<?php
namespace Database\Query;

use Database\PDO;

class SelectQuery extends AbstractQuery
{
	use Traits\JoinTrait, Traits\WhereTrait, Traits\GroupByTrait, Traits\OrderByTrait, Traits\LimitTrait;
	
	/**
	 * Set the table to select from
	 * 
	 * @param mixed $table
	 * @see AbstractQuery::table()
	 */
	public function from($table)
	{
		$this->table($table);
	}
	
	/**
	 * Fetch all results for the query
	 * 
	 * @param array $params
	 * @param int $fetchStyle
	 * @return array
	 */
	public function fetchAll(array $params = [], $fetchStyle = PDO::FETCH_ASSOC)
	{
		$driverFactory = $this->db()->driverFactory();
		$sql = $driverFactory->sqlGenerator()->generate($this);
		
		return $this->db()->fetchAll($sql, $params, $fetchStyle);
	}
	
	/**
	 * Get a single result for the query
	 * 
	 * @param array $params
	 * @param int $fetchStyle
	 * @return mixed
	 */
	public function fetchRow(array $params = [], $fetchStyle = PDO::FETCH_ASSOC)
	{
		$driverFactory = $this->db()->driverFactory();
		$sql = $driverFactory->sqlGenerator()->generate($this);
		
		return $this->db()->fetchRow($sql, $params, $fetchStyle);
	}
}