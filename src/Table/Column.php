<?php
namespace Database\Table;

class Column
{
	/**
	 * @var AbstractTable
	 */
	private $table;
	
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var string
	 */
	private $alias;

	/**
	 * @var ValueType\TypeInterface
	 */
	private $type;
	
	/**
	 * @var int
	 */
	private $length;
	
	/**
	 * @var boolean
	 */
	private $isPrimary = false;

	/**
	 * @var boolean
	 */
	private $autoIncrement = false;
	
	/**
	 * @var boolean
	 */
	private $allowNull = true;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param AbstractTable $table The table the column belongs to
	 */
	public function __construct($name = null, AbstractTable $table = null)
	{
		$this->name($name);
		$this->table($table);
	}
	
	/**
	 * Get or set the name of the column
	 * 
	 * @param string $name
	 * @return string
	 */
	public function name($name = null)
	{
		if ($name !== null) {
			$this->name = $name;
		}
		return $this->name;
	}
	
	/**
	 * Get or set the table the column belongs to
	 * 
	 * @param AbstractTable $table
	 * @return AbstractTable
	 */
	public function table(AbstractTable $table = null)
	{
		if ($table !== null) {
			$this->table = $table;
		}
		return $this->table;
	}
	
	/**
	 * Get or set the column's alias
	 * If no alias is provided, the alias will default to the column's name
	 * 
	 * @param string $alias
	 * @return string
	 */
	public function alias($alias = null)
	{
		if ($alias !== null) {
			$this->alias = $alias;
		}
		if (!$this->alias) {
			$this->alias = $this->name();
		}
		return $this->alias;
	}
	
	/**
	 * Get or set the column's value type
	 * 
	 * @param mixed $type
	 * @return ValueType\TypeInterface
	 * @throws \InvalidArgumentException
	 */
	public function type($type = null)
	{
		if ($type !== null) {
			$instance = $type;
			
			if (is_string($type)) {
				$instance = null;
				
				if (class_exists($type)) {
					$instance = new $type();
				} else {
					$className = __NAMESPACE__ ."\\ValueType\\". ucfirst($type);
					if (class_exists($className)) {
						$instance = new $className();
					}
				}
				
				if (!$instance) {
					throw new \InvalidArgumentException("Invalid value type: {$type}");
				}
			}
			
			if (!($instance instanceof ValueType\TypeInterface)) {
				$instance = new ValueType\Varchar();
			}
			
			$this->type = $instance;
		}
		
		return $this->type;
	}
	
	/**
	 * Get or set the length of the column
	 * 
	 * @param int $length
	 * @return int
	 */
	public function length($length = null)
	{
		if ($length !== null) {
			$this->length = (int) $length;
		}
		return $this->length;
	}
	
	/**
	 * Parse a value according to the column's type
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function parseValue($value) 
	{
		if ($this->type) {
			return $this->type->parseValue($value, $this->length);
		}
		return $value;
	}
	
	/**
	 * Create a new Column instance using an array of properties
	 * 
	 * @param array $properties
	 * @return Column
	 */
	public static function factory(array $properties)
	{
		$instance = new self();
		foreach ($properties as $name => $value) {
			call_user_func([$instance, $name], $value);
		}
		return $instance;
	}
}