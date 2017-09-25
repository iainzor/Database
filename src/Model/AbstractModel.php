<?php
namespace Database\Model;

use Bliss\Component;

abstract class AbstractModel extends Component implements ModelInterface
{
	/**
	 * Utility method used to get or set date properties
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @return int
	 */
	protected function getSetDate($field, $value)
	{
		if (!empty($value)) {
			if (!is_numeric($value)) {
				if (preg_match("/0*-*:*/i", $value)) {
					$value = null;
				} else {
					$value = strtotime($value);
				}
			}
		}
		return $this->getSet($field, $value, self::VALUE_INT);
	}
	
	public function toBasicArray() 
	{
		return $this->toArray();
	}
	
	public function __call($name, array $args = []) 
	{
		$value = null;
		if (isset($args[0])) {
			$value = $args[0];
		}
		return $this->getSet($name, $value);
	}
}