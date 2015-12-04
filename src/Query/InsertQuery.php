<?php
namespace Database\Query;

use Database\Table\Row,
	Database\Model\AbstractModel;

class InsertQuery extends AbstractQuery
{
	/**
	 * @var Row[]
	 */
	private $rows = [];
	
	/**
	 * @var array
	 */
	private $updateColumns = [];
	
	/**
	 * Set the table to insert into
	 * 
	 * @param mixed $table
	 */
	public function into($table)
	{
		$this->table($table);
	}
	
	/**
	 * Add a row to be inserted
	 * 
	 * @param mixed $data
	 */
	public function addRow($data)
	{
		if (is_array($data)) {
			$row = new Row(
				$this->generateModel($data)
			);
		} else if ($data instanceof AbstractModel) {
			$row = new Row($data);
		} else if ($data instanceof Row) {
			$row = $data;
		}
		
		if (!isset($row)) {
			throw new \Exception("No valid row data was provided.");
		}
		
		$this->rows[] = $row;
	}
	
	/**
	 * Get or set the rows to be inserted
	 * 
	 * @param array $rows
	 * @return Row[]
	 */
	public function rows(array $rows = null)
	{
		if ($rows !== null) {
			foreach ($rows as $row) {
				$this->addRow($row);
			}
		}
		return $this->rows;
	}
	
	/**
	 * Set the columns to update when a duplicate key is found
	 * 
	 * @param array $columns
	 */
	public function onDuplicateKeyUpdate(array $columns)
	{
		$this->updateColumns = $columns;
	}
	
	/**
	 * Get the columns to update if a duplicate key is found
	 * 
	 * @return array
	 */
	public function updateColumns()
	{
		return $this->updateColumns;
	}
	
	/**
	 * Execute the insert query and return the last inserted ID
	 * 
	 * @return \Database\Model\AbstractModel[]
	 */
	public function execute()
	{
		$factory = $this->db()->driverFactory();
		$sql = $factory->sqlGenerator()->generate($this);
		
		$this->db()->exec($sql);
		$lastId = $this->db()->lastInsertId();
		$results = [];
		
		foreach ($this->rows as $row) {
			$model = $row->data();
			$model->id($lastId);
			$results[] = $model;
		}
		
		return $results;
	}
}