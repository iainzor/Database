<?php
namespace Database\Relation;

use Database\Table\AbstractTable,
	Database\Query\SelectQuery,
	Database\Reference;

abstract class AbstractRelation
{
	/**
	 * @var RelationMap
	 */
	private $parentMap;
	
	/**
	 * @var Reference\ReferenceInterface
	 */
	protected $reference;
	
	/**
	 * @var AbstractTable
	 */
	protected $localTable;
	
	/**
	 * @var array
	 */
	protected $localKeys = [];
	
	/**
	 * @var array
	 */
	protected $foreignKeys = [];
	
	/**
	 * @var RelationMap
	 */
	protected $relationMap;
	
	/**
	 * Constructor
	 * 
	 * @param mixed $reference
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 * @param RelationMap $parentMap The RelationMap this relation belongs to
	 */
	public function __construct($reference, $localKeys, $foreignKeys, RelationMap $parentMap = null)
	{
		$this->parentMap = $parentMap;
		$this->relationMap = new RelationMap();
		$this->localKeys = is_array($localKeys) ? array_values($localKeys) : [$localKeys];
		$this->foreignKeys = is_array($foreignKeys) ? array_values($foreignKeys) : [$foreignKeys];
		
		$this->reference($reference);
		
		if (count($this->localKeys) !== count($this->foreignKeys)) {
			throw new \Exception("Local and foreign keys cannot be different lengths");
		}
	}
	
	/**
	 * Get or set the local table this relation is tied to
	 * 
	 * @param AbstractTable $table
	 * @return AbstractTable $table
	 */
	public function localTable(AbstractTable $table = null)
	{
		if ($table !== null) {
			$this->localTable = $table;
		}
		return $this->localTable;
	}
	
	/**
	 * Get or set the referenced object 
	 * When setting, the value can be a DB Table instance, DB name, a SelectQuery instance, or a ReferenceInterface instance
	 * 
	 * @param mixed $reference
	 * @return Reference\ReferenceInterface
	 */
	public function reference($reference = null)
	{
		if ($reference !== null) {
			$this->reference = $this->_parseReference($reference);
		}
		return $this->reference;
	}
	
	/**
	 * Parse a value to convert it into a referernce instance
	 * 
	 * @param mixed $reference
	 * @return \Database\Reference\ReferenceInterface
	 * @throws \UnexpectedValueException
	 */
	private function _parseReference($reference)
	{
		if (is_string($reference) || is_array($reference) || $reference instanceof AbstractTable) {
			$reference = new Reference\TableReference($reference);
		} else if ($reference instanceof SelectQuery) {
			$reference = new Reference\QueryReference($reference);
		}
		
		if (!($reference instanceof Reference\ReferenceInterface)) {
			throw new \UnexpectedValueException("Reference must be a string, array, AbstractTable, SelectQuery or an instance of ReferenceInterface");
		}
		
		return $reference;
	}
	
	/**
	 * Get or set the relation map for this relation
	 * 
	 * @param \Database\Relation\RelationMap $map
	 * @return RelationMap
	 */
	public function relationMap(RelationMap $map = null)
	{
		if ($map !== null) {
			$this->relationMap = $map;
		}
		return $this->relationMap;
	}
	
	/**
	 * Attempt to get a child relation to this one
	 * 
	 * @param string $name
	 * @return AbstractRelation
	 * @see RelationMap::relation()
	 */
	public function relation($name)
	{
		return $this->relationMap->relation($name);
	}
	
	/**
	 * Generate a new relation based on the configuration passed
	 * 
	 * @param array $config
	 * @return static
	 * @throws \Exception
	 */
	public static function generate(array $config)
	{
		if (!isset($config["reference"])) {
			throw new \Exception("No reference provided");
		}
		
		if (!isset($config["localKeys"], $config["foreignKeys"])) {
			throw new \Exception("Local and foreign keys are required for the relation");
		}
		
		$instance = new static($config["reference"], $config["localKeys"], $config["foreignKeys"]);
		
		if (isset($config["relationMap"])) {
			$instance->relationMap(
				RelationMap::generate($config["relationMap"])
			);
		}
		
		return $instance;
	}
	
	/**
	 * Find all related items matching a set of rows
	 * 
	 * @param array $rows
	 * @return array
	 */
	public function findAll(array $rows)
	{
		$params = [];
		
		foreach ($rows as $row) {
			foreach ($this->localKeys as $i => $localKey) {
				$foreignKey = $this->foreignKeys[$i];
				
				if (!empty($row[$localKey])) {
					$params[$foreignKey][] = $row[$localKey];
				}
			}
		}
		
		$results = [];
		if (count($params)) {
			$results = $this->reference->findAll($params);
		}
		
		return $results;
	}
	
	/**
	 * Check if a local and foreign row match based on the relation's local and foreign keys
	 * 
	 * @param array $localRow
	 * @param array $foreignRow
	 * @return boolean
	 */
	protected function rowsMatch(array $localRow, array $foreignRow)
	{
		$matched = 0;
		foreach ($this->localKeys as $i => $localKey) {
			$foreignKey = $this->foreignKeys[$i];
			
			if (isset($localRow[$localKey], $foreignRow[$foreignKey])) {
				$localValue = $localRow[$localKey];
				$foreignValue = $foreignRow[$foreignKey];
				
				if ($localValue === $foreignValue) {
					$matched++; 
				}
			}
		}
		
		return $matched === count($this->localKeys);
	}
	
	abstract public function assignResults($assignAs, array $foreignRows, array &$localRows);
}