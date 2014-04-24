<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

/**
 * Integrates namespace based autoload functionality.
 */
class DynamicClassLoader {
	/**
	 * Singleton instance of this object.
	 * @var DynamicClassLoader
	 */
	private static $instance;

	/**
	 * Aliased section of namespace that does not correspond to a directory structure.
	 * @var string
	 */
	private $alias;

	/**
	 * The parent directory, or working directory.
	 * @var string
	 */
	private $parent;

	/**
	 * Private constructor to prevent instantiation.
	 * @param  string $parent The parent, or working directory from which all files are loaded.
	 * @param  string $alias  Prefix appended to namespace that does not correspond with the directory structure.
	 */
	private function __construct($parent, $alias = null) {
		$this->parent = $parent;
		$this->alias = $alias;
	}

	private function autoload($namespace) {
		// Remove prefixed alias, if one exists.
		if(strpos($namespace, $this->alias) === 0)
			$namespace = substr($namespace, strlen($this->alias));

		$namespace = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
		$absolute_path = $this->parent . DIRECTORY_SEPARATOR . $namespace . '.php';

		if(is_readable($absolute_path))
		{
			return include($absolute_path);
		}

		return false;
	}

	/**
	 * Registers this DynamicClassLoader onto the standard php library autoloader stack.
	 * @param  string $parent The parent, or working directory from which all files are loaded.
	 * @param  string $alias  Prefix appended to namespace that does not correspond with the directory structure.
	 * @return boolean Whether this autoloader was registered.
	 */
	public static function register($parent, $alias = null) {
		self::$instance = new DynamicClassLoader($parent, $alias);
		return spl_autoload_register(array(self::$instance, 'autoload'));
	}

	/**
	 * Unregisters this DynamicClassLoader from the standard php library autoloader stack.
	 * @return boolean Whether this autoloader was unregistered.
	 */
	public static function unregister() {
		return spl_autoload_unregister(array(self::$instance, 'autoload'));
	}
}