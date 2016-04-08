<?php
namespace Database\Table\ValueType;

class Date implements TypeInterface
{
	public function parseValue($value, $length = null) 
	{
		return $value;
	}
}