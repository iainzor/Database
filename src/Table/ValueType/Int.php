<?php
namespace Database\Table\ValueType;

class Int implements TypeInterface
{
	/**
	 * Parse a value as an integer
	 * 
	 * @param mixed $value
	 * @param int $length
	 * @return int
	 */
	public function parseValue($value, $length = null) 
	{
		return (int) $value;
	}
}