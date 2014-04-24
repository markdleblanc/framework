<?php
/**
 * @copyright 2014 Mark LeBlanc
 * @author  Mark LeBlanc <mark@compute.systems>
 */
namespace framework\core;

use framework\core\storage\Storage,
	framework\core\exceptions\InvalidQueryException,
	framework\core\exceptions\InvalidResourceException;

/**
 *
 */
class Resource {
	/**
	 * [$requirements description]
	 * @var [type]
	 */
	private $equalities;

	private $modified;

	/**
	 *
	 */
	public function __construct() {

	}

	/**
	 * Builds the requested model.
	 * @param  [type] $namespace [description]
	 * @param  [type] $class     [description]
	 * @return [type]            [description]
	 */
	public static function build($class) {
		$qualified_class = "framework\\application\\entities\\$class";
		if(!class_exists($qualified_class))
			throw new InvalidResourceException("An internal error prevented the requested resource from being loaded.");
		return new $qualified_class();
	}

	/**
	 * The key:value pairs that must be matched.
	 * @return array The key:value pairs that must be present, and equal.
	 */
	public function getRequiredEqualities() {
		return $this->equalities;
	}

	public function dirty($name) {
		$this->modified[$name] = true;
	}

	public function isDirty($name) {
		return isset($this->modified[$name]);
	}

	public function clean($name) {
		unset($this->modified[$name]);
	}

	/**
	 * Ensures that the data retrieved contains a matching key:value pair.
	 * @param  string $key   Ensure the presence of a key in the database.
	 * @param  mixed $value Ensure that the key contains this value.
	 * @return Resource        An instance of the resource, for chaining.
	 */
	public function where($key, $value) {

		$this->equalities[\framework\seperateCamelCase($key)] = $value;
		return $this;
	}

	public function create() {
		try {
			return Storage::getInstance()->create($this);
		} catch (InvalidQueryException $exception) {
			throw $exception;
		}
	}

	public function find($offset = 0, $limit = 0) {
		try {
			return Storage::getInstance()->read($this, $offset, $limit);
		} catch (InvalidQueryException $exception) {
			throw $exception;
		}
	}

	public function update() {
		try {
			return Storage::getInstance()->update($this);
		} catch (InvalidQueryException $exception) {
			throw $exception;
		}
	}

	public function delete() {
		try {
			return Storage::getInstance()->delete($this);
		} catch (InvalidQueryException $exception) {
			throw $exception;
		}
	}
}