<?php
namespace Database\Query;

class UpdateQuery extends AbstractQuery 
{
	use Traits\WhereTrait,
		Traits\LimitTrait,
		Traits\OrderByTrait;
	
	/**
	 * @var array
	 */
	private $values = [];
	
	/**
	 * Get or set the values to update
	 * 
	 * @param array $values
	 * @return array
	 */
	public function values(array $values = null)
	{
		if ($values !== null) {
			$this->values = $values;
		}
		return $this->values;
	}
	
	/**
	 * Execute the UPDATE query
	 * 
	 * @return int The number of rows affected
	 */
	public function execute()
	{
		$factory = $this->db()->driverFactory();
		$generator = $factory->sqlGenerator();
		$sql = $generator->generate($this);
		
		return $this->db()->exec($sql);
	}
}