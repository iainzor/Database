<?php
namespace Database\Query;

interface QueryInterface
{
	const COMPARE_AND = 0;
	const COMPARE_OR = 1;
	
	const SORT_ASC = 10;
	const SORT_DESC = 11;
	
	public function table($table = null);
}