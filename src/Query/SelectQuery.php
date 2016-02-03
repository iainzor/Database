<?php
namespace Database\Query;

use Database\PDO,
	Database\Table\Column,
	Database\Table\AbstractTable;

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
	 * Attempt to find a column in the query
	 * 
	 * @param string $columnName
	 * @return Column
	 * @throws \Exception
	 */
	public function findColumn($columnName) 
	{
		$column = $this->_findColumn($columnName, $this->table());
		if (!$column) {
			foreach ($this->joins() as $join) {
				$column = $this->_findColumn($columnName, $join->foreignTable());
				if ($column) {
					break;
				}
			}
		}
		
		if (!$column) {
			throw new \Exception("Could not find column '{$columnName}'");
		}
		
		return $column;
	}
	
	/**
	 * Attempt to find a column in a table
	 * 
	 * @param string $columnName
	 * @param AbstractTable $table
	 * @return Column|false
	 */
	private function _findColumn($columnName, AbstractTable $table) 
	{
		$columns = $table->structure()->columns();
		$col = false;
		
		foreach ($columns as $column) {
			if ($column instanceof Column && $column->name() === $columnName) {
				$col = $column;
				break;
			} else if (is_string($column) && $column === $columnName) {
				$col = new Column($column, $table);
				break;
			}
		}
		
		if ($col !== false) {
			$col->table($table);
		}
		
		return $col;
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
	 * @return int
	 */
	public function foundRows()
	{
		$db = $this->db();
		$driverFactory = $db->driverFactory();
		$sql = $driverFactory->sqlGenerator()->generateFoundRowsSql();
		
		return (int) $db->fetchColumn($sql);
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
		$row = $this->fetchRow($params);
		if ($row) {
			$keys = array_keys($row);
			if (isset($keys[$column])) {
				return $row[$keys[$column]];
			}
		}
		return null;
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
			$results[$i] = $this->generateModel($result);
		}
		
		return $map->applyToRowset($results);
	}
}