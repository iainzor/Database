<?php
namespace Database\Query\Traits;

use Database\Query\QueryInterface,
	Database\Query\JoinExpr,
	Database\Table\AbstractTable;

trait JoinTrait
{
	/**
	 * @var JoinExpr[]
	 */
	private $joins = [];
	
	/**
	 * Get the joins for the query
	 * 
	 * @return JoinExpr[]
	 */
	public function joins()
	{
		return $this->joins;
	}
	
	/**
	 * Attempt to find a joined table by its name
	 * 
	 * @param string $tableName
	 * @return AbstractTable
	 * @throws \Exception
	 */
	public function findJoinedTable($tableName)
	{
		foreach ($this->joins as $join) {
			if ($join->foreignTable()->alias() === $tableName) {
				return $join->foreignTable();
			}
		}
		throw new \Exception("Could not find joined table '{$tableName}'");
	}
	
	/**
	 * Join a table onto the query
	 * 
	 * @param string|array|AbstractTable $foreignTable
	 * @param string|array $foreignKeys
	 * @param string|array $localKeys
	 * @param array $columns The columns to select from the joined table
	 * @param int $type
	 * @return JoinExpr
	 */
	public function join($foreignTable, $foreignKeys = null, $localKeys = null, array $columns = null, $type = QueryInterface::JOIN_DEFAULT)
	{
		$expr = new JoinExpr($this, $foreignTable, $foreignKeys, $localKeys, $columns, $type);
		$this->joins[] = $expr;
		
		return $expr;
	}
	
	/**
	 * Add a left joined table to the query
	 * 
	 * @param string|array|AbstractTable $foreignTable
	 * @param string|array $foreignKeys
	 * @param string|array $localKeys
	 * @param array $columns
	 * @return JoinExpr
	 */
	public function leftJoin($foreignTable, $foreignKeys = null, $localKeys = null, array $columns = null)
	{
		return $this->join($foreignTable, $foreignKeys, $localKeys, $columns, QueryInterface::JOIN_LEFT);
	}
}