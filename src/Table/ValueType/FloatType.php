<?php
namespace Database\Table\ValueType;

class FloatType implements TypeInterface
{
	public function parseValue($value, $length = null) 
	{
		return (float) $value;
	}
}