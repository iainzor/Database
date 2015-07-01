<?php
namespace Database\Driver;

interface DriverFactoryInterface
{
	/**
	 * @return SqlGeneratorInterface
	 */
	public function sqlGenerator();
}