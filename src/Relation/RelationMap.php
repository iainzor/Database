<?php
namespace Database\Relation;

use Database\Table\AbstractTable;

class RelationMap
{
	/**
	 * @var \Database\Table\AbstractTable
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
	public function __construct(AbstractTable $table)
	{
		$this->table($table);
	}
	
	/**
	 * Get or set the base table
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
	 * Create a new one-to-one relationship
	 * 
	 * @param string $name
	 * @param AbstractTable $table
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 */
	public function hasOne($name, AbstractTable $table, $localKeys, $foreignKeys)
	{
		$relation = new OneToOneRelation($table, $localKeys, $foreignKeys);
		
		$this->relations[$name] = $relation;
	}
	
	/**
	 * Create a new one-to-many relationship
	 * 
	 * @param string $name
	 * @param AbstractTable $table
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 */
	public function hasMany($name, AbstractTable $table, $localKeys, $foreignKeys)
	{
		$relation =  new OneToManyRelation($table, $localKeys, $foreignKeys);
		
		$this->relations[$name] = $relation;
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
}