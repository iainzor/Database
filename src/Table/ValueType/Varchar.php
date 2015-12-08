<?php
namespace Database\Table\ValueType;

class Varchar implements TypeInterface
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
		if ($length !== null) {
			return substr($value, 0, $length);
		}
		return $value;
	}
}