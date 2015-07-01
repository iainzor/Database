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
	 * Join a table onto the query
	 * 
	 * @param string|array|AbstractTable $foreignTable
	 * @param string|array $foreignKeys
	 * @param int $type
	 * @return JoinExpr
	 */
	public function join($foreignTable, $foreignKeys, $type = QueryInterface::JOIN_DEFAULT)
	{
		$expr = new JoinExpr($foreignTable, $foreignKeys, $type);
		$this->joins[] = $expr;
		
		return $expr;
	}
	
	/**
	 * Add a left joined table to the query
	 * 
	 * @param string|array|AbstractTable $foreignTable
	 * @param string|array $foreignKeys
	 * @return JoinExpr
	 */
	public function leftJoin($foreignTable, $foreignKeys)
	{
		return $this->join($foreignTable, $foreignKeys, QueryInterface::JOIN_LEFT);
	}
}