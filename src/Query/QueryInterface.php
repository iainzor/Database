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
	
	const OP_EQUAL_TO = 1000;
	const OP_NOT_EQUAL_TO = 1001;
	const OP_GREATER_THAN = 1002;
	const OP_GREATER_OR_EQUAL_TO = 1003;
	const OP_LESS_THAN = 1004;
	const OP_LESS_OR_EQUAL_TO = 1005;
	const OP_CONTAINS = 1006;
	const OP_STARTS_WITH = 1007;
	const OP_ENDS_WITH = 1008;
	
	public function db(PDO $db = null);
	
	public function table($table = null);
}