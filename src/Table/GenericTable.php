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
		parent::__construct($db);
		
		$this->_name = $name;
	}
	
	/**
	 * @return string
	 */
	public function defaultName() { return $this->_name; }
}