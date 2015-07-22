<?php
namespace Database\Table;

use Database\PDO,
	Database\Query,
	Database\Registry,
	Database\Config;

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
	 * @var Column[]
	 */
	private $columns = [];
	
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
	 * Get a column from the table
	 * If the column requested doesn't exist, it will be created and added to the table's columns
	 * 
	 * @param string $name
	 * @return Column
	 */
	public function column($name)
	{
		if (!isset($this->columns[$name])) {
			$this->columns[$name] = new Column($name, $this);
		}
		return $this->columns[$name];
	}
	
	/**
	 * Create a new SelectQuery for the table
	 * 
	 * @return \Database\Query\SelectQuery
	 */
	public function select()
	{
		$query = new Query\SelectQuery($this->db());
		$query->from($this);
		
		return $query;
	}
	
	/**
	 * Find a single record from the table
	 * 
	 * @param mixed $where
	 * @param array $params
	 * @return array
	 */
	public function find($where, array $params = [])
	{
		$query = $this->select();
		$query->where($where);
		
		return $query->fetchRow($params);
	}
	
	/**
	 * Insert a row into the table and return the new row's primary key
	 * 
	 * @param array $row
	 * @return mixed
	 */
	public function insert(array $row)
	{
		$query = new Query\InsertQuery($this->db());
		$query->into($this);
		$query->addRow($row);
		
		return $query->execute();
	}
}