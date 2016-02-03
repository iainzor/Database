<?php
namespace Database\Table;

use Database\PDO,
	Database\Model\ModelGeneratorInterface,
	Database\Model\ModelGeneratorTrait,
	Database\Model\GenericModel;

class GenericTable extends AbstractTable implements ModelGeneratorInterface
{
	use ModelGeneratorTrait;
	
	/**
	 * @var string
	 */
	private $_name;
	
	/**
	 * @var string
	 */
	private $_modelClassName;
	
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
	 * Get or set the model class name used when generating models
	 * 
	 * @param string $name
	 * @return string
	 */
	public function modelClassName($name = null)
	{
		if ($name !== null) {
			$this->_modelClassName = $name;
		}
		return $this->_modelClassName;
	}
	
	/**
	 * @return string
	 */
	public function defaultName() { return $this->_name; }
	
	/**
	 * Create a model instance for the table
	 * If no class name has been set, GenericModel will be used
	 * 
	 * @return \Database\Model\AbstractModel
	 */
	public function createModelInstance() 
	{
		$className = $this->modelClassName();
		if ($className) {
			return new $className();
		} else {
			return new \Database\Model\GenericModel($this->name());
		}
	}
}