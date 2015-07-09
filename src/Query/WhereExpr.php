<?php
namespace Database\Query;

use Database\PDO,
	Database\Table\Column;

class WhereExpr
{
	const TYPE_STRING = 1;
	const TYPE_COMPOUND = 2;
	
	/**
	 * @var mixed
	 */
	private $expr;
	
	/**
	 * @var int
	 */
	private $type;
	
	/**
	 * @var string
	 */
	private $column;
	
	/**
	 * @var mixed
	 */
	private $operator;
	
	/**
	 * @var mixed
	 */
	private $value;
	
	/**
	 * Constructor
	 * 
	 * @param mixed $expr
	 */
	public function __construct($expr)
	{
		$this->expr = $expr;
		
		if (is_string($expr)) {
			$this->type = self::TYPE_STRING;
		} else if (is_array($expr)) {
			$this->type = self::TYPE_COMPOUND;
			$this->parseCompound($expr);
		}
	}
	
	/**
	 * Parse a compound expression
	 * 
	 * @param array $expr
	 */
	private function parseCompound(array $expr)
	{
		$values = array_values($expr);
		
		$this->column = $values[0];
		$this->operator = isset($values[1]) ? $values[1] : null;
		$this->value = isset($values[2]) ? $values[2] : null;
	}
	
	/**
	 * Check if the expression is a string
	 * 
	 * @return boolean
	 */
	public function isString()
	{
		return $this->type === self::TYPE_STRING;
	}
	
	/**
	 * Check if the expression is a compound expression
	 * Compound expressions are multi-part arrays
	 * 
	 * @return boolean
	 */
	public function isCompound()
	{
		return $this->type === self::TYPE_COMPOUND;
	}
	
	/**
	 * Get the original expression
	 * 
	 * @return mixed
	 */
	public function expr()
	{
		return $this->expr;
	}
	
	/**
	 * Get the column used in the compound expression
	 * 
	 * @return string
	 */
	public function column()
	{
		return $this->column;
	}
	
	/**
	 * Get the operator of the expression
	 * 
	 * @return mixed
	 */
	public function operator()
	{
		return $this->operator;
	}
	
	/**
	 * Get the value used to compare to the column
	 * 
	 * @return mixed
	 */
	public function value()
	{
		return $this->value;
	}
}