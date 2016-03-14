<?php
namespace Database\Table\ValueType;

class Polygon implements TypeInterface
{
	public function parseValue($value, $length = null) 
	{
		return $value;
	}
}