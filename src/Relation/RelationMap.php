<?php
namespace Database\Relation;

use Database\Table\AbstractTable,
	Database\Table\GenericTable,
	Database\Table\Structure;

class RelationMap
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * @var AbstractRelation[]
	 */
	private $relations = [];
	
	/**
	 * Constructor
	 * 
	 * @param AbstractTable $table
	 */
	public function __construct(AbstractTable $table = null)
	{
		$this->table($table);
	}
	
	/**
	 * Get or set the table the relation map is for
	 * 
	 * @param AbstractTable $table
	 * @return AbstractTable
	 */
	public function table(AbstractTable $table = null)
	{
		if ($table !== null) {
			$this->table = $table;
		}
		return $this->table;
	}
	
	/**
	 * Generate a new RelationMap from a set of relation configurations
	 * 
	 * @param array $relations
	 * @return RelationMap
	 */
	public static function generate(array $relations)
	{
		$instance = new self();
		foreach ($relations as $type => $items) {
			foreach ($items as $name => $config) {
				$instance->relation($name, self::_generate($type, $config));
			}
		}
		return $instance;
	}
	
	/**
	 * Generate a new relation 
	 * 
	 * @param string $type
	 * @param array $config
	 * @return AbstractRelation
	 * @throws \UnexpectedValueException
	 */
	private static function _generate($type, array $config)
	{
		switch (strtolower($type)) {
			case "hasone":
				return OneToOneRelation::generate($config);
			case "hasmany":
				return OneToManyRelation::generate($config);
			default:
				throw new \UnexpectedValueException("Unknown relation type: {$type}");
		}
	}
	
	/**
	 * Create a new one-to-one relationship
	 * 
	 * @param string $name
	 * @param mixed $reference
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 * @return OneToOneRelation
	 */
	public function hasOne($name, $reference, $localKeys, $foreignKeys)
	{
		$relation = new OneToOneRelation($reference, $localKeys, $foreignKeys);
		
		$this->relations[$name] = $relation;
		
		return $relation;
	}
	
	/**
	 * Create a new one-to-many relationship
	 * 
	 * @param string $name
	 * @param mixed $reference
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 * @return OneToOneRelation
	 */
	public function hasMany($name, $reference, $localKeys, $foreignKeys)
	{
		$relation =  new OneToManyRelation($reference, $localKeys, $foreignKeys);
		
		$this->relations[$name] = $relation;
		
		return $relation;
	}
	
	/**
	 * Get all available relations
	 * 
	 * @return AbstractRelation[]
	 */
	public function relations()
	{
		return $this->relations;
	}
	
	/**
	 * Get a relation by its name
	 * 
	 * @param string $name
	 * @return AbstractRelation
	 * @throws \Exception
	 */
	public function relation($name, AbstractRelation $relation = null)
	{
		if ($relation !== null) {
			$this->relations[$name] = $relation;
		}
		
		if (!isset($this->relations[$name])) {
			throw new \Exception("Relation by the name of '{$name}' could not be found");
		}
		
		return $this->relations[$name];
	}
	
	/**
	 * Check if a relation exists in the map
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function relationExists($name)
	{
		return isset($this->relations[$name]);
	}
	
	/**
	 * Apply the relation map to a set of rows
	 * 
	 * @param array $rows
	 * @return array
	 */
	public function applyToRowset(array $rows)
	{
		foreach ($this->relations as $name => $relation) {
			$results = $relation->findAll($rows);
			$relation->assignResults($name, $results, $rows);
		}
		
		return $rows;
	}
	
	/**
	 * Apply all relations to a single row
	 * 
	 * @param array $row
	 * @return array
	 */
	public function applyToRow(array $row)
	{
		$rows = $this->applyToRowset([$row]);
		return array_shift($rows);
	}
}