<?php
namespace Database\Query\Traits;

use Database\Relation\RelationMap;

trait RelationTrait
{
	/**
	 * @var RelationMap
	 */
	private $relationMap;
	
	/**
	 * Get or set the query's relation map
	 * 
	 * @param RelationMap $map
	 * @return RelationMap
	 */
	public function relationMap(RelationMap $map = null)
	{
		if ($map !== null) {
			$this->relationMap = $map;
		}
		if (!$this->relationMap) {
			$this->relationMap = new RelationMap();
		}
		return $this->relationMap;
	}
	
	/**
	 * Add a new HasOne relationship to the query
	 * 
	 * @param string $name
	 * @param mixed $reference
	 * @param array|string $localKeys
	 * @param array|string $foreignKeys
	 * @return \Database\Relation\OneToOneRelation
	 */
	public function hasOne($name, $reference, $localKeys, $foreignKeys)
	{
		return $this->relationMap()->hasOne($name, $reference, $localKeys, $foreignKeys);
	}
	
	/**
	 * Add a new HasMany relationship to the query
	 * 
	 * @param string $name
	 * @param mixed $reference
	 * @param array|string $localKeys
	 * @param array|string $foreignKeys
	 * @return \Database\Relation\OneToManyRelation
	 */
	public function hasMany($name, $reference, $localKeys, $foreignKeys)
	{
		return $this->relationMap()->hasMany($name, $reference, $localKeys, $foreignKeys);
	}
}