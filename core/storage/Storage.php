<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core\storage;

use \ReflectionClass,
	framework\core\Resource,
	framework\core\exceptions\InvalidQueryException,
	framework\core\exceptions\InvalidStorageDriverException;

/**
 *
 */
class Storage {
	/**
	 * Singleton instance of the storage facade.
	 */
	private static $instance;

	/**
	 * Global properties not unique to any specific driver.
	 */
	private static $properties;

	/**
	 * The interface mediating between this, and the storage interface.
	 */
	private $driver;

	/**
	 * Initializes the storage driver once.
	 */
	private function __construct()
	{
		// @todo Once we migrate over to a configuration file, these exceptions will become 'Runtime'.
		if(!isset(self::$properties['provider']))
			throw new InvalidStorageDriverException("No available storage driver.");

		if(!class_exists(self::$properties['provider']))
			throw new InvalidStorageDriverException("No storage driver matching " . self::$properties['provider'] . ' was found.');

		$this->driver = new self::$properties['provider'](self::$properties);
	}

	/**
	 * Requests that the driver create a representation of the Resource.
	 * @return  boolean Whether the resource was created.
	 */
	public function create(Resource $resource) {
		$class = get_class($resource);
		$reflector = new ReflectionClass($class);

		$table = strtolower(substr($class, strrpos($class, "\\") + 1));
		$parameters = array();
		foreach($reflector->getProperties() as $parameter) {
			$method = "get" . ucfirst($parameter->name);
			$parameters[\framework\seperateCamelCase($parameter->name)] = $resource->$method();
		}

		try {
			$identifier = $this->driver->create($parameters, $table);
		} catch (InvalidQueryException $exception) {
			throw $exception;
		}

		$resource->setUid($identifier);
		return $resource;
	}

	/**
	 * Requests a Resource from the storage driver.
	 * @return array The data members that were previously saved.
	 */
	public function read(Resource $resource, $offset, $limit) {
		$class = get_class($resource);
		$reflector = new ReflectionClass($class);

		$table = strtolower(substr($class,  strrpos($class, "\\") + 1));
		$parameters = array();
		foreach($reflector->getProperties() as $parameter) {
			// Convert camel case to underscores.
			$parameters[] = \framework\seperateCamelCase($parameter->name);
		}
		$criteria = $resource->getRequiredEqualities();

		$results = array();
		try {
			$results = $this->driver->read($parameters, $table, $criteria, $offset, $limit);
		} catch (InvalidQueryException $exception) {
			throw $exception;
		}

		$objects = array();
		foreach($results as $result) {
			$object = $reflector->newInstance();
			foreach($result as $key => $value) {
				$property = $reflector->getProperty(\framework\camelCase($key));
				$property->setAccessible(true);
				$property->setValue($object, $value);
			}
			$objects[] = $object;
		}

		if(count($objects) == 1)
			return array_shift($objects);
		return $objects;
	}

	/**
	 * Requests that the driver update a specific Resource.
	 * @return boolean Whether the resource was updated.
	 */
	public function update(Resource $resource) {
		$class = get_class($resource);
		$reflector = new ReflectionClass($class);

		$table = strtolower(substr($class,  strrpos($class, "\\") + 1));
		$criteria = $resource->getRequiredEqualities();
		$populateCriteria = $criteria == null;

		$parameters = array();
		foreach($reflector->getProperties() as $parameter) {
			if($resource->isDirty($parameter->name)) {
				$parameters[$parameter->name] = $resource->{'get' . ucfirst(\framework\camelCase($parameter->name))}();
			} else if($populateCriteria) {
				$criteria[$parameter->name] = $resource->{'get' . ucfirst(\framework\camelCase($parameter->name))}();
			}
		}

		try {
			$this->driver->update($parameters, $table, $criteria);
		} catch (InvalidQueryException $exception) {
			throw $exception; # Move it up the line.
		}

		return true;
	}

	/**
	 * Requests that the driver remove a specific Resource.
	 * @return boolean Whether the resource was deleted.
	 */
	public function delete(Resource $resource) {
		$class = get_class($resource);
		$table = strtolower(substr($class,  strrpos($class, "\\") + 1));
		$criteria = $resource->getRequiredEqualities();

		try {
			$this->driver->delete($table, $criteria);
		} catch (InvalidQueryException $exception) {
			throw $exception; # Move it up the line.
		}

		return true;
	}

	/**
	 * Registers a global property for use by a driver.
	 */
	public static function configure($key, $value) {
		self::$properties[$key] = $value;
	}

	/**
	 * Returns a singleton instance if such one exists, otherwise attempts to create one.
	 */
	public static function getInstance() {
		return (self::$instance == null ? (self::$instance = new Storage()) : self::$instance);
	}

}