<?php
namespace Database\Table\ValueType;

interface TypeInterface
{
	public function parseValue($value, $length = null);
}