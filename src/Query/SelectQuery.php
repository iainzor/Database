<?php
namespace Database\Query;

use Database\PDO;

class SelectQuery extends AbstractQuery
{
	use Traits\JoinTrait, 
		Traits\WhereTrait, 
		Traits\GroupByTrait, 
		Traits\OrderByTrait, 
		Traits\LimitTrait,
		Traits\RelationTrait;
	
	/**
	 * @var array
	 */
	private $columns = ["*"];
	
	/**
	 * Get or set the columns to select from the base table
	 * 
	 * @param array $columns
	 * @return array
	 */
	public function columns(array $columns = null)
	{
		if ($columns !== null) {
			$this->columns = $columns;
		}
		return $this->columns;
	}
	
	/**
	 * Set the table to select from
	 * 
	 * @param mixed $table
	 * @param PDO $db Optional PDO instance for the table
	 * @see AbstractQuery::table()
	 */
	public function from($table, PDO $db = null)
	{
		$instance = $this->table($table);
		$instance->db($db);
		
		$this->relationMap()->table($instance);
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
		if (!$this->db()) {
			throw new \Exception("No database instance has been given to the query");
		}
		
		$driverFactory = $this->db()->driverFactory();
		$sql = $driverFactory->sqlGenerator()->generate($this);
		$results = $this->db()->fetchAll($sql, $params, $fetchStyle);
		
		return $this->_parseResults($results);
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
		$maxResults = $this->maxResults();
		$this->maxResults(1);
		
		$all = $this->fetchAll($params, $fetchStyle);
		$this->maxResults($maxResults);
		
		return count($all) ? array_shift($all) : null;
	}
	
	private function _parseResults(array $results)
	{
		$map = $this->relationMap();
		$structure = $this->table()->structure();
		$results = $map->applyToRowset($results);
		$results = $structure->parseRowset($results, $map);
		
		//var_dump($results);
		
		return $results;
	}
}