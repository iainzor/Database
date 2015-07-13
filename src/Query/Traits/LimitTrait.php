<?php
namespace Database\Query\Traits;

trait LimitTrait
{
	/**
	 * @var int
	 */
	private $maxResults = 0;
	
	/**
	 * @var int
	 */
	private $resultOffset = 0;
	
	/**
	 * Limit the number of results the query should affect and the offset to start from
	 * 
	 * @param int $maxResults
	 * @param int $resultOffset
	 */
	public function limit($maxResults, $resultOffset = 0)
	{
		$this->maxResults = (int) $maxResults;
		$this->resultOffset = (int) $resultOffset;
	}
	
	/**
	 * Get or set the maximum number of results the query should be limited to

	 * @return int
	 */
	public function maxResults($maxResults = null)
	{
		if ($maxResults !== null) {
			$this->maxResults = (int) $maxResults;
		}
		return $this->maxResults;
	}
	
	/**
	 * Get or set the number or records to offset the limit by
	 * 
	 * @return int
	 */
	public function resultOffset($offset = null)
	{
		if ($offset !== null) {
			$this->resultOffset = (int) $offset;
		}
		return $this->resultOffset;
	}
}