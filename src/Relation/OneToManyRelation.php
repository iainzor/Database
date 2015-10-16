<?php
namespace Database\Relation;

use Database\Query\SelectQuery;

class OneToManyRelation extends AbstractRelation
{
	/**
	 * Assign results to local rows as a rowset
	 * 
	 * @param string $assignAs The name of the array's key
	 * @param array $foreignRows
	 * @param array $localRows
	 */
	public function assignResults($assignAs, array $foreignRows, array &$localRows) 
	{
		foreach ($localRows as $i => $localRow) {
			$localRows[$i][$assignAs] = [];
			
			foreach ($foreignRows as $foreignRow) {
				if ($this->rowsMatch($localRow, $foreignRow)) {
					$localRows[$i][$assignAs][] = $foreignRow;
				}
			}
		}
	}
}