<?php
namespace Database\Table\ValueType;

class Double implements TypeInterface
{
	public function parseValue($value, $length = null) 
	{
		return (double) $value;
	}
}