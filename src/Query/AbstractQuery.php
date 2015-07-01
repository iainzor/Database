<?php
namespace Database\Query;

use Database\Table;

abstract class AbstractQuery implements QueryInterface
{
	/**
	 * @var \Database\Table\AbstractTable
	 */
	protected $table;
	
	/**
	 * Get or set the table to run the query on
	 * 
	 * @param mixed $table	This can either be a string of the table's name, an array as [tableName, tableAlias] 
	 *						or an instance of \Database\Table\AbstractTable
	 * @return \Database\Table\AbstractTable
	 */
	public function table($table = null)
	{
		if ($table !== null) {
			if ($table instanceof Table\AbstractTable) {
				$this->table = $table;
			} else if (is_string($table)) {
				$this->table = new Table\GenericTable($table);
			} else if (is_array($table)) {
				$tableName = $table[0];
				$tableAlias = isset($table[1]) ? $table[1] : null;
				$this->table = new Table\GenericTable($tableName);
				$this->table->alias($tableAlias);
			} else {
				throw new \InvalidArgumentException("Could not create table instance from passed value");
			}
		}
		
		return $this->table;
	}
}