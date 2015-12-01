<?php
namespace Database\Relation;

use Database\Model\AbstractModel;

class OneToOneRelation extends AbstractRelation
{
	/**
	 * Assign results to local rows as a single item
	 * 
	 * @param string $assignAs The name of the array's key
	 * @param AbstractModel[] $foreignRows
	 * @param AbstractModel[] $localRows
	 */
	public function assignResults($assignAs, array $foreignRows, array $localRows) 
	{
		foreach ($localRows as $i => $localRow) {
			$found = null;
			
			foreach ($foreignRows as $foreignRow) {
				if ($this->rowsMatch($localRow, $foreignRow)) {
					$found = $foreignRow;
				}
			}
			
			if ($found !== null) {
				$localRow->set($assignAs, $found);
			}
		}
	}
}