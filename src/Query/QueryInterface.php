<?php
namespace Database\Query;

use Database\PDO;

interface QueryInterface
{
	const COMPARE_AND = 0;
	const COMPARE_OR = 1;
	
	const SORT_ASC = 10;
	const SORT_DESC = 11;
	
	const JOIN_DEFAULT = 100;
	const JOIN_LEFT = 101;
	const JOIN_RIGHT = 102;
	
	public function db(PDO $db = null);
	
	public function table($table = null);
}