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
	 * @var Structure
	 */
	private $structure;
	
	/**
	 * @var Column[]
	 */
	private $columns = [];
	
	/**
	 * Constructor
	 * 
	 * @param ModelInterface $data
	 * @param Structure $structure
	 */
	public function __construct(ModelInterface $data, Structure $structure = null)
	{
		$this->structure = $structure;
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
				if ($this->structure && $this->structure->isColumn($key)) {
					$this->columns[] = $this->structure->column($key);
				} else {
					$this->columns[] = new Column($key);
				}
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
	 * @param string $name The name or alias of the column
	 * @return mixed
	 */
	public function value($name)
	{
		if ($this->structure) {
			$column = $this->structure->column($name);
			$name = $column->alias();
		}
		
		return $this->data->getSet($name);
	}
}
