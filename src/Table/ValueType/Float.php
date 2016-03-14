<?php
namespace Database\Table\ValueType;

class Float implements TypeInterface
{
	public function parseValue($value, $length = null) 
	{
		return (float) $value;
	}
}