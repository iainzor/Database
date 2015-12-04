<?php
namespace Database\Table;

use Database\Model\AbstractModel;

class Row
{
	/**
	 * @var array
	 */
	private $data = [];
	
	/**
	 * @var Column[]
	 */
	private $columns = [];
	
	/**
	 * Constructor
	 * 
	 * @param array|AbstractModel $data
	 */
	public function __construct($data)
	{
		$this->data($data);
	}
	
	/**
	 * Get or set the row's data model
	 * 
	 * @param AbstractModel $data
	 * @return AbstractModel
	 */
	public function data(AbstractModel $data = null)
	{
		if ($data !== null) {
			$this->columns = [];
			$this->data = $data;
			$keys = array_keys($data->toBasicArray());

			foreach ($keys as $key) {
				$this->columns[] = new Column($key);
			}
		}
		
		return $this->data;
	}
	
	/**
	 * Get the columns available to the row
	 * 
	 * @return Column[]
	 */
	public function columns()
	{
		return $this->columns;
	}
	
	/**
	 * Attempt to get the column of a column in the row
	 * 
	 * @param string $column
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function value($column, $defaultValue = null)
	{
		$value = $this->data->getSet($column);
		return empty($value) ? $defaultValue : $value;
	}
}
