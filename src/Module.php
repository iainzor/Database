<?php
namespace Database;

use Bliss\Module\AbstractModule;

class Module extends AbstractModule
{
	/**
	 * @var Registry
	 */
	private $registry;
	
	public function init()
	{
		$this->registry = new Registry();
	}
	
	/**
	 * Set the connections available to the module
	 * 
	 * @param array $connections
	 */
	public function connections(array $connections) 
	{
		foreach ($connections as $name => $connection) {
			$this->registry->set($name, $connection);
		}
	}
	
	/**
	 * Get a connection to the database
	 * 
	 * @param string $name the name of the connection to retrieve.  Defaults to \Database\Config::DEFAULT_CONNECTION
	 * @return PDO
	 */
	public function connection($name = Config::DEFAULT_CONNECTION)
	{
		$this->app->log("Getting database connection for '{$name}'");
		
		return $this->registry->get($name);
	}
	
	/**
	 * Get or set the default database connection configuration
	 * 
	 * @param array $config
	 * @return array
	 */
	public function defaultConnection(array $config = null)
	{
		if ($config !== null) {
			$this->registry->defaultConnection($config);
		}
		return $this->registry->defaultConnection();
	}
}