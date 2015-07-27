<?php
namespace Database\Table\ValueType;

class BooleanType implements TypeInterface
{
	/**
	 * Parse a value as a boolean
	 * 
	 * @param mixed $value
	 * @param int $length
	 * @return boolean
	 */
	public function parseValue($value, $length = null) 
	{
		return (boolean) $value;
	}
}