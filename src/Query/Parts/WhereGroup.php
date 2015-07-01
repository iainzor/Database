<?php
namespace Database\Query\Parts;

use Database\Query\QueryInterface;

class WhereGroup
{
	/**
	 * @var array
	 */
	private $conditions = [];
	
	/**
	 * @var int
	 */
	private $compare = QueryInterface::COMPARE_AND;
	
	/**
	 * Constructor
	 * 
	 * @param array $conditions
	 * @param int $compare
	 */
	public function __construct(array $conditions, $compare = QueryInterface::COMPARE_AND)
	{
		$this->conditions = $conditions;
		$this->compare = $compare;
	}
}