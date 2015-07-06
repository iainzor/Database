<?php
namespace Database\Table;

abstract class AbstractTable
{
	private static $dbModule;
	private $db;

	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var string
	 */
	private $alias;
	
	/**
	 * @var Column[]
	 */
	private $columns = [];

	abstract public function defaultName();
	abstract public function connectionId();
	
	/**
	 * Get or set the name of the table
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		if ($name !== null) {
			$this->name = $name;
		}
		if (!$this->name) {
			$this->name = $this->defaultName();
		}
		return $this->name;
	}
	
	/**
	 * Get or set the alias of the table.  If no alias has been set, the table's name will be returned
	 * 
	 * @param string $alias
	 * @return string
	 */
	public function alias($alias = null)
	{
		if ($alias !== null) {
			$this->alias = $alias;
		}
		return isset($this->alias) ? $this->alias : $this->name();
	}

	/**
	 * Get a column from the table
	 * If the column requested doesn't exist, it will be created and added to the table's columns
	 * 
	 * @param string $name
	 * @return Column
	 */
	public function column($name)
	{
		if (!isset($this->columns[$name])) {
			$this->columns[$name] = new Column($name, $this);
		}
		return $this->columns[$name];
	}
}