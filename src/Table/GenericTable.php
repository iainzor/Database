<?php
namespace Database\Table;

use Database\PDO;

class GenericTable extends AbstractTable 
{
	private $_name;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param PDO $db
	 */
	public function __construct($name, PDO $db = null)
	{	
		$this->_name = $name;
		$this->db($db);
	}
	
	/**
	 * @return string
	 */
	public function defaultName() { return $this->_name; }
}