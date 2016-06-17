<?php
namespace Database\Table\ValueType;

class Enum implements TypeInterface 
{
	public function parseValue($value, $length = null) 
	{
		return $value;
	}
}