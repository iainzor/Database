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
	 * @param PDO|array $connection
	 */
	public function set($name, $connection)
	{
		$this->connections[$name] = $connection;
		
		if ($name === Config::DEFAULT_CONNECTION) {
			$this->defaultConnection = $connection;
		}
	}
	
	/**
	 * Get a connection by its name
	 * 
	 * @param string $id
	 * @return PDO
	 * @throws \Exception
	 */
	public function get($id)
	{
		if ($id === Config::DEFAULT_CONNECTION) {
			return $this->defaultConnection();
		}
		if (!isset($this->connections[$id])) {
			throw new \Exception("Unknown connection name '{$id}'");
		}
		return $this->_pdo($id, $this->connections[$id]);
	}
	
	/**
	 * Get or set the default connection for the registry
	 * If no default connection is set, the first available connection will be returned and set as the new default
	 * 
	 * @param PDO|array $connection
	 * @throws \Exception
	 * @return PDO
	 */
	public function defaultConnection($connection = null)
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
		$this->defaultConnection = $this->_pdo(Config::DEFAULT_CONNECTION, $this->defaultConnection);
		
		return $this->defaultConnection;
	}
	
	/**
	 * Generate a new PDO instance from a configuration array
	 * 
	 * @param string $name
	 * @param array|PDO $config
	 * @return PDO
	 * @throws \Exception
	 */
	private function _pdo($name, $config)
	{
		if (is_array($config)) {
			$options = array_replace([
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			], isset($config[Config::CONF_OPTIONS]) ? $config[Config::CONF_OPTIONS] : []);

			$config = array_merge([
				Config::CONF_USER	=> null,
				Config::CONF_PASSWORD => null
			], $config);

			if (!isset($config[Config::CONF_DSN])) {
				throw new \Exception("No DSN value provided in connection configuration");
			}

			$connection = new PDO($config[Config::CONF_DSN], $config[Config::CONF_USER], $config[Config::CONF_PASSWORD], $options);
			$this->connections[$name] = $connection;
		} else if ($config instanceof PDO) {
			$connection = $config;
		} else {
			throw new \Exception("Could not create PDO instance from the config");
		}
		
		return $connection;
	}
}