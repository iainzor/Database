<?php
namespace Database\Relation;

use Database\Query\SelectQuery;

class OneToOneRelation extends AbstractRelation
{
	/**
	 * Assign results to local rows as a single item
	 * 
	 * @param string $assignAs The name of the array's key
	 * @param array $foreignRows
	 * @param array $localRows
	 */
	public function assignResults($assignAs, array $foreignRows, array &$localRows) 
	{
		foreach ($localRows as $i => $localRow) {
			foreach ($foreignRows as $foreignRow) {
				if ($this->rowsMatch($localRow, $foreignRow)) {
					$localRows[$i][$assignAs] = $foreignRow;
				}
			}
		}
	}
}