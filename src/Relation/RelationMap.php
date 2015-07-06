<?php
namespace Database\Relation;

use Database\PDO,
	Database\Table\AbstractTable;

class RelationMap
{
	/**
	 * @var PDO
	 */
	private $db;
	
	/**
	 * @var AbstractRelation[]
	 */
	private $relations = [];
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
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
		$relation = new OneToOneRelation($this->db, $table, $localKeys, $foreignKeys);
		
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
		$relation =  new OneToManyRelation($this->db, $table, $localKeys, $foreignKeys);
		
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
	
	/**
	 * Apply all relations to a single result row
	 * 
	 * @param array $row
	 * @return array
	 */
	public function applyToRow(array $row)
	{
		foreach ($this->relations as $name => $relation) {
			$row[$name] = $relation->find($row);
		}
		
		return $row;
	}
}