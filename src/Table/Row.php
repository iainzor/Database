<?php
namespace Database\Table;

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
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->data($data);
	}
	
	/**
	 * Get or set the values in the row
	 * 
	 * @param array $data
	 * @return array
	 */
	public function data(array $data = null)
	{
		if ($data !== null) {
			$this->columns = [];
			$this->data = $data;

			foreach ($data as $name => $value) {
				$this->columns[] = new Column($name);
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
		return isset($this->data[$column]) ? $this->data[$column] : $defaultValue;
	}
}
