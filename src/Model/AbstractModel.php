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
		if ($value !== null) {
			if (!is_numeric($value)) {
				$value = strtotime($value);
			}
		}
		return $this->getSet($field, $value, self::VALUE_INT);
	}
}