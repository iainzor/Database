<?php
namespace Database\Table\ValueType;

class Int implements TypeInterface
{
	/**
	 * Parse a value as a varchar
	 * 
	 * @param mixed $value
	 * @param int $length
	 * @return string
	 */
	public function parseValue($value, $length = null) 
	{
		return (int) $value;
	}
}