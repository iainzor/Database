<?php
namespace Database\Model;

class GenericModel extends AbstractModel
{
	/**
	 * @var string
	 */
	private $resourceName;
	
	/**
	 * Constructor
	 * 
	 * @param string $resourceName
	 */
	public function __construct($resourceName) 
	{
		$this->resourceName = $resourceName;
	}
	
	/**
	 * @return string
	 */
	public function getResourceName() 
	{
		return $this->resourceName;
	}
}