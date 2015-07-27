<?php
namespace Database\Table;

class ColumnExpr
{
	/**
	 * @var string
	 */
	private $expr;
	
	/**
	 * Constructor
	 * 
	 * @param string $expr
	 */
	public function __construct($expr)
	{
		$this->expr = $expr;
	}
	
	/**
	 * Get or set the expression string
	 * 
	 * @param string $expr
	 */
	public function expr($expr = null)
	{
		if ($expr !== null) {
			$this->expr = $expr;
		}
		
		return $this->expr;
	}
}