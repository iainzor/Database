<?php
namespace Database\Query;

class WhereExpr
{
	/**
	 * @var mixed
	 */
	private $expr;
	
	/**
	 * Constructor
	 * 
	 * @param mixed $expr
	 */
	public function __construct($expr)
	{
		$this->expr = $expr;
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		return $this->expr;
	}
}