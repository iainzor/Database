<?php
namespace Database\Relation;

use Database\Model\AbstractModel;

class OneToManyRelation extends AbstractRelation
{
	/**
	 * Assign results to local rows as a rowset
	 * 
	 * @param string $assignAs The name of the array's key
	 * @param AbstractModel[] $foreignRows
	 * @param AbstractModel[] $localRows
	 */
	public function assignResults($assignAs, array $foreignRows, array $localRows) 
	{
		foreach ($localRows as $i => $localRow) {
			$collection = [];
			
			foreach ($foreignRows as $foreignRow) {
				if ($this->rowsMatch($localRow, $foreignRow)) {
					$collection[] = $foreignRow;
				}
			}
			
			$localRow->set($assignAs, $collection);
		}
	}
}