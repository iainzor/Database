<?php
namespace Database\Query;

use Database\Query\QueryInterface;

class WhereGroup
{
	/**
	 * @var array
	 */
	private $exprs = [];
	
	/**
	 * @var int
	 */
	private $compare = QueryInterface::COMPARE_AND;
	
	/**
	 * @var int
	 */
	private $linkCompare = QueryInterface::COMPARE_AND;
	
	/**
	 * Constructor
	 * 
	 * @param array $exprs
	 * @param int $compare
	 * @param int $linkCompare
	 */
	public function __construct(array $exprs, $compare = QueryInterface::COMPARE_AND, $linkCompare = QueryInterface::COMPARE_AND)
	{
		$this->exprs($exprs);
		$this->compare = $compare;
		$this->linkCompare = $linkCompare;
	}
	
	/**
	 * Get or add expressions to the group
	 * 
	 * @param mixed $exprs
	 * @return WhereExpr[]
	 */
	public function exprs($exprs = null)
	{
		if ($exprs !== null) {
			if (!is_array($exprs)) {
				$exprs = [$exprs];
			}
			
			foreach ($exprs as $key => $expr) {
				if (!($expr instanceof WhereExpr)) {
					if (!is_numeric($key)) {
						$expr = [$key, "=", $expr];
					}
					
					$expr = new WhereExpr($expr);
				}
				$this->exprs[] = $expr;
			}
		}
		
		return $this->exprs;
	}
	
	/**
	 * Get or set how to compare the expressions in the group
	 * 
	 * @param int $compare
	 * @return int
	 */
	public function compare($compare = null)
	{
		if ($compare !== null) {
			$this->compare = (int) $compare;
		}
		return $this->compare;
	}
	
	/**
	 * Define how this group should be linked to other groups in a query
	 * 
	 * @param int $compare
	 * @return int
	 */
	public function linkCompare($compare = null)
	{
		if ($compare !== null) {
			$this->linkCompare = (int) $compare;
		}
		return $this->linkCompare;
	}
}