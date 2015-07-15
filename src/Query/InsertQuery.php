<?php
namespace Database\Query;

use Database\Table\Row;

class InsertQuery extends AbstractQuery
{
	/**
	 * @var Row[]
	 */
	private $rows = [];
	
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
	 * @param array|Row $row
	 */
	public function addRow($row)
	{
		if (is_array($row)) {
			$row = new Row($row);
		}
		
		if (!($row instanceof Row)) {
			throw new \Exception("Row must be an array of data or an instance of \\Database\\Table\\Row");
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
	 * Execute the insert query and return the last inserted ID
	 * 
	 * @return mixed
	 */
	public function execute()
	{
		$factory = $this->db()->driverFactory();
		$sql = $factory->sqlGenerator()->generate($this);
		
		$this->db()->exec($sql);
		
		return $this->db()->lastInsertId();
	}
}