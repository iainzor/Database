<?php
namespace Database\Table;

use Database\PDO,
	Database\Query,
	Database\Registry,
	Database\Config,
	Database\Model\AbstractModel;

abstract class AbstractTable
{
	/**
	 * @var Registry
	 */
	private static $dbRegistry;
	
	/**
	 * @var string
	 */
	protected $connectionId = Config::DEFAULT_CONNECTION;
	
	/**
	 * @var PDO
	 */
	private $db;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var string
	 */
	private $alias;
	
	/**
	 * @var Structure
	 */
	private $structure;
	
	/**
	 * Set the database registry used to find database connections for tables
	 * 
	 * @param Registry $registry
	 */
	public static function dbRegistry(Registry $registry)
	{
		self::$dbRegistry = $registry;
	}
	
	/**
	 * Generate a new AbstractTable instance using a mixed value
	 * 
	 * Possible values are an instance of AbstractTable, a string, or an array.  If
	 * an array is used, it should only contain two items as [$tableName, $tableAlias]
	 * 
	 * @param mixed $table
	 * @param PDO $db
	 * @return \Database\Table\AbstractTable
	 * @throws \InvalidArgumentException
	 */
	public static function factory($table, PDO $db)
	{
		if (is_string($table)) {
			$table = new GenericTable($table, $db);
		} else if (is_array($table)) {
			$tableName = $table[0];
			$tableAlias = isset($table[1]) ? $table[1] : null;
			$table = new GenericTable($tableName, $db);
			$table->alias($tableAlias);
		}
		
		if (!$table instanceof AbstractTable) {
			throw new \InvalidArgumentException("Could not create table instance from passed value");
		}

		if (!$table->db()) {
			$table->db($this->db());
		}
		
		return $table;
	}

	/**
	 * @return string
	 */
	abstract public function defaultName();
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct(PDO $db = null)
	{
		if ($db !== null) {
			$this->db = $db;
		}
		
		if ($this instanceof StructureProviderInterface) {
			$this->structure = new Structure();
			$this->initStructure($this->structure);
		}
	}
	
	/**
	 * Get or set the connection ID of the table
	 * 
	 * @param string $id
	 * @return string
	 */
	public function connectionId($id = null)
	{
		if ($id !== null) {
			$this->connectionId = $id;
			$this->db = null;
		}
		return $this->connectionId;
	}
	
	/**
	 * Get or set the PDO instance for the table
	 * 
	 * @param PDO $db
	 * @return PDO
	 */
	public function db(PDO $db = null)
	{
		if ($db !== null) {
			$this->db = $db;
		}
		
		if (!$this->db && self::$dbRegistry) {
			$this->db = self::$dbRegistry->get($this->connectionId);
		} else if (!$this->db) {
			throw new \Exception("No database or connection registry has been provided to the table");
		}
		
		return $this->db;
	}
	
	/**
	 * Get or set the name of the table
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		if ($name !== null) {
			$this->name = $name;
		}
		if (!$this->name) {
			$this->name = $this->defaultName();
		}
		return $this->name;
	}
	
	/**
	 * Generate a fully qualified name for the table: [dbName.]tableName
	 * If no schema name can be found it will be left out.
	 * 
	 * @param type $quoted
	 * @return type
	 */
	public function fullName($quoted = false)
	{
		$schema = $this->db()->schemaName();
		$table = $this->name();
		$parts = empty($schema) ? [$table] : [$schema, $table];
		
		return $quoted
			? "`". implode("`.`", $parts) ."`"
			: implode(".", $parts);
	}
	
	/**
	 * Get or set the alias of the table.  If no alias has been set, the table's name will be returned
	 * 
	 * @param string $alias
	 * @return string
	 */
	public function alias($alias = null)
	{
		if ($alias !== null) {
			$this->alias = $alias;
		}
		return isset($this->alias) ? $this->alias : $this->name();
	}
	
	/**
	 * Get or set the table's structure 
	 * 
	 * If a structure has not been defined, one will be generated using the 
	 * description of the table from the database
	 * 
	 * @param Structure $structure
	 * @return Structure
	 */
	public function structure(Structure $structure = null)
	{
		if ($structure !== null) {
			$this->structure = $structure;
		}
		if (!$this->structure) {
			$this->structure = $this->db()->describe($this);
		}
		return $this->structure;
	}

	/**
	 * Get a column from the table
	 * If the column requested doesn't exist, it will be created and added to the table's columns
	 * 
	 * @param string $name
	 * @param array $config
	 * @return Column
	 */
	public function column($name, array $config = null)
	{
		$column = $this->structure()->column($name, $config);
		$column->table($this);
		
		return $column;
	}
	
	/**
	 * Create a new SelectQuery for the table
	 * 
	 * @param array $columns Optional column names to retrieve, defaults to all
	 * @return \Database\Query\SelectQuery
	 */
	public function select(array $columns = null)
	{
		$query = new Query\SelectQuery($this->db());
		$query->from($this);
		
		if ($columns !== null) {
			$query->columns($columns);
		}
		
		return $query;
	}
	
	/**
	 * Find a single record from the table
	 * 
	 * @param mixed $where
	 * @param array $params
	 * @return \Database\Model\AbstractModel
	 */
	public function find($where, array $params = [])
	{
		$query = $this->select();
		$query->where($where);
		
		return $query->fetchRow($params);
	}
	
	/**
	 * Insert a row into the table and return its model with its ID assigned
	 * 
	 * @param array|AbstractModel $model
	 * @param array $updateColumns If not empty, the query will update these columns if a unique conflict arises
	 * @return \Database\Model\AbstractModel
	 */
	public function insert($model, array $updateColumns = [])
	{
		$query = new Query\InsertQuery($this->db());
		$query->into($this);
		$query->addRow($model);
		$query->onDuplicateKeyUpdate($updateColumns);
		
		return $query->execute()[0];
	}
	
	/**
	 * Create a new UpdateQuery instance for the table
	 * 
	 * @return \Database\Query\UpdateQuery
	 */
	public function update()
	{
		$query = new Query\UpdateQuery($this->db());
		$query->table($this);
		
		return $query;
	}
}