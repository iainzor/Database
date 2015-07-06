<?php
namespace Database\Relation;

use Database\Query\SelectQuery;

class OneToOneRelation extends AbstractRelation
{
	public function find(array $data)
	{
		$query = new SelectQuery($this->db);
		$query->from($this->table);
		
		foreach ($this->localKeys as $i => $local) {
			if (!isset($data[$local])) {
				throw new \Exception("Local key not found in data: {$local}");
			}
			if (!isset($this->foreignKeys[$i])) {
				throw new \Exception("Local and foreign keys are different sizes");
			}
			$foreign = $this->foreignKeys[$i];
			$query->where([
				$foreign => $data[$local]
			]);
		}
		
		return $query->fetchRow();
	}
}