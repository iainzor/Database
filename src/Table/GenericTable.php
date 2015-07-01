<?php
namespace Database\Table;

use Database\Config;

class GenericTable extends AbstractTable 
{
	private $_connectionId;
	private $_name;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param string $connectionId
	 */
	public function __construct($name, $connectionId = Config::DEFAULT_CONNECTION)
	{	
		$this->_name = $name;
		$this->_connectionId = $connectionId;
	}
	
	public function connectionId() { return $this->_connectionId; }

	public function defaultName() { return $this->_name; }
}