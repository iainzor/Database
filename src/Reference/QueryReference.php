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
	 * @return SelectQuery
	 */
	public function selectQuery() 
	{
		return $this->query;
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