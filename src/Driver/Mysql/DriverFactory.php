<?php
namespace Database\Driver\Mysql;

use Database\Driver\DriverFactoryInterface;

class DriverFactory implements DriverFactoryInterface
{
	/**
	 * @return \Database\Driver\Mysql\SqlGenerator
	 */
	public function sqlGenerator() 
	{
		return new SqlGenerator();
	}
}