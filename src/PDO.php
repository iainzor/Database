<?php
namespace Database;

class PDO extends \PDO
{
	/**
	 * @var array
	 */
	private $logs = [];
	
	/**
	 * @var string
	 */
	private $schemaName = null;
	
	/**
	 * @var string
	 */
	private $driver;
	
	/**
	 * @var Table\Structure[]
	 */
	private $tables = [];
	
	/**
	 * Overrides the default constructor to keep track of the driver being used
	 * 
	 * @param string $dsn
	 * @param string $username
	 * @param string $passwd
	 * @param string $options
	 */
	public function __construct($dsn, $username = null, $passwd = null, $options = []) 
	{
		parent::__construct($dsn, $username, $passwd, $options);
		
		$this->driver = preg_replace("/^([a-z0-9]+):.*$/i", "$1", $dsn);
	}
	
	/**
	 * Get all database logs
	 * 
	 * @return array
	 */
	public function logs()
	{
		return $this->logs;
	}
	
	/**
	 * Get or set the name of the driver being used
	 * 
	 * @return string
	 */
	public function driver($driver = null)
	{
		if ($driver !== null) {
			$this->driver = $driver;
		}
		return $this->driver;
	}
	
	/**
	 * Attempt to get the factory for the database driver
	 * 
	 * @return \Database\Driver\DriverFactoryInterface
	 * @throws \Exception
	 */
	public function driverFactory()
	{
		$className = __NAMESPACE__ ."\\Driver\\". ucfirst($this->driver) ."\\DriverFactory";
		if (class_exists($className)) {
			return new $className();
		}
		return false;
		//throw new \Exception("No driver factory found for '{$this->driver}'");
	}
	
	/**
	 * Get or set the name of the database currently connected to
	 * 
	 * @return string
	 */
	public function schemaName($name = null)
	{
		if ($name !== null) {
			$this->schemaName = $name;
		}
		
		if ($this->schemaName === null) {
			$this->schemaName = $this->fetchColumn("SELECT DATABASE()");
		}
		
		return $this->schemaName;
	}
	
	/**
	 * Fetch all results of a SQL statement
	 *
	 * @param string|Query\Query $query
	 * @param array $params
	 * @param int $fetchStyle
	 * @return array
	 */
	public function fetchAll($query, array $params = [], $fetchStyle = \PDO::FETCH_ASSOC)
	{
		$statement = $this->_exec($query, $params);
		$results = $statement->fetchAll($fetchStyle);
		
		unset($statement);
		return $results;
	}

	/**
	 * Fetch a single row from a SQL statement
	 *
	 * @param string $sql
	 * @param array $params
	 * @param int $fetchStyle
	 * @param int $rowOffset
	 * @return mixed
	 */
	public function fetchRow($sql, array $params = [], $fetchStyle = \PDO::FETCH_ASSOC, $rowOffset = 0)
	{
		$statement = $this->_exec($sql, $params);
		$result = $statement->fetch($fetchStyle, \PDO::FETCH_ORI_NEXT, $rowOffset);
		
		unset($statement);
		return $result;
	}

	/**
	 * Fetch a single column's value from a SQL statement
	 * If no results can be found, NULL will be returned
	 *
	 * @param string $sql
	 * @param array $params
	 * @param int $columnNumber
	 * @return mixed
	 */
	public function fetchColumn($sql, array $params = [], $columnNumber = 0)
	{
		$statement = $this->_exec($sql, $params);
		$result = $statement->fetchColumn($columnNumber);
		
		unset($statement);
		return $result;
	}
	
	/**
	 * Override the default query method in order to log the query statement
	 * 
	 * @see \PDO::query()
	 * @param string $statement
	 * @return \PDOStatement|false
	 */
	public function query($statement) {
		$startTime = microtime(true);
		$result = parent::query($statement);
		$totalTime = microtime(true) - $startTime;
		
		$this->logs[] = [
			"sql" => $statement,
			"totalTime" => $totalTime
		];
		
		return $result;
	}
	
	/**
	 * Override the default exec method in order to log the query statement
	 * 
	 * @see \PDO::exec()
	 * @param string $statement
	 * @param array $params Binding parameters for the query
	 * @return int
	 */
	public function exec($statement, array $params = []) 
	{
		$exception = false;
		$startTime = microtime(true);
		$log = [
			"sql" => $statement
		];
		
		try {
			
			$stmt = $this->prepare($statement);
			$stmt->execute($params);
			$result = $stmt->rowCount();
		} catch (\PDOException $e) {
			$log["error"] = $e->getMessage();
			$exception = $e;
		}
		
		$log["totalTime"] = microtime(true) - $startTime;
		
		$this->logs[] = $log;
		
		if ($exception !== false) {
			throw $exception;
		}
		
		return $result;
	}
	
	/**
	 * Create a PDOStatement from a SQL string, execute it, log it and return it
	 * 
	 * @param string $sql
	 * @param array $params
	 * @return \PDOStatement
	 */
	private function _exec($sql, array $params = [])
	{
		$startTime = microtime(true);
		$statement = $this->prepare($sql);
		
		if (!$statement) {
			$errorInfo = $this->errorInfo();
			throw new \Exception("Could not prepare statement: {$errorInfo[2]}");
		}
		
		$statement->execute($params);
		$totalTime = microtime(true) - $startTime;
		
		$this->logs[] = [
			"sql" => $sql,
			"params" => $params,
			"totalTime" => $totalTime
		];
		
		return $statement;
	}
	
	/**
	 * Get the structure of a table
	 * 
	 * @param mixed $table
	 * @return \Database\Table\Structure
	 */
	public function describe($table)
	{
		$table = Table\AbstractTable::factory($table, $this);
		$tableName = $table->name();
		
		if (!isset($this->tables[$tableName])) {
			$structure = new Table\Structure();
			$factory = $this->driverFactory();
			$factory->populateStructure($this, $table, $structure);
		
			$this->tables[$tableName] = $structure;
		}
		
		return $this->tables[$tableName];
	}
	
	public function quote($string, $parameter_type = self::PARAM_STR) {
		if (is_array($string)) {
			throw new \Exception("Expected string, got array");
		}
		return parent::quote($string, $parameter_type);
	}
}