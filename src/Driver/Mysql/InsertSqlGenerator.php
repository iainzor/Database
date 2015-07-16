<?php
namespace Database\Driver\Mysql;

use Database\Query\InsertQuery,
	Database\Table\Column;

class InsertSqlGenerator
{
	/**
	 * @var InsertQuery
	 */
	private $query;
	
	/**
	 * Constructor
	 * 
	 * @param InsertQuery $query
	 */
	public function __construct(InsertQuery $query)
	{
		$this->query = $query;
	}
	
	/**
	 * Generate the INSERT statement
	 * 
	 * @return string
	 * @throws \Exception
	 */
	public function generate()
	{
		$table = $this->query->table();
		$rows = $this->query->rows();
		
		if (!$table) {
			throw new \Exception("No table has been given to the InsertQuery");
		}
		
		if (!count($rows)) {
			throw new \Exception("No rows have been added to the InsertQuery");
		}
		
		$parts = [
			"INSERT INTO `{$table->name()}`",
			$this->columnList(),
			$this->valueList(),
			$this->onDuplicateClause()
		];
			
		return implode(" ", $parts);
	}
	
	/**
	 * Generate a list of columns data exists for
	 * 
	 * @return string
	 */
	public function columnList()
	{
		$columns = $this->columns();
		
		return "(". implode(", ", array_map(function($name) { 
			return "`{$name}`"; 
		}, $columns)) .")";
	}
	
	/**
	 * Generate a list of a values to be inserted
	 * 
	 * @return string
	 */
	public function valueList()
	{
		$db = $this->query->db();
		$columns = $this->columns();
		$rows = [];
		
		foreach ($this->query->rows() as $row) {
			$r = [];
			foreach ($columns as $columnName) {
				$value = $row->value($columnName);
				if ($value !== null) {
					$value = $db->quote($value);
				} else if ($value === true || $value === false) {
					$value = (int) $value;
				} else if ($value === null) {
					$value = "NULL";
				}
				
				$r[] = $value;
			}
			$rows[] = "(". implode(", ", $r) .")";
		}
		
		return "VALUES ". implode(", ", $rows);
	}
	
	/**
	 * Get a list of all unique columns listed in the row data
	 * 
	 * @return array
	 */
	private function columns()
	{
		$columns = [];
		foreach ($this->query->rows() as $row) {
			foreach ($row->columns() as $column) {
				if (!in_array($column->name(), $columns)) {
					$columns[] = $column->name();
				}
			}
		}
		return $columns;
	}
	
	/**
	 * Generate the ON DUPLICATE KEY clause if the query has columns set to update
	 * 
	 * @return string
	 */
	private function onDuplicateClause()
	{
		$columns = $this->query->updateColumns();
		$db = $this->query->db();
		
		if (count($columns)) {
			$parts = [];
			foreach ($columns as $name => $value) {
				if (is_numeric($name)) {
					$name = $value;
					$value = "VALUES(`{$name}`)";
				} else if ($value === null) {
					$value = "NULL";
				} else {
					$value = $db->quote($value);
				}
				$parts[] = "`{$name}` = {$value}";
			}
			return "ON DUPLICATE KEY UPDATE ". implode(", ", $parts);
		}
		
		return null;
	}
}