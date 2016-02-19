<?php
namespace Database\Table;

use Database\Model\ModelInterface;

class Row
{
	/**
	 * @var ModelInterface
	 */
	private $data;
	
	/**
	 * @var Column[]
	 */
	private $columns = [];
	
	/**
	 * Constructor
	 * 
	 * @param ModelInterface $data
	 */
	public function __construct(ModelInterface $data)
	{
		$this->data($data);
	}
	
	/**
	 * Get or set the row's data model
	 * 
	 * @param ModelInterface $data
	 * @return ModelInterface
	 */
	public function data(ModelInterface $data = null)
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
		return $this->data->getSet($column);
	}
}
