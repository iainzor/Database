<?php
namespace Database;

class Registry
{
	/**
	 * @var PDO[]
	 */
	private $connections = [];
	
	/**
	 * @var PDO
	 */
	private $defaultConnection;
	
	/**
	 * Set a PDO connection
	 * 
	 * @param string $name
	 * @param \Database\PDO $connection
	 */
	public function set($name, PDO $connection)
	{
		$this->connections[$name] = $connection;
		
		if ($name === Config::DEFAULT_CONNECTION) {
			$this->defaultConnection = $connection;
		}
	}
	
	/**
	 * Get a connection by its name
	 * 
	 * @param string $name
	 * @return PDO
	 * @throws \Exception
	 */
	public function get($name)
	{
		if ($name === Config::DEFAULT_CONNECTION) {
			return $this->defaultConnection();
		}
		if (!isset($this->connections[$name])) {
			throw new \Exception("Unknown connection name '{$name}'");
		}
		return $this->connections[$name];
	}
	
	/**
	 * Get or set the default connection for the registry
	 * If no default connection is set, the first available connection will be returned and set as the new default
	 * 
	 * @param \Database\PDO $connection
	 * @throws \Exception
	 */
	public function defaultConnection(PDO $connection = null)
	{
		if ($connection !== null) {
			$this->defaultConnection = $connection;
		}
		if (!$this->defaultConnection) {
			$keys = array_keys($this->connections);
			if (count($keys)) {
				$key = array_shift($keys);
				$this->defaultConnection = $this->connections[$key];
			} else {
				throw new \Exception("No connections have been set");
			}
		}
		return $this->defaultConnection;
	}
}