<?php
namespace Database\Relation;

use Database\Table\AbstractTable;

abstract class AbstractRelation
{
	/**
	 * @var \Database\Table\AbstractTable
	 */
	private $table;
	
	/**
	 * @var array
	 */
	private $localKeys = [];
	
	/**
	 * @var array
	 */
	private $foreignKeys = [];
	
	/**
	 * Constructor
	 * 
	 * @param AbstractTable $table
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 */
	public function __construct(AbstractTable $table, $localKeys, $foreignKeys)
	{
		$this->table = $table;
		$this->localKeys = is_array($localKeys) ? $localKeys : [$localKeys];
		$this->foreignKeys = is_array($foreignKeys) ? $foreignKeys : [$foreignKeys];
	}
}