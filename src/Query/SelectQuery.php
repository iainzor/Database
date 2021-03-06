<?php
namespace Database\Query;

use Database\PDO,
	Database\Table\AbstractTable,
	Database\Table\Column;

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
	 * @var boolean
	 */
	private $calcFoundRows = false;
	
	/**
	 * @var int
	 */
	private $foundRows = 0;
	
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
	 * Add a column to retieve from the query
	 * 
	 * @param mixed $column
	 */
	public function addColumn($column)
	{
		$this->columns[] = $column;
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
	 * Get or set whether the query should calculate the total number of found rows
	 * 
	 * @param boolean $flag
	 * @return boolean
	 */
	public function calcFoundRows($flag = null)
	{
		if ($flag !== null) {
			$this->calcFoundRows = (boolean) $flag;
		}
		return $this->calcFoundRows;
	}
	
	/**
	 * Get the total number of rows found from the last executed query
	 * 
	 * @param int $rows
	 * @return int
	 */
	public function foundRows($rows = null)
	{
		if ($rows !== null) {
			$this->foundRows = (int) $rows;
		}
		return $this->foundRows;
	}
	
	/**
	 * Fetch all results for the query
	 * 
	 * @param array $params
	 * @param int $fetchStyle
	 * @return \Database\Model\AbstractModel[]
	 */
	public function fetchAll(array $params = [], $fetchStyle = PDO::FETCH_ASSOC)
	{
		$sql = $this->generateSQL();
		$results = $this->db()->fetchAll($sql, $params, $fetchStyle);
		
		if ($this->calcFoundRows()) {
			$driverFactory = $this->db()->driverFactory();
			$sql = $driverFactory->sqlGenerator()->generateFoundRowsSql();
			$rows = (int) $this->db()->fetchColumn($sql);
			
			$this->foundRows($rows);
		}
		
		return $this->_parseResults($results);
	}
	
	/**
	 * Get a single result for the query
	 * 
	 * @param array $params
	 * @param int $fetchStyle
	 * @return \Database\Model\AbstractModel
	 */
	public function fetchRow(array $params = [], $fetchStyle = PDO::FETCH_ASSOC)
	{
		$maxResults = $this->maxResults();
		$this->maxResults(1);
		
		$all = $this->fetchAll($params, $fetchStyle);
		$this->maxResults($maxResults);
		
		return count($all) ? array_shift($all) : null;
	}
	
	/**
	 * Fetch a single column from the query
	 * 
	 * @param array $params
	 * @param int $column
	 * @return mixed
	 */
	public function fetchColumn(array $params = [], $column = 0)
	{
		$sql = $this->generateSQL();
		$column = $this->db()->fetchColumn($sql, $params, $column);
		
		return $column;
	}
	
	/**
	 * Parse the results and apply any relations to them
	 * 
	 * @param array $results
	 * @return \Database\Model\AbstractModel[]
	 */
	private function _parseResults(array $results)
	{
		$map = $this->relationMap();
		
		foreach ($results as $i => $result) {
			$results[$i] = $this->generateModel($result, true);
		}
		
		return $map->applyToRowset($results);
	}
}