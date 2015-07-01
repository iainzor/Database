<?php
namespace Database\Tests;

use Database\PDO;

class TestDb
{
	/**
	 * @var PDO
	 */
	private static $pdo;
	
	public static function pdo()
	{
		if (!self::$pdo) {
			self::$pdo = new PDO("mysql:dbname=bliss_database_tests;host=127.0.0.1", "root", null, [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]);
		}
		return self::$pdo;
	}
}