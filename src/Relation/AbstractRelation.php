<?php
namespace Database\Relation;

use Database\Table\AbstractTable,
	Database\PDO;

abstract class AbstractRelation
{
	/**
	 * @var PDO
	 */
	protected $db;
	
	/**
	 * @var \Database\Table\AbstractTable
	 */
	protected $table;
	
	/**
	 * @var array
	 */
	protected $localKeys = [];
	
	/**
	 * @var array
	 */
	protected $foreignKeys = [];
	
	/**
	 * Constructor
	 * 
	 * @param PDO $db
	 * @param AbstractTable $table
	 * @param string|array $localKeys
	 * @param string|array $foreignKeys
	 */
	public function __construct(PDO $db, AbstractTable $table, $localKeys, $foreignKeys)
	{
		$this->db = $db;
		$this->table = $table;
		$this->localKeys = is_array($localKeys) ? $localKeys : [$localKeys];
		$this->foreignKeys = is_array($foreignKeys) ? $foreignKeys : [$foreignKeys];
	}
	
	abstract public function find(array $data);
}