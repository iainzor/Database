<?php
namespace Database\Query;

use Database\Table,
	Database\PDO,
	Database\Model,
	Bliss\Component;

abstract class AbstractQuery implements QueryInterface
{
	/**
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * @var \Database\Table\AbstractTable
	 */
	protected $table;
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db($db);
	}
	
	/**
	 * Get or set the PDO instance
	 * 
	 * @param PDO $db
	 * @return PDO
	 */
	public function db(PDO $db = null)
	{
		if ($db) {
			$this->db = $db;
		}
		return $this->db;
	}
	
	/**
	 * Get or set the table to run the query on
	 * 
	 * @param mixed $table
	 * @return \Database\Table\AbstractTable
	 * @see \Database\Table\AbstractTable::factory()
	 */
	public function table($table = null)
	{
		if ($table !== null) {
			$this->table = Table\AbstractTable::factory($table, $this->db);
		}
		
		return $this->table;
	}
	
	/**
	 * Generate a SQL statement from the query
	 * 
	 * @throws \Exception
	 * @return string
	 */
	public function generateSQL()
	{
		if (!$this->db()) {
			throw new \Exception("No database instance has been given to the query");
		}
		
		$driverFactory = $this->db()->driverFactory();
		if ($driverFactory) {
			return $driverFactory->sqlGenerator()->generate($this);
		}
	}
	
	/**
	 * Generate a new model instance from a single row of data
	 * 
	 * @param array|Model\AbstractModel $data
	 * @return Model\AbstractModel
	 */
	public function generateModel($data)
	{
		$table = $this->table();
		$data = Component::convertValueToArray($data);
		
		if (!$table) {
			throw new \Exception("Cannot generate a model without a table instance");
		}

		if ($table instanceof Model\ModelGeneratorInterface) {
			return $table->generateModel($data);
		} else {
			$model = new Model\GenericModel($table->alias());
			return Model\GenericModel::populate($model, $data);
		}
	}
}