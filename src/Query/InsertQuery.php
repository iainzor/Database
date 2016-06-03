<?php
namespace Database\Query;

use Database\Table\Row,
	Database\Model\ModelInterface;

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
		$struct = isset($this->table) ? $this->table->structure() : null;
		
		if (is_array($data)) {
			$model = $this->generateModel($data);
			$row = new Row($model, $struct);
		} else if ($data instanceof ModelInterface) {
			$row = new Row($data, $struct);
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
		if (count($this->rows) > 0) {
			$factory = $this->db()->driverFactory();
			$sql = $factory->sqlGenerator()->generate($this);

			$this->db()->exec($sql);
			$this->_updateInsertedRows($this->db()->lastInsertId(), $this->rows);

			$results = [];
			foreach ($this->rows as $row) {
				$model = $row->data();
				$results[] = $model;
			}

			return $results;
		} else {
			return [];
		}
	}
	
	/**
	 * Update the IDs of newly inserted rows
	 * 
	 * @param int $lastId
	 * @param \Database\Table\Row[] $rows
	 */
	private function _updateInsertedRows($lastId, array $rows)
	{
		$needIds = [];
		foreach ($rows as $row) {
			if (!$row->data()->id()) {
				$needIds[] = $row->data();
			}
		}
		
		if (count($needIds) > 0) {
			$table = $this->table();
			$structure = $table->structure();

			if ($structure->isColumn("id")) {
				$query = new SelectQuery($this->db());
				$query->from($this->table());
				$query->columns(["id"]);
				$query->limit(count($needIds));
				$query->orderBy("id")->asc();
				$query->where("id >= :lastId");
				
				$lastResults = $query->fetchAll([
					":lastId" => $lastId
				]);
				
				foreach ($lastResults as $i => $result) {
					if (isset($needIds[$i])) {
						$needIds[$i]->id($result->id());
					}
				}
			}
		}
	}
}