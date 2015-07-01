<?php
namespace Database\Query;

use Database\PDO;

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
	 * @param \Database\PDO
	 * @return string
	 */
	public function toString(PDO $db)
	{
		if (is_array($this->expr)) {
			$column = $this->expr[0];
			$operator = isset($this->expr[2]) ? $this->expr[1] : "=";
			$value = isset($this->expr[2]) ? $this->expr[2] : $this->expr[1];
			
			if (substr($value, 0, 1) !== ":") {
				$value = $db->quote($value);
			}
			
			return implode(" ", [$column, $operator, $value]);
		}
		
		return $this->expr;
	}
}