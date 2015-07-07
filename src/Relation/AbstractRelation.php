<?php
namespace Database\Relation;

use Database\Table\AbstractTable,
	Database\PDO,
	Database\Reference\ReferenceInterface;

abstract class AbstractRelation
{
	/**
	 * @var ReferenceInterface
	 */
	protected $reference;
	
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
	 * @param ReferenceInterface $reference
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 */
	public function __construct(ReferenceInterface $reference, $localKeys, $foreignKeys)
	{
		$this->relationMap = new RelationMap();
		$this->reference = $reference;
		$this->localKeys = is_array($localKeys) ? array_values($localKeys) : [$localKeys];
		$this->foreignKeys = is_array($foreignKeys) ? array_values($foreignKeys) : [$foreignKeys];
		
		if (count($this->localKeys) !== count($this->foreignKeys)) {
			throw new \Exception("Local and foreign keys cannot be different lengths");
		}
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
		if (!isset($config["reference"]) || !($config["reference"] instanceof ReferenceInterface)) {
			throw new \Exception("No valid reference provided");
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
		
		if (count($params)) {
			$results = $this->reference->findAll($params);
			return $this->relationMap->applyToRowset($results);
		}
		
		return [];
	}
}