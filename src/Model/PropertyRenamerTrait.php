<?php
namespace Database\Model;

trait PropertyRenamerTrait
{
	abstract public function propertyMap();
	
	public function __call($name, array $args = array()) 
	{
		$map = $this->propertyMap();
		if (!is_array($map)) {
			throw new \Exception("Property map must be a [key => value] array");
		}
		
		if (isset($map[$name])) {
			$method = $map[$name];
			$value = isset($args[0]) ? $args[0] : null;
			
			if ($method !== false) {
				return call_user_func([$this, $method], $value);
			}
		} else {
			return parent::__call($name, $args);
		}
	}
}