<?php
namespace Database\Driver\Mysql;

use Database\Table\AbstractTable;

class LimitClauseGenerator
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * @var int
	 */
	private $maxResults = 0;
	
	/**
	 * @var int
	 */
	private $resultOffset = 0;
	
	/**
	 * Constructor
	 * 
	 * @param AbstractTable $table
	 * @param int $maxResults
	 * @param int $resultOffset
	 */
	public function __construct(AbstractTable $table, $maxResults = 0, $resultOffset = 0)
	{
		$this->table = $table;
		$this->maxResults = (int) $maxResults;
		$this->resultOffset = (int) $resultOffset;
	}
	
	/**
	 * Generate the LIMIT clause of a SQL statement
	 * 
	 * @return string
	 */
	public function generate()
	{
		if ($this->maxResults < 1) {
			return null;
		}
		
		$clause = "LIMIT ". $this->maxResults;
		if ($this->resultOffset > 0) {
			$clause .= " OFFSET ". $this->resultOffset;
		}
		return $clause;
	}
}