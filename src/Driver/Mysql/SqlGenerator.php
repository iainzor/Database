<?php
namespace Database\Driver\Mysql;

use Database\Driver\SqlGeneratorInterface,
	Database\Query;

class SqlGenerator implements SqlGeneratorInterface 
{
	/**
	 * Generate a SQL statement for the query provided
	 * 
	 * @param \Database\Query\AbstractQuery $query
	 * @return string
	 * @throws \Exception
	 */
	public function generate(Query\AbstractQuery $query)
	{
		if (!$query->table()) {
			throw new \Exception("Query must have a base table assigned to it");
		}
		
		if ($query instanceof Query\SelectQuery) {
			$generator = new SelectSqlGenerator($query);
			return $generator->generate();
		} else if ($query instanceof Query\InsertQuery) {
			$generator = new InsertSqlGenerator($query);
			return $generator->generate();
		} else if ($query instanceof Query\UpdateQuery) {
			$generator = new UpdateSqlGenerator($query);
			return $generator->generate();
		}
		
		throw new \Exception("Could not generate a SQL statement using the query '". get_class($query) ."'");
	}
	
	/**
	 * @return string
	 */
	public function generateFoundRowsSql() 
	{
		return "SELECT FOUND_ROWS()";
	}
}