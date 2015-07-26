<?php
namespace Database\Reference;

use Database\Query\SelectQuery;

class QueryReference implements ReferenceInterface
{
	/**
	 * @var SelectQuery
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param SelectQuery $query
	 */
	public function __construct(SelectQuery $query)
	{
		$this->query = $query;
	}
	
	/**
	 * Find all results from the query using an array of conditions
	 * 
	 * @param array $conditions
	 * @return array
	 */
	public function findAll(array $conditions) 
	{
		$this->query->where($conditions);
		
		return $this->query->fetchAll();
	}
	
	/**
	 * Get the structure of the referenced query
	 * 
	 * @return \Database\Table\Structure
	 */
	public function structure() 
	{
		return $this->query->table()->structure();
	}
}