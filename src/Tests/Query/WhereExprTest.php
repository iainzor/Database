<?php
namespace Database\Tests\Query;

use Database\Query\WhereExpr,
	Database\Query\QueryInterface;

class WhereExprTest extends \PHPUnit_Framework_TestCase
{
	public function testStringExpr()
	{
		$expr = new WhereExpr("foo = :bar");
		
		$this->assertTrue($expr->isString());
	}
	
	public function testMySQLFunc()
	{
		$expr = new WhereExpr(["UNIX_TIMESTAMP(`updated`)", QueryInterface::OP_GREATER_THAN, ":lastUpdated"]);
		
		$this->assertTrue($expr->isCompound());
		$this->assertEquals(QueryInterface::OP_GREATER_THAN, $expr->operator());
	}
	
	public function testNormalCompound()
	{
		$expr = new WhereExpr(["foo", QueryInterface::OP_EQUAL_TO, ":bar"]);
		
		$this->assertTrue($expr->isCompound());
		$this->assertEquals("foo", $expr->column());
	}
}