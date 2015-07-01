<?php
namespace Database\Query;

/**
 * @method SelectQuery where()
 */
class SelectQuery extends AbstractQuery
{
	use Parts\WhereTrait, Parts\GroupByTrait, Parts\OrderByTrait, Parts\LimitTrait;
	
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
	public function fetchAll(array $params = [], $fetchStyle = \PDO::FETCH_ASSOC)
	{
		$driverFactory = $this->db()->driverFactory();
		$sql = $driverFactory->sqlGenerator()->generate($this);
		
		return $this->db()->fetchAll($sql, $params, $fetchStyle);
	}
}